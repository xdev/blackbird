/* $Id$ */

/**
*	formController
*
*
*/

function formController(name_space)
{
	this.name_space = name_space;
	this.data_delta = [];
	this.data_alpha = [];
	
	var tA = Form.getElements('form_' + name_space);
		
	for(i=0;i<tA.length;i++){
	
		var obj = tA[i];
		if(obj.nodeName == "INPUT" || obj.nodeName == "TEXTAREA" || obj.nodeName == "SELECT"){
			if(obj.hasClassName('noparse')){
			}else{
				this.data_alpha.push( [obj.id,obj.value] );
				var controller = this.name_space;
				obj.onchange = function(){
					var t = eval('formController_'+controller);
					t.change(this);
				}				
			}
		}				
		
	}
}

/**
*	change
*
*
*/

formController.prototype.change = function(obj){
	var status = 1;
		
	for(var i in this.data_alpha){
					
		if(this.data_alpha[i][0] == obj.id){				
					
			if(this.data_alpha[i][1] == obj.value){
									
				for(var j in this.data_delta){
					if(this.data_delta[j][0] == obj.id){
						this.data_delta.splice(j,1);
					}
				}
				
				status = 0;
				
			}else{					
			
				var create = true;
				if(this.data_delta.length > 0){
					for(var j in this.data_delta){
					
						if(this.data_delta[j][0] == obj.id){
							this.data_delta[j][1] = obj.value;
							create = false;
						}
					}
				}					
				
				if(create){
					this.data_delta.push( [obj.id, obj.value] );
				}
			}	
		}		
	}
	
	this.updateStatus(obj,status);

}

/**
*	updateStatus
*
*
*/

formController.prototype.updateStatus = function(obj,status)
{

	if(obj != 'reset'){
		var label = $('form_' + this.name_space).getElementsBySelector('label[for="' + obj.id + '"]');
		
		if(status == 1){			
			label[0].style.background = "#E6E8ED";//FFFF33
		}
		if(status == 0){
			label[0].style.background = "none";
		}
	}
			
	if(this.data_delta != undefined){
		if(this.data_delta.length > 0){
			//$('changes').innerHTML = data_delta.length + " Changes";
		}else{
			//$('changes').innerHTML = "";
		}
	}

}

/**
*	reset
*
*
*/

formController.prototype.reset = function()
{
	for(var i in this.data_delta){
			
		var label = getElementsByAttribute($('form_' + this.name_space), "label", "for",this.data_delta[i][0]);
		label[0].style.background = "#CCCCCC";
		
	}
	
	delete this.data_delta;
	this.data_delta = [];
	this.updateStatus('reset');
	
	Form.reset('form_' + this.name_space);
	
}

/**
*	getLength
*
*
*/

formController.prototype.getLength = function()
{
	if(this.data_delta != undefined){
		if(this.data_delta.length > 0){
			return this.data_delta.length;
		}		
	}
	
	return 0;
}

/**
*	addChangeHandler
*
*
*/

formController.prototype.addChangeHandler = function(elem,handler)
{
	var controller = this.change;
	$(elem).onchange = function(){
		controller.bind(this);
		eval(handler)(this);
	}
}