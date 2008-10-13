<?php

class AdminModel extends Model
{

	public function getGroups()
	{
		//get groups, find members per group, etc
		$q = $this->db->query("SELECT * FROM " . BLACKBIRD_TABLE_PREFIX . "groups ORDER BY name");
		$dA = array();
		//foreach($q as $row){
			//$q_tot = $this->db->queryRow("SELECT COUNT(*) AS total FROM " . BLACKBIRD_TABLE_PREFIX . "users
			//get user count
		//}
		
		return $q;
		
	}
	
}