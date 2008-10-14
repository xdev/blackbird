var blackbirdCookie;
blackbirdCookie = new Object();

document.observe('dom:loaded',function(){
	blackbird = new blackbird();
});

function blackbird(options)
{
	this.data = new Object();
	for(var i in options){
		this.data[i] = options[i];
	}
	
	var groupA;
	var tables;
	var lastSection;
	var tab;
	
	if (tmp = this.readCookie('Blackbird')) blackbirdCookie = tmp.evalJSON();
	this.initTableNavigation();
	this.initTabNavigation();
	
	this.callbacks = new Object();
	this.broadcaster = new EventBroadcaster();
	this.broadcaster.addListener(this);
	
	this.windowSize();
	Event.observe(window,'resize',this.windowSize.bind(this));
	
	var tA = $('body').select('.lightbox');
	for(var i=0;i<tA.length;i++){
		Event.observe(tA[i],'click',this.openLightbox.bind(this));		
	}
	
	var tA = $('body').select('.titlebar a.toggle');
	for(var i=0;i<tA.length;i++){
		Event.observe(tA[i],'click',this.toggleDashItem.bind(this));		
	}
	
}

blackbird.prototype.windowSize = function()
{
	body = $('body');
	bodyWidth = body.getWidth();
	if (bodyWidth < '820') {
		body.addClassName('small');
		body.removeClassName('medium');
		body.removeClassName('large');
	}
	else if (bodyWidth < '1460') {
		body.addClassName('medium');
		body.removeClassName('small');
		body.removeClassName('large');
	}
	else {
		body.addClassName('large');
		body.removeClassName('small');
		body.removeClassName('medium');
	}
};

blackbird.prototype.checkForChanges = function(ns)
{
	if(ns !== undefined){
		var tA = [{id:'form_target_'+ns}];
	}else{
		var tA = $('body').select('iframe');	
	}
	var changesA = [];
	for(var i=0;i<tA.length;i++){
		var name_space = tA[i].id.substr(12);
		var obj = eval('window.formController_' + name_space);
		if(obj !== undefined){
			if(obj.getLength()){
				changesA.push({name_space:name_space,changes:obj.getLength()});
			}
		}
	}
	
	if(changesA.length > 0){
		return changesA;
	}
	
	return false;
};

blackbird.prototype.onFormUpdate = function(obj)
{
	//trim off form_
	var name_space = obj.form.substr(5);
	
	var mode = $('form_' + name_space).select('input[name="query_action"]')[0].value;
	
	if (mode == 'update') {
		var form_item = obj.elem.up('.form_item');
		if (obj.status == 1) form_item.addClassName('changed');
		if (obj.status == 0) form_item.removeClassName('changed');
	}
	
	if(obj.length > 0){
		$('section_' + name_space).select('.revert')[0].show();
		$('section_' + name_space).select('.button_submit')[0].enable();
	}else{
		$('section_' + name_space).select('.revert')[0].hide();
		$('section_' + name_space).select('.button_submit')[0].disable();
	}
};

blackbird.prototype.onFormReset = function(form)
{
	
	//clean up form items, remove changed and error status classes
		
	var tA = $(form).select('.changed');
	var iMax = tA.length;
	for(i=0;i<iMax;i++){
		var obj = tA[i];		
		obj.removeClassName('changed');
	}
	
	var tA = $(form).select('.failed');
	var iMax = tA.length;
	for(i=0;i<iMax;i++){
		var obj = tA[i];		
		obj.removeClassName('failed');
		var elem = obj.select('.error_item')[0];
		elem.remove();
	}
		
	var name_space = form.substr(5);
	$('section_' + name_space).select('.revert')[0].hide();
	$('section_' + name_space).select('.button_submit')[0].disable();
};

blackbird.prototype.toggleDashItem = function(e)
{
	var elem = Event.element(e);
	
	
	var targ = elem.up().up().select('.content')[0];
	if(targ.style.display == 'none'){
		elem.up().up().removeClassName('closed');
		Effect.BlindDown(targ,{duration: .5});
	}else{
		elem.up().up().addClassName('closed');
		Effect.BlindUp(targ,{duration: .5});
	}
};

blackbird.prototype.promptLogout = function()
{
	var changesA = this.checkForChanges();
	var r = '';
	if(changesA){
		r += 'You have unsaved changes!';
	}
	
	var answer = confirm('Logout?\n' + r);
	if(answer){
		window.location = this.data.base + 'user/logout';
	}
}

