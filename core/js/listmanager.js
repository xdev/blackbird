/**
*	listManager
*
*
*/

listManager = function(options)
{
	this.data = new Object();
	for(var i in options){
		this.data[i] = options[i];
	}

	if(this.data.label_name == undefined){
		this.data.label_name = 'Name';
	}
	if(this.data.label_value == undefined){
		this.data.label_value = 'Value';
	}
	
	this.controller = 'listManager_' + this.data.name;
	this.ul = 'pairset_' + this.data.name;
}

/**
*	editItem
*
*
*/

listManager.prototype.editItem = function(element)
{
	if ($(element).down().tagName == 'PRE') {
		$(element).innerHTML = '<textarea onblur="' + this.controller + '.saveItem(this);" cols="20" rows="3">'+ $(element).down().innerHTML +'</textarea>';
		$(element).down().focus();
	}
}

/**
*	saveItem
*
*
*/

listManager.prototype.saveItem = function(element)
{
	if ($(element).tagName == 'TEXTAREA') {
		$(element).up().innerHTML = '<pre>'+ $(element).value +'</pre>';
		this.update();
	}
}

/**
*	update
*
*
*/

listManager.prototype.update = function()
{
	var rA = new Array();
	
	if($(this.ul).hasChildNodes()){
		var m = $(this.ul).childNodes.length;
		for(var i=0;i<m;i++){
			var itemA = document.getElementsByClassName("input",$(this.ul).childNodes[i]);
			if(this.data.mode == 'pair'){
				rA.push(itemA[0].down().innerHTML + "*_*" + itemA[1].down().innerHTML);
			}
			if(this.data.mode == 'single'){
				rA.push(itemA[0].down().innerHTML);
			}
		}
		$(this.data.name).value = rA.join("+_+");
	}else{
		$(this.data.name).value = '';
	}
}

/**
*	deleteItem
*
*
*/

listManager.prototype.deleteItem = function(element)
{
	Element.remove($(element).up());
	this.update();
}

/**
*	addItem
*
*
*/

listManager.prototype.addItem = function()
{
	var index = ($(this.ul).childNodes.length) + 1;
	
	if(this.data.mode == 'pair'){
		var html = '<li id="' + this.data.name + '_' + index + '" class="pair"><div class="handle"></div>';
		html += '<label>' + this.data.label_name + '</label>';
		html += '<div class="input" onclick="' + this.controller + '.editItem(this);"><pre>(none)</pre></div>';
		html += '<label>' + this.data.label_value + '</label>';
		html += '<div class="input" onclick="' + this.controller + '.editItem(this);"><pre>(none)</pre></div>';
	}
	
	if(this.data.mode == 'images'){
		var html = '<li id="' + this.data.name + '_' + index + '" class="pair images"><div class="handle"></div>';
		html += '<label>' + this.data.label_name + '</label>';
		html += '<div class="input" onclick="' + this.controller + '.editItem(this);"><pre>(none)</pre></div>';
		html += '<label>Image</label>';
		html += '<input class="img" id="' + this.data.name + '_' + index + '_img" name="' + this.data.name + '_' + index + '_img" type="file" />';
	}
	
	if(this.data.mode == 'single'){
		var html = '<li id="' + this.data.name + '_' + index + '" class="pair single"><div class="handle"></div>';
		html += '<label>' + this.data.label_name + '</label>';
		html += '<div class="input" onclick="' + this.controller + '.editItem(this);"><pre>(none)</pre></div>';
	}
	
	
	html += '<a class="icon delete" style="float: left;" href="#" onclick="' + this.controller + '.deleteItem(this); return false;">Delete</a>';
	html += '</li>';
	
	new Insertion.Bottom(this.ul,html);
	Sortable.create(this.ul,{constraint:"vertical",onUpdate:this.update.bind(this)});
}