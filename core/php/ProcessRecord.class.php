<?php

/* $Id$ */

class ProcessRecord
{
	
	private $cms;
	private $name_space;
	private $mode;
	private $query_action;
	private $errorData;
	
	function __construct($cms,$name_space=""){
				
		$this->cms = $cms;
		$this->db = $cms->db;
		$this->name_space = $name_space;
		$this->errorData = Array();
		
		$this->_name_space = '';
		if(strlen($this->name_space) > 1){
			$this->_name_space = $this->name_space . "_";
		}
		
		$this->table = Utils::setVar("table");
		$this->query_action = Utils::setVar('query_action');
		
		
			
		if($this->query_action == "update"){
			$p_update = $this->cms->session->privs("update",$this->table);
			if($p_update){
				$this->id = Utils::setVar("id");
				$this->process();
			}else{
				die();
			}
		}
		
		if($this->query_action == "insert"){
			$p_insert = $this->cms->session->privs("insert",$this->table);
			if($p_insert){
				$this->process();
			}else{
				die();
			}
		}
			
	}
	
	function process()
	{
		$q_cols = $this->db->query("SHOW COLUMNS FROM $this->table");
		$row_data = array();
				
		foreach($q_cols as $col){
			/*
			$q_c = $this->db->query("SELECT * FROM cms_cols WHERE column_name = '$col[Field]' AND process_module != ''");
			if($q_c){				
				$q_col = Utils::checkArray($q_c,array('table_name'=>$this->table,'process_mode'=>$this->query_action));
				if(!$q_col){
					$q_col = Utils::checkArray($q_c,array('table_name'=>$this->table,'process_mode'=>''));
				}
				
				if(!$q_col){
					$q_col = Utils::checkArray($q_c,array('table_name'=>'*','process_mode'=>$this->query_action));
					if(!$q_col){
						$q_col = Utils::checkArray($q_c,array('table_name'=>'*','process_mode'=>''));
					}
				}
			}
			*/
			
			$col_type = strtolower($col['Type']);
			$col_ready = false;
						
			$q_col = $this->db->queryRow("SELECT * FROM cms_cols WHERE column_name = '$col[Field]' AND table_name = '$this->table' AND process_module != '' AND process_mode = '$this->query_action'");
				
			if(!$q_col){
				$q_col = $this->db->queryRow("SELECT * FROM cms_cols WHERE column_name = '$col[Field]' AND table_name = '$this->table' AND process_module != '' AND process_mode = ''");
			}
			if(!$q_col){
				$q_col = $this->db->queryRow("SELECT * FROM cms_cols WHERE column_name = '$col[Field]' AND table_name = '*' AND process_module != '' AND process_mode = '$this->query_action'");
			}
			if(!$q_col){
				$q_col = $this->db->queryRow("SELECT * FROM cms_cols WHERE column_name = '$col[Field]' AND table_name = '*' AND process_module != '' AND process_mode = ''");
			}
			
			
			
			
			if($q_col){
			
				$module = $q_col['process_module'];
								
				switch(true){
			
					case $module == 'plugin':
						//if we have a plugin function
						$options = array();
						$options['mode'] = $this->query_action;
						$options['name_space'] = $this->_name_space;
						
						if($this->query_action == "update"){
							$options['id'] = $this->id;
						}
						if($this->query_action == "insert"){
							$options['id'] = $this->db->getInsertId($this->table);
						}
						$options['col_name'] = $col['Field'];
						$options['table'] = $this->table;
						
						if(isset($_REQUEST[$this->_name_space . $col['Field']])){
							$value = $_REQUEST[$this->_name_space . $col['Field']];
						}else{
							$value = '';
						}
						
						if(strlen($q_col['process_config']) > 1){
							$config = Utils::parseConfig($q_col['process_config']);
							$options = array_merge($options,$config);
						}
						
						$t = $this->cms->pluginColumnProcess($this->_name_space . $col['Field'],$value,$options);
						
						if(isset($t['error'])){
							$this->errorData[] = array('field'=>$col['Field'],'error'=>$t['error']);	
						}else{
							if(is_array($t)){
								$row_data[] = $t;
							}
						}
						
						
						$col_ready = true;
					break;
					
					case $module == 'position':
						//if we are a position column
						$where = '';
						
						if(strlen($q_col['process_config']) > 1){
							$config = Utils::parseConfig($q_col['process_config']);
						}
						
						$value = $_REQUEST[$this->_name_space . $col['Field']];
						
						if($this->query_action == "update"){
							
							
							//check for constraints from config
							if(isset($config)){
								//try to find in row_data
								$foundrow = false;
								foreach($row_data as $temprow){
									if($temprow['field'] == $config['col_constraint']){
										$foundrow = true;
										$where = "WHERE `".$config['col_constraint']."` = '".$temprow['value']."' ";
									}
								}
								if(!$foundrow){
									//check for the $_REQUEST
									$where = "WHERE `".$config['col_constraint']."` = '".$_REQUEST[$this->_name_space . $config['col_constraint']]."' ";
								}
							}
							
							$this->cms->sortPosition($this->table,"SELECT id FROM `$this->table` $where ORDER BY $col[Field]",$this->id,$value);
						}
						if($this->query_action == "insert"){
							//check for constraints from config
							if(isset($config)){
								$where = "WHERE `".$config['col_constraint']."` = '".$_REQUEST[$this->_name_space . $config['col_constraint']]."' ";
							}
							
							$q_pos = $this->db->queryRow("SELECT max($col[Field]) FROM `$this->table` $where");
							$row_data[] = array("field"=>$col['Field'],"value"=>($q_pos[0] + 1));
						}
						$col_ready = true;
					break;
					
					case $module == 'timestamp':
						$row_data[] = array("field"=>$col['Field'],"value"=>(($col['Field'] == 'created' && $_REQUEST[$this->_name_space . $col['Field']]) ? $_REQUEST[$this->_name_space . $col['Field']] : Utils::now()));
						$col_ready = true;
					break;
					
				}
				
			}
			
			
			if(!$col_ready){
				//if we are a timestamp
				if($col_type == "datetime"){
					$row_data[] = array("field"=>$col['Field'],"value"=>Utils::assembleDateTime($col['Field'],$this->_name_space));
					$col_ready = true;
				}
			
				if($col_type == "date"){
					$row_data[] = array("field"=>$col['Field'],"value"=>Utils::assembleDate($col['Field'],$this->_name_space));
					$col_ready = true;
				}
			
				if($col_type == "time"){
					$row_data[] = array("field"=>$col['Field'],"value"=>Utils::assembleTime($col['Field'],$this->_name_space));
					$col_ready = true;
				}
			}
			
			
			if(!$col_ready){
				//if we are a generic column
				if(isset($_REQUEST[$this->_name_space . $col['Field']])){
					$row_data[] = array("field"=>$col['Field'],"value"=>$_REQUEST[$this->_name_space . $col['Field']]);
				}
			}
				
		}
		
		$q_table = $this->db->queryRow("SELECT * FROM cms_tables WHERE table_name = '$this->table'");
		
		if(strlen($q_table['process_module']) > 3){
			$this->cms->pluginTableProcess($this->table,$this->id,$this->query_action);
		}else{
				
			if(count($this->errorData) == 0){
				
				//die(print_r($row_data));
				
				if($this->query_action == "insert"){
					$sql = $this->db->insert($this->table,$row_data);
					$this->id = mysql_insert_id();
				}
				
				if($this->query_action == "update"){
					$sql = $this->db->update($this->table,$row_data,"id",$this->id);
				}
						
				$row_data = array();
				$row_data[] = array('field'=>'table_name','value'=>$this->table);
				$row_data[] = array('field'=>'record_id','value'=>$this->id);
				$row_data[] = array('field'=>'action','value'=>$this->query_action);
				$row_data[] = array('field'=>'user_id','value'=>$this->cms->session->u_id);
				$row_data[] = array('field'=>'sql','value'=>$sql);
				$row_data[] = array('field'=>'session_id','value'=>session_id());
				
				$this->db->insert('cms_history',$row_data);
				
			}else{
				
				$GLOBALS['errors'] = $this->errorData;
			
			}
		
		}
		
		
		
	}
	
	
	
	
}
?>