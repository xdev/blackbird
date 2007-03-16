/**
*	dataGrid
*
*
*/

function dataGrid(options)
{
	this.data = new Object();
	
	for(var i in options){
		this.data[i] = options[i];
	}
	
	this.filters = new Object();
	this.tr = undefined;
	
	this.listener = new Object();
	this.listener._scope = this;
	this.listener.onClose = function()
	{
		this._scope.clearHilite();
	}
	this.listener.onAddNew = function()
	{
		this._scope.clearHilite();
	}
	
	this.listener.onComplete = function()
	{
		this._scope.getUpdate();
	}
	
}

/**
*	clearHilite
*
*
*/

dataGrid.prototype.clearHilite = function()
{
	if($(this.tr)){
		$(this.tr).removeClassName('active');
	}
}

/**
*	setFilter
*
*
*/

dataGrid.prototype.setFilter = function(prop,obj)
{
	this.filters[prop] = obj.value;
	this.getUpdate();
}

/**
*	setProperty
*
*
*/

dataGrid.prototype.setProperty = function(prop,value)
{
	this.data[prop] = value;
	this.getUpdate();
}

/**
*	sortColumn
*
*
*/

dataGrid.prototype.sortColumn = function(col,dir)
{
	this.data.sort_col = col;
	this.data.sort_dir = dir;
	this.getUpdate();
}

/**
*	search
*
*
*/

dataGrid.prototype.search = function(obj)
{
	if($(this.data.name_space + '_search').value != 'Search...'){
		this.data.search = $(this.data.name_space + '_search').value;
		this.getUpdate();
	}
}

/**
*	doSearch
*
*
*/

dataGrid.prototype.doSearch = function(obj)
{
	window.clearInterval(obj.interval);
	obj.interval = null;
	obj.data.search = $(obj.data.name_space + '_search').value;
	obj.getUpdate();
}

/**
*	editRecord
*
*
*/

dataGrid.prototype.editRecord = function(id,elem)
{
		
	//var p = elem.parentNode
	var p = $(elem).up('tr');
	$(p).addClassName('active');
	
	CMS.recordHandler(this.data.table,id,this.data.name_space,'edit',CMS.processEdit,'update');
	
	this.clearHilite();
	
	this.tr = p;
	
}

/**
*	getUpdate
*
*
*/

dataGrid.prototype.getUpdate = function()
{
	var sendVars = new Object();
		
	for(var i in this.data){
		sendVars[i] = this.data[i];
	}
	
	for(var i in this.filters){
		
		sendVars['filter_' + i] = this.filters[i];
	}
	
	sendVars.action = 'getDataGrid';
		
	var tA = document.getElementsByClassName('data_grid_embed',$('pane_' + this.data.name_space));
	var obj = tA[0];
			
	var myAjax = new Ajax.Updater(
		obj,
		this.data.cms_root + 'ajax', 
		{
			method		: 'post', 
			parameters	: formatPost(sendVars),
			evalScript	: true
		}
	);


}

/**
*	reset
*
*
*/

dataGrid.prototype.reset = function()
{
	this.data.sort_col = 'id';
	this.data.sort_dir = 'DESC';
	this.data.sort_index = '0';
	this.data.search = '';
	this.filters = new Object();
	this.getUpdate();
}