/**
*	EventBroadcaster
*
*
*/

function EventBroadcaster(){
	this.listeners = [];
}

/**
*	addListener
*
*
*/

EventBroadcaster.prototype.addListener = function(obj)
{
	var in_list = false;
	
	for(var i in this.listeners){
		var listener = this.listeners[i];
		if (obj == listener){
			in_list = true;
		}
	}
	
	if(!in_list){
		this.listeners.push(obj);
	}
}

/**
*	removeListener
*
*
*/

EventBroadcaster.prototype.removeListener = function(obj)
{
	for(var i in this.listeners){
		var listener = this.listeners[i];
		if (obj == listener){
			this.listeners.splice(i,1);
		}
	}
}

/**
*	broadcastMessage
*
*
*/

EventBroadcaster.prototype.broadcastMessage = function(method)
{
	var args = [];	
	
	for(var i=0;i<arguments.length;i++){
		args.push(arguments[i]);
	}
	args.shift();
		
	for(var i in this.listeners){
		var listener = this.listeners[i];
		
		if(listener[method]){
			listener[method].apply(listener,args);
		}
	
	}
}