<?php

class RecordModel extends Model
{
		
	public function processDelete($table,$id_set)
	{
		if(_ControllerFront::$session->getPermissions('delete',$table)){

			switch($table){

				default:

					foreach($id_set as $id){
						AdaptorMysql::sql("DELETE FROM `$table` WHERE id = $id");
						$this->logChange($table,$id);
					}				

				break;

			}

		}

	}
	
	private function logChange($table,$id)
	{
		$row_data = array();
		$row_data[] = array('field'=>'table_name','value'=>$table);
		$row_data[] = array('field'=>'record_id','value'=>$id);
		$row_data[] = array('field'=>'action','value'=>'delete');
		$row_data[] = array('field'=>'user_id','value'=>_ControllerFront::$session->u_id);
		$row_data[] = array('field'=>'session_id','value'=>session_id());
		AdaptorMysql::insert(BLACKBIRD_TABLE_PREFIX . 'history',$row_data);
	}
	
	public function getData($config=null)
	{
		
		//needs to function in both edit and insert modes, obviously	
		$this->table = $config['table'];
		$this->id = $config['id'];
		$this->channel = $config['channel'];
		
		if($config['query_action'] == 'insert'){
			$this->mode = 'add';
		}else{
			$this->mode = 'edit';
		}
		
		//get table description data
		$this->tableMeta = AdaptorMysql::query("SHOW COLUMNS FROM $this->table",MYSQL_BOTH);
		
		//get key
		//default, set as first column
		$this->key = $this->tableMeta[0]['Field'];
		//find real primary key
		foreach($this->tableMeta as $column){
			if($column['Key'] == 'PRI'){
				$this->key = $column['Field'];
			}
		}		
		
		$q = $this->db->queryRow("SELECT * FROM `" . $this->table . "` WHERE " . $this->key . " = '" . $this->id . "'");
		
		$this->data = array();
		$this->col_config = false;
		
		foreach($this->tableMeta as $col){
			
			//check for config info here
			$q_col = false;
			//get configuration data for form
			$q_c = array();
			//get all the base config
			
			$tA = Utils::checkArray(_ControllerFront::$config['cols'],array('column_name'=>$col['Field']),true);
			if(is_array($tA)){
				$q_c = $tA;
			}

			//get anything from the blackbird_cols
			if($q_sql = $this->db->query("SELECT * FROM ".BLACKBIRD_TABLE_PREFIX."cols WHERE column_name = '$col[Field]' ORDER BY table_name,edit_mode,edit_channel")){
				$q_c = array_merge($q_c,$q_sql);
			}
								
			if(!$q_col){
				$q_col = Utils::checkArray($q_c,array('table_name'=>$this->table,'edit_mode'=>$this->mode,'edit_channel'=>$this->channel));
				if(!$q_col){
					$q_col = Utils::checkArray($q_c,array('table_name'=>$this->table,'edit_mode'=>$this->mode,'edit_channel'=>''));
				}
			}

			if(!$q_col){
				$q_col = Utils::checkArray($q_c,array('table_name'=>$this->table,'edit_mode'=>'','edit_channel'=>$this->channel));
				if(!$q_col){
					$q_col = Utils::checkArray($q_c,array('table_name'=>$this->table,'edit_mode'=>'','edit_channel'=>''));
				}
			}

			if(!$q_col){
				$q_col = Utils::checkArray($q_c,array('table_name'=>'*','edit_mode'=>$this->mode,'edit_channel'=>$this->channel));
				if(!$q_col){
					$q_col = Utils::checkArray($q_c,array('table_name'=>'*','edit_mode'=>$this->mode,'edit_channel'=>''));
				}
			}

			if(!$q_col){
				$q_col = Utils::checkArray($q_c,array('table_name'=>'*','edit_mode'=>'','edit_channel'=>$this->channel));
				if(!$q_col){
					$q_col = Utils::checkArray($q_c,array('table_name'=>'*','edit_mode'=>'','edit_channel'=>''));
				}
			}
						
			$value = $q[$col['Field']];
			$config = $q_col;

			//flow in the default value only if creating a new record
			if($q_col && $this->mode == 'add'){
				if($q_col['default_value'] != ''){
					$value = $q_col['default_value'];
				}
			}
			
			if($this->mode == 'edit' && !_ControllerFront::$session->getPermissions('update',$this->table)){
				//turn to readonly - check on foreign keys later
				
				if(is_array($config)){
					$config['edit_module'] = 'readonly';
				}else{
					$config = array('table_name'=>$this->table,'column_name'=>$col['Field'],'validate'=>'','default_value'=>'','edit_module'=>'readonly','edit_config'=>'','display_name'=>'','help'=>'');
				}
				
			}
			
			
			$this->data[$col['Field']] = array('name'=>$col['Field'],'value'=>$value,'type'=>$col['Type'],'config'=>$config);
		}
		
		return $this->data;
	}
	
	public function getRelated($table)
	{
		//find relations
		$this->table = $table;
		$q_related = $this->db->query("SELECT * FROM ".BLACKBIRD_TABLE_PREFIX."relations WHERE table_parent = '$this->table' ORDER BY position");
		return $q_related;
		
	}
	
	public function getHistory()
	{
		//grab history
	}
	
	public function getInspector()
	{
		//use created/modified from record... or
		
		//grab from CMS History
	}

}