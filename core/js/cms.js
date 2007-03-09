/**
*	CMS
*
*
*/

function cms(options)
{
	this.data = new Object();
	for(var i in options){
		this.data[i] = options[i];
	}
	
	this.callbacks = new Object();	
	this.broadcaster = new EventBroadcaster();
	this.broadcaster.addListener(this);
	
	this.clickA = new Array();
	this.fadeInterval = undefined;
	
	//this design is weak sauce, get a new loader
	var myGlobalHandlers = {
		onCreate: function(){
			Element.show('ajax');
		},
		onComplete: function() {
			if(Ajax.activeRequestCount == 0){
				Element.hide('ajax');
			}
		}
	};
	
	Ajax.Responders.register(myGlobalHandlers);

}
var CMS = new cms();


/**
*	setProperty
*
*
*/

cms.prototype.setProperty = function(prop,value)
{
	this.data[prop] = value;
}

/**
*	getProperty
*
*
*/

cms.prototype.getProperty = function(prop)
{
	return this.data[prop];
}

/**
*	addCallback
*
*
*/

cms.prototype.addCallback = function(name_space,obj,method)
{
	this.callbacks[name_space] = { obj: obj, method: method }
}

/**
*	onRemoteComplete
*
*
*/

cms.prototype.onRemoteComplete = function(obj)
{
	if($('ajax')){
		Element.hide('ajax');
	}
	var listener = this.callbacks[obj.name_space].obj;
	var method = this.callbacks[obj.name_space].method;
	
	this.closeRecord(obj.name_space);
	
	if(listener[method]){
		listener[method].apply(listener,[obj]);
	}
}

/**
*	onRemoteErrors
*
*
*/

cms.prototype.onRemoteErrors = function(obj)
{
	if($('ajax')){
		Element.hide('ajax');
	}

	this.showTab(obj.name_space);

	for(var i in obj.errors){
		var elem = obj.name_space + '_' + obj.errors[i][0];
		
		var newobj = 'error_' + obj.name_space + '_' + obj.errors[i][0];
		
		if($(newobj)){
			//update interior content yo
			$(newobj).update(obj.errors[i][1]);
		}else{
			new Insertion.After($(elem), '<div id="' + newobj + '" class="error">' + obj.errors[i][1] + '</div>');
		}
			
		var label = $('form_' + obj.name_space).getElementsBySelector('label[for="' + elem + '"]');
		label[0].addClassName('error');
		label[0].style.color = '#CC3333';
	}
	
	if(obj.name_space != 'main'){
		//show form buttons
		var tA = document.getElementsByClassName('buttons','pane_' + this.name_space);
		var obj = tA[0];
		obj.show();
		//new Effect.Opacity(obj, {duration:0.5, from:0.2, to:1.0});
	}
	if(obj.name_space == 'main'){
		$('edit_buttons').show();	
	}
	
}


/**
*	onSubmit
*
*
*/

cms.prototype.onSubmit = function()
{
	if($('ajax')){
		Element.show('ajax');
	}
}

/**
*	submitRelated
*
*
*/

cms.prototype.submitRelated = function(name_space)
{
	if(this.validate(name_space)){
		var tA = document.getElementsByClassName('buttons','pane_' + this.name_space);
		var obj = tA[0];
		obj.hide();
		//new Effect.Opacity(obj, {duration:0.5, from:1.0, to:0.2});
	}		
}

/**
*	submitMain
*
*
*/

cms.prototype.submitMain = function(name_space)
{
	if(this.validate(name_space)){
		$('edit_buttons').hide();
	}else{
		this.showTab('main');
	}
}

/**
*	validate
*
*
*/

cms.prototype.validate = function(name_space)
{
	return validate($('form_' + name_space),name_space);
}

/**
*	addNewRecord
*
*
*/

cms.prototype.addNewRecord = function(table,name_space)
{

	this.recordHandler(table,'',name_space,'add',this.processAdd,'insert');
	this.broadcaster.broadcastMessage("onAddNew");
	
}

