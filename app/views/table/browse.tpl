<?php

//set up ajax data grid here son

//surrounding markup and suck yo



?>

<div id="mytable">
	
</div>

<script type="text/javascript">
	//set up ajax responder or something swag like that
	
	
	var sendVars = new Object();
	/*
	for(var i in this.data){
		sendVars[i] = this.data[i];
	}

	for(var i in this.filters){
		sendVars['filter_' + i] = this.filters[i];
	}
	*/
	
	sendVars.table = <?php print "'" . $this->route['table'] . "';" ?>;
	

	//var tA = $('pane_' + this.data.name_space).select('.data_grid_embed');
	
	
	var obj = $('mytable');
	
	var myAjax = new Ajax.Updater(
		obj,
		'<?= BASE ?>table/datagrid', 
		{
			method		: 'post', 
			parameters	: formatPost(sendVars),
			evalScript	: true
		}
	);
	
	//;//,
	
	
</script>