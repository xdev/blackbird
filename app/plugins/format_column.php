<?php

function plugin__format_column($col_name,$col_value,$table)
{
	$boolSet = array("admin");
	
	if(in_array($col_name,$boolSet)){
		if($col_value == 0){ return "false";}
		if($col_value == 1){ return "true";}
	}
	
	if($col_name == 'active'){
		if($col_value == 1){ return 'Active';}
		if($col_value == 0){ return 'Inactive';}
	}
	
	if($col_name == 'groups'){
		
		//split list
		$tA = explode(',',$col_value);
		$r = array();
		foreach($tA as $item){
			$q = AdaptorMysql::queryRow("SELECT name FROM ".BLACKBIRD_TABLE_PREFIX."groups WHERE id = '$item'");
			$r[] = $q['name'];
		}
		
		return join(', ',$r);
		
	}	
	
	if($col_name == 'user_id' && $table == BLACKBIRD_TABLE_PREFIX.'history'){
	
		$q = AdaptorMysql::queryRow("SELECT email FROM " . BLACKBIRD_USERS_TABLE . " WHERE id = '$col_value'");
		return $q['email'];
	
	}
	
	if(strlen($col_value) > 100){
		$data = substr($col_value,0,100) . "...";
		return strip_tags($data);
	}
	
	return $col_value;

}