/**
*	editRecord
*
*
*/

cms.prototype.editRecord = function(table,id,name_space,elem)
{
	this.recordHandler(table,id,name_space,'edit',this.processEdit,'update');
}

/**
*	deleteRecord
*
*
*/

cms.prototype.deleteRecord = function(table,id,name_space)
{

	var answer = confirm ("Really Delete?")
	if (answer) {
		
		this.data.name_space = name_space;
		
		var sendVars = new Object();
		sendVars.name_space = name_space;
		sendVars.action = 'deleteRecord';
		sendVars.table = table;
		sendVars.id = id;
		
		var myAjax = new Ajax.Request(
			this.data.cms_root + 'ajax', 
			{
				method		: 'post', 
				parameters	: formatPost(sendVars),
				onComplete	: this.processDelete.bind(this)
			}
		);
	
	}
		
}

/**
*	processDelete
*
*
*/

cms.prototype.processDelete = function()
{
		
	var tA = document.getElementsByClassName('edit_form','pane_' + this.data.name_space);
	var obj = tA[0];
	
	if (obj.style.display == 'none') {
	
	}else{
		this.closeRecord(this.data.name_space);
	}
	
	//possible use of event broadcaster here
	
	//
	//cmsBroadcaster.broadcastMessage("onDelete");
	eval("data_grid_" + this.data.name_space + ".getUpdate();");
}

/**
*	recordHandler
*	central gateway for all datagrid requests
*
*/

cms.prototype.recordHandler = function(table,id,name_space,mode,handler,query_action)
{

	var sendVars = new Object();
			
	sendVars.query_action = query_action;
	sendVars.mode = query_action;
	sendVars.table = table;
	sendVars.id = id;
	sendVars.id_parent = this.data.id_parent;
	sendVars.action = 'editRecord';
	sendVars.name_space = name_space;
	sendVars.table_parent = this.data.table_parent;
	
	
	this.data.name_space = name_space;
	
	var tA = document.getElementsByClassName('detail','pane_' + this.data.name_space);
	var obj = tA[0];
	
	var _scope = this;
	
	var myAjax = new Ajax.Updater(
		obj,
		this.data.cms_root + 'ajax', 
		{
			method			: 'post', 
			parameters		: formatPost(sendVars),
			onComplete		: handler.bind(this),
			evalScripts 	: true
		}
	);
		
}

/**
*	processAdd
*
*
*/

cms.prototype.processAdd = function()
{
	this.openRecord(this.data.name_space);
}

/**
*	processEdit
*
*
*/

cms.prototype.processEdit = function()
{
	this.openRecord(this.data.name_space);
}

/**
*	openRecord
*
*
*/

cms.prototype.openRecord = function(name_space)
{
	
	var tA = document.getElementsByClassName('edit_form','pane_' + name_space);
	var obj = tA[0];
	if (obj.style.display == 'none') {
		Effect.SlideDown(obj, {duration: .5});
	}
	
	this.broadcaster.broadcastMessage("onOpen");
	
}

/**
*	closeRecord
*
*
*/

cms.prototype.closeRecord = function(name_space)
{
	
	var tA = document.getElementsByClassName('edit_form','pane_' + name_space);
	var obj = tA[0];
	Effect.SlideUp(obj, {duration: .5});
	
	this.broadcaster.broadcastMessage("onClose");

}

cms.prototype.closeMessage = function()
{
	$('message_content').remove();
}

/**
*	registerClick
*
*
*/

cms.prototype.registerClick = function(obj)
{
	
	var inset = false;
	for(var i=0;i<this.clickA.length;i++){
		if(this.clickA[i] == obj.id){
			if(obj.checked == false){
				this.clickA.splice(i,1);
			}
			inset = true;
			break;
		}			
	}
	if(inset == false){
		if(obj.checked == true){
			this.clickA.push(obj.id);
		}
	}
	if(this.clickA.length > 0){
		$('selection_set').innerHTML = 'With Seletected ' + this.clickA.length;
	}else{
		$('selection_set').innerHTML = 'With Selected';
	}
}