blackbird.prototype.handleDelete = function(table,id,name_space)
{
	if(name_space == 'main'){
		window.location = this.data.base + 'table/browse/' + table + '?message=delete&id=' + id;
	}else{
		this.closeRecord(name_space,false);
		//reset the form
		$('form_'+name_space).reset();
		//set up callbacks because we want only the correct object to update oye!
		this.broadcaster.broadcastMessage("onUpdate");		
	}
	
}

blackbird.prototype.promptDeleteRecord = function(table,id,name_space)
{
	var answer = confirm('Delete Record?');
	if(answer){
		
		var sendVars = {
			table:table,
			id:id
		}
		
		//do the deleting via background ajax - but need to be able to handle errors if they occur
		var myAjax = new Ajax.Request(
			this.data.base + 'record/delete',
			{
				method:'post',
				parameters:formatPost(sendVars),
				onComplete:this.handleDelete.bind(this,table,id,name_space)
			}
		);
	}
}

blackbird.prototype.openLightbox = function(e)
{

	var elem = Event.element(e);
	
	$('body').insert({bottom: '<div id="lightbox" style="display: none;"><div class="wrapper"><div class="dialog"><h1>Loading...</h1></div></div></div>'});
	Effect.Appear($('lightbox'),{duration: .25});
	
	sendVars = {};
		
	var url = elem.hash.substring(1);
	
	var myAjax = new Ajax.Updater(
		$('lightbox').select('div.dialog')[0],
		url, 
		{
			method      : 'post',
			parameters  : formatPost(sendVars),
			evalScripts : true,
			onComplete  : function(){
				if (close = $('closeLightbox')) {
					Event.observe(close,'click',blackbird.closeLightbox);
					Event.observe(document.body, 'click', function(event) {
					  if (Event.findElement(event).id == 'lightbox') blackbird.closeLightbox();
					});
					Event.observe(document,'keypress',function(event){
						if(event.keyCode == Event.KEY_ESC){
							blackbird.closeLightbox();
						}
					});
				}
			}
		}
	);
	
	
	
};

blackbird.prototype.closeLightbox = function()
{
	Effect.Fade($('lightbox'),{duration: .25});
	
	//clean up event listeners
};

blackbird.prototype.logout = function()
{
	var obj = $('lightbox').select('div.dialog')[0];
	var myAjax = new Ajax.Updater(
		obj,
		this.data.base + 'user/processlogout', 
		{
			method			: 'post',
			evalScripts 	: true
		}
	);
};

blackbird.prototype.destroyRecord = function(table,id)
{
	var obj = $('lightbox').select('div.dialog')[0];
	var myAjax = new Ajax.Updater(
		obj,
		this.data.base + 'record/processdelete/' + table + '/' + id, 
		{
			method			: 'post',
			evalScripts 	: true
		}
	);
};

blackbird.prototype.setProperty = function(prop,value)
{
	this.data[prop] = value;
};

blackbird.prototype.getProperty = function(prop)
{
	return this.data[prop];
};

/*

Method: addCallback

Adds a callback obj to a namespace to handle AJAX events

Parameters:

	name_space - name space
	obj - object reference
	method - method name

*/

blackbird.prototype.addCallback = function(name_space,obj,method)
{
	this.callbacks[name_space] = { obj: obj, method: method };
};

//Table Navigation
blackbird.prototype.initTableNavigation = function()
{
	if (elem = $('bb_main_nav_tables')) {
		groupA = elem.childElements();
		for (i=0,il=groupA.length;i<il;i++) {
			groupA[i].id = 'table_group_'+i;
			if (blackbirdCookie[groupA[i].id]) groupA[i].addClassName('open');
			tables = groupA[i].down('ul');
			if (groupA[i].hasClassName('open')) {
				if (!tables.visible()) {
					tables.show();
				}
			} else {
				if (tables.visible()) {
					tables.hide();
				}
			}
			Event.observe(groupA[i].down('span'),'click',this.toggleNavigation);
		}
		if (expand = $('bb_nav_action_expand')) {
			Event.observe(expand,'click',function(){
				for (i=0,il=groupA.length;i<il;i++) {
					if (!groupA[i].hasClassName('open')) {
						Effect.BlindDown(groupA[i].down('ul'),{duration: .2});
						groupA[i].addClassName('open');
					}
				}
				blackbird.setNavigationCookie();
			});
		}
		if (collapse = $('bb_nav_action_collapse')) {
			Event.observe(collapse,'click',function(){
				for (i=0,il=groupA.length;i<il;i++) {
					if (groupA[i].hasClassName('open')) {
						Effect.BlindUp(groupA[i].down('ul'),{duration: .2});
						groupA[i].removeClassName('open');
					}
				}
				blackbird.setNavigationCookie();
			});
		}
	}
};

