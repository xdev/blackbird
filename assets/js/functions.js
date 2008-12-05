// Clear text field default value
// Original script: http://www.scriptygoddess.com/archives/2005/11/15/clear-default-text-onclick-restore-if-nothing-entered/
function clickclear(thisfield, defaulttext) {
		if (thisfield.value == defaulttext) {
		thisfield.value = "";
	}
}

// Recall text field default value
// Original script: http://www.scriptygoddess.com/archives/2005/11/15/clear-default-text-onclick-restore-if-nothing-entered/
function clickrecall(thisfield, defaulttext) {
	if (thisfield.value == "") {
		thisfield.value = defaulttext;
	}
}

//could add some encoding/escaping, used for formatting ajax post data
function formatPost(obj){
	var ret = "";
	var c = 0;
	for(var i in obj){
		if(c > 0){
			ret += "&";
		}		
		ret += i + "=" + obj[i];
		c++;
	}
	return ret;
}

/*
Cookies
*/
// http://www.quirksmode.org/js/cookies.html
function createCookie(name,value,days)
{
	if (days) {
		var date = new Date();
		date.setTime(date.getTime()+(days*24*60*60*1000));
		var expires = "; expires="+date.toGMTString();
	}
	else var expires = "";
	document.cookie = name+"="+value+expires+"; path=/";
}

function readCookie(name)
{
	var nameEQ = name + "=";
	var ca = document.cookie.split(';');
	for(var i=0;i < ca.length;i++) {
		var c = ca[i];
		while (c.charAt(0)==' ') c = c.substring(1,c.length);
		if (c.indexOf(nameEQ) == 0) return c.substring(nameEQ.length,c.length);
	}
	return null;
}

function eraseCookie(name)
{
	this.createCookie(name,"",-1);
}