<?php

class AdminModel extends Model
{

	public function getGroups()
	{
		$q = $this->db->query("SELECT * FROM " . BLACKBIRD_TABLE_PREFIX . "groups ORDER BY name");
		$dA = array();
		foreach($q as $row){
			$q_tot = $this->db->queryRow("SELECT COUNT(*) AS total FROM " . BLACKBIRD_TABLE_PREFIX . "users__groups WHERE group_id = '$row[id]'");
			$dA[] = array('id'=>$row['id'],'admin'=>$row['admin'],'name'=>$row['name'],'members'=>$q_tot['total']);
		}
		return $dA;		
	}
	
}