blackbird.prototype.toggleNavigation = function()
{
	group = this.up();
	if (!group.hasClassName('active')) {
		tables = group.down('ul');
		if (group.hasClassName('open')) {
			group.addClassName('active');
			Effect.BlindUp(tables,{duration: .2});
			setTimeout(function()
			{
				group.removeClassName('active');
			},300);
			group.removeClassName('open');
		} else {
			group.addClassName('active');
			Effect.BlindDown(tables,{duration: .2});
			setTimeout(function()
			{
				group.removeClassName('active');
			},300);
			group.addClassName('open');
		}
		setTimeout(function()
		{
			blackbird.setNavigationCookie();
		},300);
	}
};

blackbird.prototype.setNavigationCookie = function()
{
	for (i=0,il=groupA.length;i<il;i++) {
		if (groupA[i].hasClassName('open')) blackbirdCookie[groupA[i].id] = 1;
		else blackbirdCookie[groupA[i].id] = 0;
	}
	this.createCookie('Blackbird',Object.toJSON(blackbirdCookie).replace(/\s+/g,''),365);
	//if (blackbirdCookie == 'undefined') this.eraseCookie('Blackbird');
};


//Edit page tab behavior
blackbird.prototype.initTabNavigation = function()
{
	if($('bb_toggle_sections')){
		var tA = $('bb_toggle_sections').select('a');
		var iMax = tA.length;
		for(var i=0;i<iMax; i++){
			Event.observe(tA[i],'click',this.handleTabClick.bind(this));
		}
	}
};

blackbird.prototype.handleTabClick = function(event)
{
	var elem = Event.element(event);	
	var t = elem.hash.substring(1);
	
	//check for changes
	var changesA = this.checkForChanges();
	if(changesA){
		//loop that 
		var r = '';
		for(var i=0;i<changesA.length;i++){
			r += changesA[i].changes + ' changes in the ' + changesA[i].name_space + ' section\n';
		}
		var answer = confirm('You Have Unsaved Changes...\n' + r);
		if(answer){
			this.showTab(t);
		}
	}else{	
		this.showTab(t);
	}
};

blackbird.prototype.showTab = function(tab)
{
	if($('bb_toggle_sections')){
		var tA = $('bb_toggle_sections').select('a');
		var iMax = tA.length;
		for(var i=0;i<iMax; i++){
		
			var nav = tA[i];
			var name_space = nav.hash.substring(1);
			var item = $('section_' + name_space);

			if(item){
				if(tab == name_space){
					nav.addClassName('selected');
					///this.tab = item;
					//this.tab.show();
					item.show();
				}else{
					item.hide();
					nav.removeClassName('selected');
				}
			
			}
		}
	
		/*
		if(tab != this.lastSection){
			//check for old formController
			var cont = eval('formController_' + tab);
			if(cont != undefined){
				if(cont.getLength() > 0){
					alert('there are unsaved changes');
				}	
			}
		}
		*/
	
		this.lastSection = tab;
	
	}
};

/**
*	handleErrors
*
*
*/

blackbird.prototype.handleErrors = function(obj,name_space)
{
	//need to remove class from items that already have it and are good now
	var tA = $('form_'+name_space).select('.failed');
	var iMax = tA.length;
	for(i=0;i<iMax;i++){
		var item = tA[i];		
		item.removeClassName('failed');
		var elem = item.select('.error_item')[0].remove();
	}
		
	//handle new errors
	var t = '';
	var iMax = obj.length;
	for(var i=0;i<iMax;i++){
		//build message message
		t += obj[i].message + '\n';
		//add error class to form item
		var form_item = obj[i].field.up('.form_item');
		form_item.addClassName('failed');
		//insert error content
		var newobj = 'error_' + obj[i].field;
		
		if($(newobj)){
			//update interior content yo
			$(newobj).update(obj[i].message);
		}else{
			new Insertion.After($(obj[i].field), '<div id="' + newobj + '" class="error_item">' + obj[i].message + '</div>');
		}
	}
	
	alert(t.stripHTML());
	
	//focus first field
	obj[0].field.focus();
		
	//display error dialog
};

