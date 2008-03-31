/* $Id$ */

/**
*	ImageBrowser
*
*
*/

function ImageBrowser(options){

	this.data = new Object();
	for(var i in options){
		this.data[i] = options[i];
	}
	
	this.removeInterval = null;
	this.replaceId = null;
	
	this.listener = new Object();
	this.listener._scope = this;

	this.listener.onNew = function(bits)
	{
		this._scope.insertImage(bits);
	}
	
	CMS.broadcaster.addListener(this.listener);
	
}

/**
*	onRemoteComplete
*
*
*/

ImageBrowser.prototype.onRemoteComplete = function(obj)
{

	if(obj.mode == "insert"){
		this.getNewImage(obj.id,"insert");
	}
	if(obj.mode == "update"){
		this.getNewImage(obj.id,"update");
	}
	if(obj.mode == "delete"){
	
	}

}

/**
*	deleteImg
*
*
*/

ImageBrowser.prototype.deleteImg = function(id)
{
	
	var answer = confirm ("Really Delete?")
	if (answer) {
		
		var obj = this.data.name_space + '_img_' + id;
		
		this.tempitem = obj;		
		Effect.Fade(obj, 
			{
				duration: .5
				
			}
		);
		clearInterval(this.removeInterval);
		this.removeInterval = setInterval(this.removeItem.bind(this),500);
						
		var sendVars = new Object();
		
		
		sendVars.action = 'deleteRecord';
		sendVars.name_space = this.data.name_space;
		
		sendVars.table = this.data.table;
		sendVars.id = id;
		sendVars.parent_id = CMS.data.parent_id;
		
		
		var myAjax = new Ajax.Request(
			this.data.cms_root + 'ajax', 
			{
				method		: 'post', 
				parameters	: formatPost(sendVars)
			}
		);
	
	}



}

/**
*	removeItem
*
*
*/

ImageBrowser.prototype.removeItem = function()
{	
	clearInterval(this.removeInterval);
	Element.remove(this.tempitem);
	this.tempitem = undefined;
	this.updateLabel();		
}

/**
*	updateLabel
*
*
*/

ImageBrowser.prototype.updateLabel = function()
{
	
	var someNodeList = $(this.data.name_space + '_image_set').getElementsByTagName('li');
	var nodes = $A(someNodeList);
	
	var tA = $('pane_' + this.data.name_space).select('.right');
	var obj = tA[0];
	
	obj.innerHTML = nodes.length + ' Images(s)';
	
}

/**
*	editImg
*
*
*/

ImageBrowser.prototype.editImg = function(id)
{
	CMS.recordHandler(this.data.table,id,this.data.name_space,'edit',CMS.processEdit,'update');

}

/**
*	handleNew
*
*
*/

ImageBrowser.prototype.handleNew = function(bits)
{
	CMS.broadcaster.broadcastMessage("onNew",bits);
}

/**
*	handleUpdate
*
*
*/

ImageBrowser.prototype.handleUpdate = function(bits)
{
	var element = this.data.name_space + "_image_set";	
	var obj = this.data.name_space + '_img_' + this.last_id;
	Element.replace(obj,bits.responseText);
	
	var handler = this.onOrderChange;
	
	Sortable.create(
		element,
		{
			overlap		: "horizontal",
			constraint	: false,
			handle		: "handle",
     		onUpdate	: function()
     		{
				handler.bind(this);
			}
			
        }
	);
	
}

/**
*	getNewImage
*
*
*/

ImageBrowser.prototype.getNewImage = function(id,mode)
{
	var sendVars = new Object();
	
	sendVars.action = 'loadModule';
	sendVars.module = 'ImageBrowser';
	sendVars.remote_method = '_getImgDetail';
	
	sendVars.table = this.data.table;
	sendVars.id = id;
	
	sendVars.name_space = this.data.name_space;
	sendVars.table_parent = CMS.data.table_parent;
	
	var obj = this;
	
	this.last_id = id;
		
	if(mode == "insert"){
		var callback = this.handleNew.bind(this);
	}
	if(mode == "update"){
		var callback = this.handleUpdate.bind(this);
	}
	
	var myAjax = new Ajax.Request(
		this.data.cms_root + 'ajax',
		{
			method			: 'post', 
			parameters		: formatPost(sendVars),
			onComplete		: callback
		}
	);
	
}

/**
*	insertImage
*
*
*/

ImageBrowser.prototype.insertImage = function(bits)
{
	var element = this.data.name_space + "_image_set";	
	new Insertion.Bottom(element,bits.responseText);
	
	var tA = $(element).descendants();	
	var elem = tA[tA.length - 1];
	Effect.Appear(elem, {duration: .5});
	
	var handler = this.onOrderChange;
	
	Sortable.create(
		element,
		{
			overlap		: "horizontal",
			constraint	: false,
			handle		: "handle",
     		onUpdate	: function()
     		{
				handler.bind(this);
			}
			
        }
	);
	
	this.updateLabel();
}

/**
*	replaceImg
*
*
*/

ImageBrowser.prototype.replaceImg = function(bits)
{
	var obj = 'img_' + relacedId;
	Element.update(obj,bits.responseText);
}

/**
*	onOrderChange
*
*
*/

ImageBrowser.prototype.onOrderChange = function()
{
	var someNodeList = $(this.data.name_space  + '_image_set').getElementsByTagName('li');
	var nodes = $A(someNodeList);
	var order = new Array();
	
	nodes.each(function(node){
		var tA = node.id.split("_");
		order.push(tA[tA.length-1]);		
	});
		
	var sendVars = new Object();
	
	sendVars.action = 'loadModule';
	sendVars.table = this.data.table;
	sendVars.module = 'ImageBrowser';
	sendVars.name_space = this.data.name_space;
	sendVars.id_set = order.join(',');
	sendVars.table_parent = CMS.data.table_parent;
	sendVars.remote_method = 'sort_order';
	
	
	var myAjax = new Ajax.Request(
		this.data.cms_root + 'ajax',
		{
			method		: 'post', 
			parameters	: formatPost(sendVars)
		}
	);	

}