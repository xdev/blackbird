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

/**
*	dirify
*
*
*/

function dirify(input)
{
	output = input.strip(); // strip whitespace
	output = output.gsub("[^a-zA-Z0-9 \_-]", ""); // only take alphanumerical characters, but keep the spaces too...
	output = output.gsub("[ ]+", "_", output); // replace spaces by underscores
	output = output.toLowerCase();  // make it lowercase
	return output;
};


/**
*	createSlug
*
*
*/

function createSlug(elem,source)
{
	var elem = $(elem);
	// If a value doesn't already exist, generate the slug
	if (!elem.value) {
		if (source) {
			var source = $(source);
		} else {
			var source = elem.up(1).previous(0).down(2);
		}
		Event.observe(source,'keyup', function()
		{
			elem.value = dirify(source.value);
		}, true);
		
	}
	Event.observe(elem,'change', function()
	{
		elem.value = dirify(elem.value);
	}, true);
};