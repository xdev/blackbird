// Image Browser

function ImageBrowser(options)
{

	this.data = new Object();
	for(var i in options){
		this.data[i] = options[i];
	}

	this.removeInterval = null;
	this.replaceId = null;

	this.createSortable();

	this.listener = new Object();
	this.listener._scope = this;

	this.listener.onNew = function(bits)
	{
		this._scope.insertImage(bits);
	}

	blackbird.broadcaster.addListener(this.listener);

}

ImageBrowser.prototype.onRemoteComplete = function(obj)
{
	switch(obj.query_action)
	{

		case 'insert':
			this.getNewImage(obj.id,"insert");
		break;

		case 'update':
			this.getNewImage(obj.id,"update");
		break;

		case 'delete':

		break;

	}

}

ImageBrowser.prototype.createSortable = function()
{
	Sortable.create(
		$(this.data.name_space + "_image_set"),
		{
			overlap		: "horizontal",
			constraint	: false,
			handle		: "handle",
			scroll		: window,
     		onUpdate	: this.onOrderChange.bind(this)
        }
	);
}

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


		sendVars.name_space = this.data.name_space;
		sendVars.table = this.data.table;
		sendVars.id = id;
		sendVars.parent_id = blackbird.data.parent_id;


		var myAjax = new Ajax.Request(
			this.data.base + 'imagebrowser/deleteimage',
			{
				method		: 'post',
				parameters	: formatPost(sendVars)
			}
		);

	}



}

ImageBrowser.prototype.removeItem = function()
{
	clearInterval(this.removeInterval);
	Element.remove(this.tempitem);
	this.tempitem = undefined;
	this.updateLabel();
}

ImageBrowser.prototype.updateLabel = function()
{

	var someNodeList = $(this.data.name_space + '_image_set').getElementsByTagName('li');
	var nodes = $A(someNodeList);

	var tA = $('section_' + this.data.name_space).select('.total');
	var obj = tA[0];
	var t = 'Images';
	if(nodes.length == 1){
		t = 'Image';
	}

	obj.innerHTML = nodes.length + ' ' + t;

}

ImageBrowser.prototype.editImg = function(id)
{
	if(this.data.fk_table){
		blackbird.recordHandler(this.data.fk_table,jQuery('#'+this.data.name_space+'_img_'+id).attr('rel').substring(5),this.data.name_space,'edit',blackbird.processEdit,'update');
	}else{
		blackbird.recordHandler(this.data.table,id,this.data.name_space,'edit',blackbird.processEdit,'update');
	}
}

ImageBrowser.prototype.handleNew = function(bits)
{
	blackbird.broadcaster.broadcastMessage("onNew",bits);
}

ImageBrowser.prototype.handleUpdate = function(bits)
{

	var obj = this.data.name_space + '_img_' + this.last_id;
	Element.replace(obj,bits.responseText);

	this.createSortable();

}

ImageBrowser.prototype.getNewImage = function(id,mode)
{
	var sendVars = new Object();

	sendVars.table = this.data.table;
	sendVars.id = id;

	sendVars.name_space = this.data.name_space;
	sendVars.table_parent = blackbird.data.table_parent;

	var obj = this;

	this.last_id = id;

	if(mode == "insert"){
		var callback = this.handleNew.bind(this);
	}
	if(mode == "update"){
		var callback = this.handleUpdate.bind(this);
	}

	var myAjax = new Ajax.Request(
		this.data.base + 'imagebrowser/getimage',
		{
			method			: 'post',
			parameters		: formatPost(sendVars),
			onComplete		: callback
		}
	);

}

ImageBrowser.prototype.insertImage = function(bits)
{
	var element = this.data.name_space + "_image_set";
	new Insertion.Bottom(element,bits.responseText);

	var tA = $(element).descendants();
	var elem = tA[tA.length - 1];
	Effect.Appear(elem, {duration: .5});

	this.updateLabel();
	this.createSortable();
	this.onOrderChange();

}

ImageBrowser.prototype.replaceImg = function(bits)
{
	var obj = 'img_' + relacedId;
	Element.update(obj,bits.responseText);
}

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

	sendVars.table = this.data.table;
	sendVars.name_space = this.data.name_space;
	sendVars.id_set = order.join(',');
	sendVars.table_parent = blackbird.data.table_parent;


	var myAjax = new Ajax.Request(
		this.data.base + 'imagebrowser/sort',
		{
			method		: 'post',
			parameters	: formatPost(sendVars)
		}
	);

}