/* $Id$ */

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


//THESE ARE NOT USED MUCH

function displayElement(elem,display)
{
	$(elem).style.display = display;
}

function selectElement(elem,status)
{
	$(elem).checked = status;
}

function emptyForm(f){

	var tA = Form.getElements(f);
	var iMax = tA.length;
	for(i=0;i<iMax;i++){
		var obj = tA[i];
		if(obj.type == "text" || obj.nodeName == "TEXTAREA" || obj.nodeName == "SELECT" || obj.type == "file"){
			obj.value = '';	
		}
		
	}

}


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