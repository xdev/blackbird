/**
*	duapDrop
*
*
*/

function dualDrop(options)
{
	this.src = options.src;
	this.name = options.name;
	this.holder = options.holder;
	this.data = options.dataA;
	
	var obj =  $(this.src);
	
	obj.controller = "dualdrop_" + this.name;
	obj.onchange = function()
	{	
		eval(this.controller).update();
	}
	
}

/**
*	update
*
*
*/

dualDrop.prototype.update = function(){
	
	var id = $(this.src).value;
		
	if(id == 'all'){
		this.fill(this.data);
	}else{
	
		var temp_OBJ = new Array();
		
		for(var i in this.data){
			if(this.data[i][1] == id){
				temp_OBJ.push(this.data[i]);
			}
		}
		this.fill(temp_OBJ);	
	}
}

/**
*	fill
*
*
*/

dualDrop.prototype.fill = function(data){

	var r = '';
	var r = '<select id="'+this.name+'" name="'+this.name+'" >';
	for(var i in data){
		if(data[i][2] != undefined){
			r += '<option value="' + data[i][0] + '">' + data[i][2] + '</option>';
		}
	}
		
	r += '</select>';
	var drop = $(this.holder);
	drop.innerHTML = r;

}