<?php
class ProcessRecord
{
	
	private $cms;
	private $name_space;
	private $mode;
	private $query_action;
	private $errorData;
	
	function __construct($cms,$name_space=""){
				
		$this->cms = $cms;
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
		$q_cols = Db::query("SHOW COLUMNS FROM $this->table");
		$row_data = array();
				
		foreach($q_cols as $col){
			$col_ready = false;
						
			$q_col = Db::queryRow("SELECT * FROM cms_cols WHERE column_name = '$col[Field]' AND table_name = '$this->table' AND process_module != '' AND process_mode = '$this->query_action'");
				
			if(!$q_col){
				$q_col = Db::queryRow("SELECT * FROM cms_cols WHERE column_name = '$col[Field]' AND table_name = '$this->table' AND process_module != '' AND process_mode = ''");
			}
			if(!$q_col){
				$q_col = Db::queryRow("SELECT * FROM cms_cols WHERE column_name = '$col[Field]' AND table_name = '*' AND process_module != '' AND process_mode = '$this->query_action'");
			}
			if(!$q_col){
				$q_col = Db::queryRow("SELECT * FROM cms_cols WHERE column_name = '$col[Field]' AND table_name = '*' AND process_module != '' AND process_mode = ''");
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
							$options['id'] = Db::getInsertId($this->table);
						}
						$options['col_name'] = $col['Field'];
						$options['table'] = $this->table;
						
						if(isset($_REQUEST[$this->_name_space . $col['Field']])){
							$value = $_REQUEST[$this->_name_space . $col['Field']];							
						}else{
							$value = '';
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
					
						//die('this module is broken');
						//if we are a position column
						if($this->query_action == "update"){
							//sort_position($table,"SELECT id FROM `$table` ORDER BY $col[Field]",$id,$_REQUEST[$col['Field']]);
						}
						if($this->query_action == "insert"){
							$q_pos = Db::queryRow("SELECT max($col[Field]) FROM `$this->table`");
							$row_data[] = array("field"=>$col['Field'],"value"=>($q_pos[0] + 1));
						}
						$col_ready = true;
					break;
				}
				
			}		
					
			//if we are a timestamp
			$col_type = strtolower($col['Type']);
			if($col_type == "datetime"){
				$row_data[] = array("field"=>$col['Field'],"value"=>Utils::assembleDateTime($col['Field'],$this->_name_space));
				$col_ready = true;
			}
			
			if($col_type == "date"){
				$row_data[] = array("field"=>$col['Field'],"value"=>Utils::assembleDate($col['Field'],$this->_name_space));
				$col_ready = true;
			}
					
			
			if(!$col_ready){		
				//if we are a generic column
				if(isset($_REQUEST[$this->_name_space . $col['Field']])){
					$row_data[] = array("field"=>$col['Field'],"value"=>$_REQUEST[$this->_name_space . $col['Field']]);
				}
			}
				
		}
		
		$q_table = Db::queryRow("SELECT * FROM cms_tables WHERE table_name = '$this->table'");
		
		if(strlen($q_table['process_module']) > 3){
			$this->cms->pluginTableProcess($this->table,$this->id,$this->query_action);
		}else{
				
			if(count($this->errorData) == 0){
			
				if($this->query_action == "insert"){
					$sql = Db::insert($this->table,$row_data);
					$this->id = mysql_insert_id();
				}
				
				if($this->query_action == "update"){
					$sql = Db::update($this->table,$row_data,"id",$this->id);
				}
						
				$row_data = array();
				$row_data[] = array('field'=>'table_name','value'=>$this->table);
				$row_data[] = array('field'=>'record_id','value'=>$this->id);
				$row_data[] = array('field'=>'action','value'=>$this->query_action);
				$row_data[] = array('field'=>'user_id','value'=>$this->cms->session->u_id);
				$row_data[] = array('field'=>'sql','value'=>$sql);
				$row_data[] = array('field'=>'session_id','value'=>session_id());
				
				Db::insert('cms_history',$row_data);
				
			}else{
				
				$GLOBALS['errors'] = $this->errorData;
			
			}
		
		}
		
		
		
	}
	
	
}
?>