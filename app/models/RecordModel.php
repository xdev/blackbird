<?php

class RecordModel extends Model
{
	
	//private $data;
	
	public function __construct($route)
	{
		parent::__construct($route);
		
		//figure out if we're operating through a normal request mode, or via ajax

		
	}
	
	public function getData($config=null)
	{
		
		//needs to function in both edit and insert modes, obviously	
		//$this->table = $this->route['table'];
		$this->table = $config['table'];
		$this->id = $config['id'];
		$this->channel = $config['channel'];
		
		if($config['query_action'] == 'insert'){
			$this->mode = 'add';
		}else{
			$this->mode = 'edit';
		}		
		
		$q = $this->db->queryRow("SELECT * FROM `" . $this->table . "` WHERE id = '" . $this->id . "'");
		$q_cols = $this->db->query("SHOW COLUMNS FROM `$this->table`",MYSQL_BOTH);
		
		$this->data = array();
		$this->col_config = false;
		
		foreach($q_cols as $col){
			
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

			$q_col = Utils::checkArray($q_c,array('table_name'=>$this->table,'edit_mode'=>$this->mode,'edit_channel'=>$this->channel));
			if(!$q_col){
				$q_col = Utils::checkArray($q_c,array('table_name'=>$this->table,'edit_mode'=>$this->mode,'edit_channel'=>''));
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
						
			$this->data[$col['Field']] = array('name'=>$col['Field'],'value'=>$q[$col['Field']],'type'=>$col['Type'],'config'=>$q_col);
		}
		
		return $this->data;
	}
	
	public function getRelated()
	{
		//find relations
		$this->table = $this->route['table'];
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