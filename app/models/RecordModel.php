<?php

class RecordModel extends Model
{
	
	//private $data;
	
	public function __construct($route)
	{
		parent::__construct($route);
		
		$this->data = $this->db->queryRow("SELECT * FROM `" . $this->route['table'] . "` WHERE id = '" . $this->route['id'] . "'");
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