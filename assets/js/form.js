/**
*	formController
*
*
*/

function formController(form)
{
	this.form = form;
	this.data_delta = [];
	this.data_alpha = [];

	var tA = Form.getElements(this.form);
	var iMax = tA.length;
	for(i=0;i<iMax;i++){
		var obj = tA[i];
		//if(obj.nodeName == "INPUT" || obj.nodeName == "TEXTAREA" || obj.nodeName == "SELECT"){
			//if(obj.hasClassName('noparse')){
			//}else{
				this.data_alpha.push( [obj.id,obj.value] );
				var c = this;
				Event.observe(obj,'focus',function()
				{
					c.focus(this);
				});
				Event.observe(obj,'blur',function()
				{
					c.blur(this);
				});
				obj.onchange = function(){
					c.change(this);
				};
			//}
		//}
	}
}

/**
*	focus
*
*
*/

formController.prototype.focus = function(obj){
	form_item = obj.up('.form_item');
	form_item.addClassName('active');
};

/**
*	blur
*
*
*/

formController.prototype.blur = function(obj){
	form_item = obj.up('.form_item');
	form_item.removeClassName('active');
};

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

	//this should be broadcast instead
	this.updateStatus(obj,status);

};

/**
*	updateStatus
*
*
*/

formController.prototype.updateStatus = function(obj,status)
{
	if (obj != 'reset') {
		var form_item = obj.up('.form_item');
		if (status == 1) form_item.addClassName('changed');
		if (status == 0) form_item.removeClassName('changed');
	}

	if(this.data_delta != undefined){
		if(this.data_delta.length > 0){
			//$('changes').innerHTML = data_delta.length + " Changes";
		}else{
			//$('changes').innerHTML = "";
		}
	}

};

/**
*	reset
*
*
*/

formController.prototype.reset = function()
{
	for(var i in this.data_delta){
		var label = getElementsByAttribute($(this.form), "label", "for",this.data_delta[i][0]);
		label[0].style.background = "#CCCCCC";
	}

	delete this.data_delta;
	this.data_delta = [];
	this.updateStatus('reset');

	Form.reset(this.form);

};

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
};