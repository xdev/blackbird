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
		$privA = array('browse','insert','update','delete');
		
		
		$xml = simplexml_load_string($value);
		$tableA = array();
		if($xml){
			foreach($xml->table as $mytable){
				$t = sprintf($mytable['name']);
				$tableA[$t] = sprintf($mytable);
			}
		}
		
		$r = '<table>
		<tr><th>Table</th>';
		
			foreach($privA as $priv){
				
				$r .= '<th>' . $priv . '</th>';
				
			}
		$r .= '</tr>';
					
		foreach($q as $table){
		
		
			if($table['Comment'] != 'private'){
			
				$r .= '<tr>';
				$r .= '<td>' .  Utils::formatHumanReadable($table['Name']) . '</td>';
				
				$tP = array();
				if(isset($tableA[$table['Name']])){
					$tP = explode(',',$tableA[$table['Name']]);
				}
				
				foreach($privA as $priv){
					
					(in_array($priv ,$tP) ) ? $v = 'Y' : $v = '';
					$r .= '<td>' . Forms::checkboxBasic('table_' . $table['Name'] . '_' . $priv,$v, array('class'=>'checkbox noparse','label'=>'')) . '</td>';
				
				}
				
				
				$r .= '</tr>';
			}
		
		}
		
		$r .= '</table>';
		
		$options['label'] = "Tables";
		Forms::buildElement($name,$r,$options);
		Forms::hidden($name,'',array('omit_id'=>true));
					
	}
	
	return true;
}