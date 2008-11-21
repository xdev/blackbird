<?php

function plugin__record_column_edit($name,$value,$options)
{
	if($options['col_name'] == 'password' && $options['table'] == BLACKBIRD_USERS_TABLE){
		$options['type'] = 'password';
		Forms::text($name,'',$options);		
	}
	
	if($options['col_name'] == 'user_id' && $options['table'] == BLACKBIRD_TABLE_PREFIX.'history'){
		$q = $options['db']->query("SELECT email FROM " . BLACKBIRD_USERS_TABLE . " WHERE id = '$value'");
		Forms::readonly($name,$q['email'],$options);		
	}
	
	if($options['col_name'] == 'groups' && $options['table'] == BLACKBIRD_USERS_TABLE){
		
		$q = $options['db']->query("SELECT id,name FROM ".BLACKBIRD_TABLE_PREFIX."groups ORDER BY name");
		$r = '<ul>';
		$tA = explode(',',$value);
		
		foreach($q as $group){
			(in_array($group['id'] ,$tA) ) ? $v = 'Y' : $v = '';
			$r .= '<li>' . Forms::checkboxBasic('group_' . $group['id'],$v,array('class'=>'checkbox noparse','label'=>$group['name'])) . '</li>';
		}
		
		$r .= '</ul>';
		$options['label'] = "Groups";
		Forms::buildElement($name,$r,$options);
		Forms::hidden($name,'',array('omit_id'=>true));
	
	}
	
	if($options['col_name'] == 'tables' && $options['table'] == BLACKBIRD_TABLE_PREFIX.'groups'){
		
		$q = $options['db']->query("SHOW TABLE STATUS");
		$tA = explode(',',$value);
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
		
		//
		$tA = _ControllerFront::getRoute();		
		$group_id = $tA['id'];
		
		$q_permissions = $options['db']->query("SELECT * FROM " . BLACKBIRD_TABLE_PREFIX . "permissions WHERE group_id = '$group_id' ORDER BY table_name");
		
		/*
		$xml = simplexml_load_string($value);
		$valueA = array();
		if($xml){
			foreach($xml->table as $mytable){
				$t = sprintf($mytable['name']);
				$valueA[$t] = sprintf($mytable);
			}
		}
		*/
		
		$r = '<input type="button" id="matrix_on" value="ON" /><input type="button" id="matrix_off" value="OFF" /><input type="button" id="matrix_toggle" value="TOGGLE" />';
		
		$r .= '<table id="matrix">
		<tr><th>Table</th>';
		
			foreach($privA as $priv){
				
				$r .= '<th>' . $priv . '</th>';
				
			}
		$r .= '</tr>';
		
		$r .= '<tr><th></th>';
		
		foreach($privA as $priv){
			$r .= '<th><input type="button" title="'.$priv.'" class="checktoggle column" value="col" /></th>';
		}
		
		$r .= '</tr>';
		
					
		foreach($tableA as $table){
			
		//used to rely upon a private comment to hide, no longer, just don't show any blackbird tables here
			$r .= '<tr>';
			$r .= '<td><input type="button" title="' . $table . '" class="checktoggle row" value="row" />' .  Utils::formatHumanReadable($table) . '</td>';
			
			/*
			$tP = array();
			if(isset($valueA[$table])){
				$tP = explode(',',$valueA[$table]);
			}
			*/
			$tA = array();
			if(is_array($q_permissions)){
				$tA = Utils::checkArray($q_permissions,array('table_name'=>$table));
			}	
			
			
			foreach($privA as $priv){
				//$v = '';
				//(in_array($priv ,$tP) ) ? $v = 'Y' : $v = '';
				$v = '';
				if(isset($tA[$priv . '_priv'])){
					if($tA[$priv . '_priv'] == '1'){
						$v = 'Y';
					}
				}
				
				$r .= '<td>' . Forms::checkboxBasic('table_' . $table . '_' . $priv,$v, array('class'=>'checkbox noparse col_'.$priv . ' row_'.$table,'label'=>'')) . '</td>';
			
			}
			
			
			$r .= '</tr>';
			
		
		}
		
		$r .= '</table>';
		
		$options['label'] = "Tables";
		Forms::buildElement($name,$r,$options);
		Forms::hidden($name,'',array('omit_id'=>true));
					
	}
	
	return true;
}