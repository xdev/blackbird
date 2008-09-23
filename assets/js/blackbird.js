Event.observe(window,'load',function(){
	blackbird = new blackbird();
});

var blackbirdCookie;
blackbirdCookie = new Object();

function blackbird()
{
	var groupA;
	var tables;
	var lastSection;
	var tab;
	if (tmp = this.readCookie('Blackbird')) blackbirdCookie = tmp.evalJSON();
	this.initToggleNavigation();
	this.initTabNavigation();
}

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

blackbird.prototype.handleTabClick = function(event)
{
	var elem = Event.element(event);	
	var t = elem.hash.substring(1);
	this.showTab(t);
}

blackbird.prototype.initTabNavigation = function()
{
	var tA = $('bb_main_sections').select('a');
	var iMax = tA.length;
	for(var i=0;i<iMax; i++){
		Event.observe(tA[i],'click',this.handleTabClick.bind(this));
	}
	
	//loop
	
	//observe click yoz
}

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
}