//http://www.geekpedia.com/code20_Strip-HTML-using-JavaScript.html
String.prototype.stripHTML = function()
{
	var matchTag = /<(?:.|\s)*?>/g;
	return this.replace(matchTag, "");
};

/*

Method: onRemoteComplete

Fires when remote operations are complete and closes record container

Parameters:

	obj - object reference

*/

blackbird.prototype.onRemoteComplete = function(obj)
{
	if($('ajax')){
		$('ajax').hide();
	}
	//var listener = this.callbacks[obj.name_space].obj;
	//var method = this.callbacks[obj.name_space].method;
	
	//create message div or something
	
	if(obj.channel == 'related'){	
		this.closeRecord(obj.name_space,false);
		//reset the form
		$('form_'+obj.name_space).reset();
		this.broadcaster.broadcastMessage("onUpdate");
	}
	
	if(obj.channel == 'main'){
		window.location = this.data.base + 'record/edit/' + obj.table + '/' + obj.id + '?message=' + obj.mode;
		//should reload form for simplicity
	}
	
	//if(listener[method]){
	//	listener[method].apply(listener,[obj]);
	//}
};

/*

Method:	onRemoteErrors

Handles errors from remote script operations

Parameters:

	obj - object reference

*/

blackbird.prototype.onRemoteErrors = function(obj)
{
	if($('ajax')){
		$('ajax').hide();
	}
	
	var tA = $('form_' + obj.name_space).select('.failed');
	var iMax = tA.length;
	for(i=0;i<iMax;i++){
		var item = tA[i];		
		item.removeClassName('failed');
		var elem = item.select('.error_item')[0];
		elem.remove();
	}
	
	this.showTab(obj.name_space);
	var t = '';
	for(var i in obj.errors){
		t += obj.errors[i].message + '\n';
		var elem = obj.name_space + '_' + obj.errors[i].field;
		
		var newobj = 'error_' + obj.name_space + '_' + obj.errors[i].field;
		
		if($(newobj)){
			//update interior content yo
			$(newobj).update(obj.errors[i].message);
		}else{
			new Insertion.After($(elem), '<div id="' + newobj + '" class="error_item">' + obj.errors[i].message + '</div>');
		}
		
		var form_item = $(obj.name_space + '_' + obj.errors[i].field);
		form_item = form_item.up('.form_item');
		form_item.addClassName('failed');
	}
	
	//unblock browser
	
	if(obj.name_space != 'main'){
		//show form buttons
		//var tA = $('section_' + obj.name_space).select('.buttons');
		//var obj = tA[0];
		//obj.show();
		//new Effect.Opacity(obj, {duration:0.5, from:0.2, to:1.0});
	}
	if(obj.name_space == 'main'){
		//$('edit_buttons').show();	
	}
	
	//alert it up
	alert(t.stripHTML());
	//focus first element?	
	$(obj.name_space + '_' + obj.errors[0].field).focus();
	
};

blackbird.prototype.deleteRecord = function(table,id)
{
	var answer = confirm('Really Delete?');
	if(answer){
		//process delete
	}
};

/**
*	onSubmit
*
*
*/

blackbird.prototype.onSubmit = function()
{
	if($('ajax')){
		$('ajax').show();
	}
};

/**
*	submitRelated
*
*
*/

blackbird.prototype.submitRelated = function(name_space)
{	
	if($('active_' + name_space)){
		$(name_space + '_active').value = $('active_' + name_space).value;
	}
	var errorsA = this.validate(name_space);
	if(errorsA == true){
		this.broadcaster.broadcastMessage("onSubmit");
		//$('pane_' + name_space).select('.buttons')[0].hide();
		
	}
	if(errorsA.length > 0){
		this.handleErrors(errorsA,name_space);
	}
};

/**
*	submitMain
*
*
*/

blackbird.prototype.submitMain = function(name_space)
{
	this.showTab(name_space);
	/*
	if($('active_' + name_space)){
		$(name_space + '_active').value = $('active_' + name_space).value;
	}
	*/
	var tA = this.validate(name_space);
	if(tA == true){
		//$('edit_buttons').hide();
	}
	if(tA.length > 0){
		this.showTab(name_space);
		this.handleErrors(tA,name_space);
	}
};

/**
*	validate
*
*
*/

blackbird.prototype.validate = function(name_space)
{
	return validate($('form_' + name_space),name_space);
};

/**
*	addNewRecord
*
*
*/

