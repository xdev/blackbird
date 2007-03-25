function validate(form,name_space)
{
	//get elements by class name
	var elementList = document.getElementsByClassName('validate', $(form));
	var errorA = new Array();
	
	for(var i=0;i<elementList.length;i++){
		
		var elem = elementList[i];
		
		if(name_space != undefined){
			var tA = elem.name.split(name_space + '_');
			var elem_name = tA[1];
		}else{
			var elem_name = elem.name;
		}
		
		if(elem.hasClassName('email')){
			var strng = elem.value;
			var emsg="";
			if (strng == "") {
			   emsg = "You didn't enter an email address.\n";
			}
		
			var emailFilter=/^.+@.+\..{2,3}$/;
			if (!(emailFilter.test(strng))) { 
			   emsg = "Please enter a valid email address.\n";
			}
			else {
			//test email for illegal characters
			   var illegalChars= /[\(\)\<\>\,\;\:\\\"\[\]]/
				 if (strng.match(illegalChars)) {
				  emsg = "The email address contains illegal characters.\n";
			   }
			}
			errorA.push({field: elem,message: emsg});
		}
		
		if(elem.hasClassName('numeric')){
			
			if (isNaN(parseInt(elem.value))) {
				var emsg = 'Please enter a number for ' + elem_name + '\n';
				errorA.push({field: elem,message: emsg});
			}else{
			}
		}
				
		
		if(elem.hasClassName('default')){
			
			if(elem.value == ''){
				var emsg = 'Please enter a value for ' + elem_name + '\n';
				errorA.push({field: elem,message: emsg});
			}
		
		}
		
		
	}
	
	
	if(errorA.length > 0){
		return errorA;
	}else{
		$(form).submit();
		return true;
	}
	
}

