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
	this.initToggleNavigation();
	this.initTabNavigation();
}

blackbird.prototype.setProperty = function(prop,value)
{
	this.data[prop] = value;
};

blackbird.prototype.getProperty = function(prop)
{
	return this.data[prop];
};

//Table Navigation
blackbird.prototype.initToggleNavigation = function()
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
	if($('bb_main_sections')){
		var tA = $('bb_main_sections').select('a');
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
	this.showTab(t);
};

blackbird.prototype.showTab = function(tab)
{
	var tA = $('bb_main_sections').select('a');
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
};

/**
*	addNewRecord
*
*
*/

blackbird.prototype.addNewRecord = function(table,name_space)
{

	this.recordHandler(table,'',name_space,'add',this.processAdd,'insert');
	//this.broadcaster.broadcastMessage("onAddNew");
	
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
	var tA = $('section_' + name_space).select('.edit_form');
		
	var obj = $(tA[0]);
	if (obj.style.display == 'none') {
		//Effect.SlideDown(obj, {duration: .5});
	}
	obj.show();
	
	//this.broadcaster.broadcastMessage("onOpen");
	//hide the datagrid for this section ehh
	var tA = $('section_' + name_space).select('.table');
	var obj = tA[0];
	obj.hide();
	
};

/**
*	closeRecord
*
*
*/

blackbird.prototype.closeRecord = function(name_space)
{
	
	var tA = $('section_' + name_space).select('.edit_form');
	var obj = tA[0];
	//Effect.SlideUp(obj, {duration: .5});
	obj.hide();
	
	//this.broadcaster.broadcastMessage("onClose");
	//show the datagrid for this section ehh
	var tA = $('section_' + name_space).select('.table');
	var obj = tA[0];
	obj.show();

};

/**
*	recordHandler
*	central gateway for all datagrid requests
*
*/

blackbird.prototype.recordHandler = function(table,id,name_space,mode,handler,query_action)
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
	
	var tA = $('section_' + this.data.name_space).select('.detail');
	
	var obj = $(tA[0]);

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