blackbird.prototype.addNewRecord = function(table,name_space)
{

	this.recordHandler(table,'',name_space,'add',this.processAdd,'insert');
	this.broadcaster.broadcastMessage("onAddNew");
	
};

/**
*	editRecord
*
*
*/

blackbird.prototype.editRecord = function(table,id,name_space,elem)
{
	this.recordHandler(table,id,name_space,'edit',this.processEdit,'update');
};

/**
*	processAdd
*
*
*/

blackbird.prototype.processAdd = function()
{
	this.openRecord(this.data.name_space);
};

/**
*	processEdit
*
*
*/

blackbird.prototype.processEdit = function()
{
	this.openRecord(this.data.name_space);
};

/**
*	openRecord
*
*
*/

blackbird.prototype.openRecord = function(name_space)
{
	var obj = $('section_' + name_space).select('.edit_form')[0];
	if (obj.style.display == 'none') {
		//Effect.SlideDown(obj, {duration: .5});
	}
	obj.show();
	
	this.broadcaster.broadcastMessage("onOpen");
	//hide the datagrid for this section ehh
	obj = $('section_' + name_space).select('.browse')[0];
	obj.hide();
	
};

blackbird.prototype.closeMain = function(url)
{
	var name_space = 'main';
	
	var changesA = this.checkForChanges(name_space);
	var close = true;
	if(changesA){
		//loop that 
		var r = '';
		for(var i=0;i<changesA.length;i++){
			r += changesA[i].changes + ' changes in the ' + changesA[i].name_space + ' section\n';
		}
		var answer = confirm('You Have Unsaved Changes...\n' + r);
		if(answer){
			//reset the form son
			$('form_'+name_space).reset();
		}else{
			close = false;
		}
	}
	
	if(close == true){
		window.location = url;
	}
};

/**
*	closeRecord
*
*
*/

blackbird.prototype.closeRecord = function(name_space,check)
{
	
	var close = true;
	if(check){
		var changesA = this.checkForChanges(name_space);
		if(changesA){
			//loop that 
			var r = '';
			for(var i=0;i<changesA.length;i++){
				r += changesA[i].changes + ' changes in the ' + changesA[i].name_space + ' section\n';
			}
			var answer = confirm('You Have Unsaved Changes...\n' + r);
			if(answer){
				//reset the form son
				$('form_'+name_space).reset();
			}else{
				close = false;
			}
		}
	}
	
	if(close == true){
		
		var obj = $('section_' + name_space).select('.edit_form')[0];
		//Effect.SlideUp(obj, {duration: .5});
		obj.hide();

		this.broadcaster.broadcastMessage("onClose");
		//show the datagrid for this section ehh
		obj = $('section_' + name_space).select('.browse')[0];
		obj.show();
		
	}
};

/**
*	recordHandler
*	central gateway for all datagrid requests
*
*/

blackbird.prototype.recordHandler = function(table,id,name_space,mode,handler,query_action)
{

	var sendVars = new Object({
		query_action:query_action,
		mode:mode,
		table:table,
		id:id,
		id_parent:this.data.id_parent,
		action:'editRecord',
		name_space:name_space,
		table_parent:this.data.table_parent
	});
	
	this.data.name_space = name_space;

	var obj = $('section_' + this.data.name_space).select('.detail')[0];
	var _scope = this;
	
	var myAjax = new Ajax.Updater(
		obj,
		this.data.base + 'record/editrelated', 
		{
			method			: 'post', 
			parameters		: formatPost(sendVars),
			onComplete		: handler.bind(this),
			evalScripts 	: true
		}
	);
		
};

/*
Cookies
*/
// http://www.quirksmode.org/js/cookies.html
blackbird.prototype.createCookie = function(name,value,days)
{
	if (days) {
		var date = new Date();
		date.setTime(date.getTime()+(days*24*60*60*1000));
		var expires = "; expires="+date.toGMTString();
	}
	else var expires = "";
	document.cookie = name+"="+value+expires+"; path=/";
};

blackbird.prototype.readCookie = function(name)
{
	var nameEQ = name + "=";
	var ca = document.cookie.split(';');
	for(var i=0;i < ca.length;i++) {
		var c = ca[i];
		while (c.charAt(0)==' ') c = c.substring(1,c.length);
		if (c.indexOf(nameEQ) == 0) return c.substring(nameEQ.length,c.length);
	}
	return null;
};

blackbird.prototype.eraseCookie = function(name)
{
	this.createCookie(name,"",-1);
};
