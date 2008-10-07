<?php

function plugin__record_column_process($name,$value,$options)
{

	if($options['col_name'] == 'tables' && $options['table'] == 'cms_groups'){
		
		$q = $options['db']->query("SHOW TABLE STATUS",MYSQL_BOTH);
		$r = '<data>';
		
		$privA = array('browse','insert','update','delete');
		foreach($q as $table){
		
			if($table['Comment'] != 'private'){
				//
				$p = array();
				foreach($privA as $priv){
					if(isset($_REQUEST['table_' . $table['Name']. '_' . $priv])){
						if($_REQUEST['table_' . $table['Name']. '_' . $priv] == 'Y'){
							$p[] = $priv;
						}
					}
				}
				
				if(count($p)>0){
					$p = join(',',$p);
					$r .= '<table name="' . $table['Name'] . '">' . $p . '</table>';
				}
				
			}
		}
		
		$r .= '</data>';
		
		return array('field'=>'tables','value'=>$r);
	
	}
	
	if($options['col_name'] == 'groups' && $options['table'] == CMS_USERS_TABLE){
		
		$q = $options['db']->query("SELECT * FROM " . BLACKBIRD_TABLE_PREFIX . "groups");
		foreach($q as $group){
			if(isset($_REQUEST['group_' . $group['id']])){
				if($_REQUEST['group_' . $group['id']] == 'Y'){
					$r[] = $group['id'];
				}
			}
		}
	
		
		//trim last character;
		$r = join(',',$r);
		return array('field'=>'groups','value'=>$r);			
	
	}
	
	if($options['col_name'] == 'password' && $options['table'] == CMS_USERS_TABLE){
		
		if(strlen($value) > 1){
			return array('field'=>'password','value'=>sha1($value));			
		}else{
			return false;
		}
	}

}