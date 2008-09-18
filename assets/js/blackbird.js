Event.observe(window,'load',function(){
	blackbird = new blackbird();
});

var blackbirdCookie;
blackbirdCookie = new Object();

function blackbird()
{
	var groupA;
	var tables;
	if (tmp = this.readCookie('Blackbird')) blackbirdCookie = tmp.evalJSON();
	this.initToggleNavigation();
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