/**
*	checkAll
*
*
*/

cms.prototype.checkAll = function(mode)
{
	//get elements
	var itemA = document.getElementsByClassName("data_grid_checkbox");
	for(var i in itemA){
		var obj = itemA[i];
		obj.checked = mode;
		//register with click controller
		if(obj.id != undefined){
			this.registerClick(obj);
		}
	}
}

/**
*	toggleTabs
*
*
*/

cms.prototype.toggleTabs = function()
{
	var triggers = document.getElementsByClassName('trigger');
	for (i=0;i<triggers.length;i++) {
		//alert(triggers[i].id);
		toggle = triggers[i].id.replace('tab_','');
		//alert(toggle);
		Event.observe(triggers[i], 'click', function(){alert(toggle)});
	}
}

/**
*	showTab
*
*
*/

cms.prototype.showTab = function(tab)
{
	var tab_list = document.getElementsByClassName('trigger','edit_nav');
	for(var i=0;i<tab_list.length; i++){
		
		var name_space = tab_list[i].id.replace('tab_','');
		var item = $('pane_' + name_space);

		if(item){
			if(tab == name_space){
				$('tab_' + name_space).className += ' active';
				//clearInterval(this.fadeInterval);
				this.tab = item;
				//this.fadeInterval = setInterval(this.fadeIn.bind(this),1);
				this.tab.show();
			}else{
				//turn me off
				item.hide();
				//Effect.BlindUp(item, {duration: .25});
				//Effect.Fade($('footer'), {duration: .25});
				//Effect.Fade($('edit_buttons'), {duration: .25});
				$('tab_' + name_space).className = $('tab_' + name_space).className.replace(' active','');
			}
			
		}
	}
}

/**
*	fadeIn
*
*
*/

cms.prototype.fadeIn = function()
{
	
	//clearInterval(this.fadeInterval);
	
	//Effect.BlindDown(this.tab, {duration: .25});
	//Effect.Appear($('footer'), {duration: .25});
	//Effect.Appear($('edit_buttons'), {duration: .25});
}

/**
*	searchDataGrid
*
*
*/

cms.prototype.searchDataGrid = function(){
	if($('search').value != 'Search...'){
		window.document.forms['searchrec'].submit();
	}
}

/**
*	viewRows
*
*
*/

cms.prototype.viewRows = function(obj,url){
	
	window.location = url + '&sort_max=' + obj.value;
	
}

/**
*	setFilter
*
*
*/

cms.prototype.setFilter = function(obj,url){
	if(obj.value != ''){
		window.location = url + '&' + obj.id + '=' + obj.value;
	}else{
		window.location = url;
	}
}

/**
*	batchProcess
*
*
*/

cms.prototype.batchProcess = function(table)
{
	if(this.clickA.length > 0){
		if($('batchProcess').value != ''){
			var idA = new Array();
			for(var i=0;i<this.clickA.length;i++){
				idA.push(this.clickA[i].split("_")[1]);
			}
			window.location = this.data.cms_root + 'process/batch/' + table + '/?action='+ $('batchProcess').value + '&id_set=' + idA.join();
		}
	}else{
		$('batchProcess').value = '';
		alert('Select some rows first');
	}
	
}

/**
*	toggleHelp
*
*
*/

cms.prototype.toggleHelp = function()
{
	if ($('help')) {
		if ($('help').style.display == 'none') {
			Effect.SlideDown($('help'), {duration: .5});
		} else {
			Effect.SlideUp($('help'), {duration: .5});
		}
	} else {
		alert('Help documentation not found.');
	}
}


/**
*	loopBack
*
*
*/

cms.prototype.loopBack = function(name_space)
{
	$('loop_back').value = 'loop';
	if(validate($('form_'+name_space),name_space)){
		$('edit_buttons').hide();
	}else{
		this.showTab('main');
	}

	
}