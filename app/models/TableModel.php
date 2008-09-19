<?php

class TableModel extends Model
{
	
	
	public function getData($table='')
	{
		//Get data
		if($table != ''){
			$this->table = $table;
		}
		
		//Get config data
		
		//Get headers
		$q = AdaptorMysql::query("SHOW COLUMNS FROM `$this->table`",MYSQL_BOTH);
		$fields = array();
		for($i=0;$i<count($q);$i++){
			$row = $q[$i];
			$fields[] = $row[0];
		}
		
		$q = AdaptorMysql::query("SELECT * FROM `$this->table`");

		return array('headerData'=>$fields,'rowData'=>$q);
		
	}
	
}