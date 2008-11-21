<?php

function plugin__record_column_process($name,$value,$options)
{

	if($options['col_name'] == 'tables' && $options['table'] == BLACKBIRD_TABLE_PREFIX . 'groups'){
		
		$q = $options['db']->query("SHOW TABLE STATUS");
		$privA = array('select','insert','update','delete');
		
		$tableA = array();
		//loop her and throw out system tables
		$tlen = strlen(BLACKBIRD_TABLE_PREFIX);
		foreach($q as $table){
			//if pattern fails add to list
			if(substr($table['Name'],0,$tlen) != BLACKBIRD_TABLE_PREFIX){
				$tableA[] = $table['Name'];
			}
		}
		//get proper id
		$group_id = $options['id'];
		//query all existing permissions for this group
		$q_permissions = $options['db']->query("SELECT * FROM " . BLACKBIRD_TABLE_PREFIX . "permissions WHERE group_id = '$group_id' ORDER BY table_name");
		
		//handle bulk update query? or not
		
		foreach($tableA as $table){
			//use checkArray on each table_name
			$row_data = array();
			$row_data[] = array('field'=>'table_name','value'=>$table);
			foreach($privA as $priv){
				if(isset($_REQUEST['table_' . $table . '_' . $priv])){
					if($_REQUEST['table_' . $table . '_' . $priv] == 'Y'){
						$row_data[] = array('field'=>$priv.'_priv','value'=>1);
					}else{
						$row_data[] = array('field'=>$priv.'_priv','value'=>0);
					}
				}else{
					$row_data[] = array('field'=>$priv.'_priv','value'=>0);
				}
			}		
			
			$tA = Utils::checkArray($q_permissions,array('table_name'=>$table));
			if(is_array($tA)){
				//do updates
				$options['db']->update(BLACKBIRD_TABLE_PREFIX . 'permissions',$row_data,'id',$group_id);
			}else{
				//do inserts
				$row_data[] = array('field'=>'group_id','value'=>$group_id);
				$options['db']->insert(BLACKBIRD_TABLE_PREFIX . 'permissions',$row_data);
			}
			
		}
		
	
		
		/*
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
		*/
	
	}
	
	if($options['col_name'] == 'groups' && $options['table'] == BLACKBIRD_USERS_TABLE){
		
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
	
	if($options['col_name'] == 'password' && $options['table'] == BLACKBIRD_USERS_TABLE){
		
		if(strlen($value) > 1){
			return array('field'=>'password','value'=>sha1($value));			
		}else{
			return false;
		}
	}

}