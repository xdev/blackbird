<?php

class RecordModel extends Model
{
	
	//private $data;
	
	public function __construct($route)
	{
		parent::__construct($route);
		
		//figure out if we're operating through a normal request mode, or via ajax
		
		
		$this->table = $this->route['table'];
		$this->mode = 'edit';
		$this->channel = 'main';
		$this->id = $this->route['id'];
		
		$q = $this->db->queryRow("SELECT * FROM `" . $this->table . "` WHERE id = '" . $this->id . "'");
		$q_cols = $this->db->query("SHOW COLUMNS FROM `$this->table`",MYSQL_BOTH);
		
		$this->data = array();
		$this->col_config = false;
		
		foreach($q_cols as $col){			
			$this->data[] = array('name'=>$col['Field'],'value'=>$q[$col['Field']],'type'=>$col['Type']);
		}		
		
		//get configuration data for form
		$q_c = array();
		//get all the base config
		/*
		$tA = Utils::checkArray($this->cms->config[BLACKBIRD_TABLE_PREFIX.'cols'],array('column_name'=>$col['Field']),true);
		if(is_array($tA)){
			$q_c = $tA;
		}
		*/
		
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
		
		if($q_col){
			$this->col_config = $q_col;
		}
	}
	
	public function getRelated()
	{
		//find relations
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