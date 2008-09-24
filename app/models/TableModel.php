<?php

class TableModel extends Model
{
	
	
	public function getData($table='')
	{
		if($table != ''){
			$this->table = $table;
		}
		
		//set necessary variables
		
		$table_parent = '';
		$this->sql = '';
		
		//these should be mapped in from the controller - not set here
		$sort_col = Utils::setVar("sort_col","id");
		$sort_dir = Utils::setVar("sort_dir","DESC");
		$offset = Utils::setVar("offset","0");
		$limit = Utils::setVar("limit","20");
		$search = Utils::setVar("search");
		
		//get config data
		$tA = Utils::checkArray(_ControllerFront::$config['tables'],array('table_name'=>$this->table,'display_mode'=>'related'));
		if(is_array($tA)){
			$q_display = $tA;
		}else{
			$q_display = AdaptorMysql::queryRow("SELECT * FROM `".BLACKBIRD_TABLE_PREFIX."tables` WHERE table_name = '$this->table' AND display_mode = 'related'");
		}		
		
		if(!$q_display){
			$tA = Utils::checkArray(_ControllerFront::$config['tables'],array('table_name'=>$this->table,'display_mode'=>''));
			if(is_array($tA)){
				$q_display = $tA;
			}else{
				$q_display = AdaptorMysql::queryRow("SELECT * FROM `".BLACKBIRD_TABLE_PREFIX."tables` WHERE table_name = '$this->table' AND display_mode = ''");
			}
		}
		
		//column description information		
		$fields = array();
		if($q_display['cols_default'] == ""){
			//die();
			$select_cols = '*';
			$q = AdaptorMysql::query("SHOW COLUMNS FROM $this->table",MYSQL_BOTH);
			$fields = array();
			for($i=0;$i<count($q);$i++){
				$row = $q[$i];
				$fields[] = $row[0];
			}
		}else{
			$select_cols = $q_display['cols_default'];
			$fields = explode(",",$select_cols);
			
			if($select_cols == "*"){
				$q = AdaptorMysql::query("SHOW COLUMNS FROM $this->table",MYSQL_BOTH);
				$fields = array();
				for($i=0;$i<count($q);$i++){
					$row = $q[$i];
					$fields[] = $row[0];
				}
			}
		}
		
		
		//filters and WHERE
		$filterA = array();
		$this->filtersA = array();
		$whereA = array();
		$where = 'WHERE ';
		
		$where .= $this->sql;
		
		$filterWhere = '';
		
		if($table == BLACKBIRD_TABLE_PREFIX.'history'){
			$filterWhere = $this->sql;
			$label = 'CMS History';
		}else if($table_parent != ''){
			$relation = AdaptorMysql::queryRow("SELECT * FROM ".BLACKBIRD_TABLE_PREFIX."relations WHERE table_parent = '$table_parent' AND table_child = '$table'");
			$q_parent = AdaptorMysql::queryRow("SELECT * FROM $table_parent WHERE id = $id_parent");
			$sql_val = $q_parent[$relation['column_parent']];
			$whereA[] = "$relation[column_child] = '$sql_val'";			
			$filterWhere = "$relation[column_child] = '$sql_val'";
			
			//from build in a page
			if(isset($this->config['sql_where'])){				
				$whereA[] = $this->config['sql_where'];
				$filterWhere .= ' AND ' . $this->config['sql_where'];
			}
			
			//from build in remote
			if(isset($_REQUEST['sql_where'])){				
				$whereA[] = stripslashes($_REQUEST['sql_where']);
				$filterWhere .= ' AND ' . stripslashes($_REQUEST['sql_where']);
			}
			
			$label = $relation['label'];
		
		}else{
			$label = $table;
		}
		
		if($search != ""){
						
			$q = $this->db->query("SHOW COLUMNS FROM $this->table",MYSQL_BOTH);
			$search_fields = array();
			for($i=0;$i<count($q);$i++){
				$row = $q[$i];
				$search_fields[] = $row[0];
			}
			
			
			//Generate search
			$mySearch = "'%" . mysql_real_escape_string(stripslashes(trim($search))) . "%'";
			$rSearch = AdaptorMysql::generateSearch($search_fields,$mySearch);
		}
		
		
		$q_filters = AdaptorMysql::query("SELECT column_name FROM ".BLACKBIRD_TABLE_PREFIX."cols WHERE (table_name = '*' OR table_name = '$table') AND filter != ''");
		if($q_filters){
			//loop through and find intersections
			foreach($q_filters as $filter){
				$col = $filter['column_name'];
				if(in_array($col,$fields)){
					$filterA[] = $col;
					if(isset($_REQUEST['filter_'.$col])){
						if($_REQUEST['filter_'.$col] != ''){
							$t = $_REQUEST['filter_'.$col];
							$whereA[] = "$col = '$t'";
							$this->filtersA[] = array('col'=>$col,'value'=>$t);
						}
					}
				}
			}
		}		
		if(count($whereA) > 0){
			if($where != 'WHERE '){
				$where .= ' AND ';
			}
			$where .= join($whereA,' AND ');
		}else{
			if($where == 'WHERE '){
				$where = '';
			}
		}
				
		if($search == ''){
			$query_data = AdaptorMysql::query("SELECT $select_cols FROM `$table` $where ORDER BY `$sort_col` $sort_dir LIMIT $limit OFFSET $offset");
			if($query_data){
				$rT = count($query_data);
			}else{
				$rT = 0;
			}
			$q2 = AdaptorMysql::query("SELECT * FROM $table $where");
			if($q2){
				$rows_total = count($q2);
			}else{
				$rows_total = 0;
			}	
		
		}else{
		
			if($where == ''){
				$where = 'WHERE ';
			}
			
			if($where != 'WHERE ' && $rSearch != ''){
				$where .= ' AND (';
				$rSearch = $rSearch . ')';
			}
						
			$query_data = AdaptorMysql::query("SELECT $select_cols FROM `$table` $where $rSearch ORDER BY `$sort_col` LIMIT $limit OFFSET $offset");
			$rT = count($query_data);
			$q2 = AdaptorMysql::query("SELECT * FROM `$table` $where $rSearch");
			if($q2){
				$rows_total = count($q2);
			}else{
				$rows_total = 0;
			}	
		}
		$this->recordSet = array();
		
		//build recordSet and headerData
		if($query_data){
			foreach($query_data as $row){
				
				$tA = array();
				$rowData = array();
				
				for($j=0;$j<count($fields);$j++){
					$data = _ControllerFront::formatCol($fields[$j],$row[$fields[$j]],$table );
					$tA[$fields[$j]] = array('col'=>$fields[$j],'value'=>$data);	
				}
				
				$rowData = _ControllerFront::injectData($tA,$table);
				$this->recordSet[$row['id']] = $rowData;
				
			}
			
		}
		
		//
		$tA = array();
		for($j=0;$j<count($fields);$j++){
			if(isset($row[$j])){
				$data = _ControllerFront::formatCol($fields[$j],$row[$j],$table);
			}else{
				$data = _ControllerFront::formatCol($fields[$j],'',$table);
			}
			
			$tA[$fields[$j]] = array('col'=>$fields[$j],'value'=>$data);	
		}
		
		$rowData = _ControllerFront::injectData($tA,$table);
		$this->headerData = $rowData;
		
		$delete_allowed = false;
		//if($this->cms->session->privs("delete",$table)){
		//	$delete_allowed = true;
		//}
		//die(print_r($this->recordSet));
		
					
		return array(
			'headerData'=>$fields,
			'rowData'=>$this->recordSet,
			'sort_col'=>$sort_col,
			'sort_dir'=>$sort_dir,
			'table'=>$this->table,
			'rows_total'=>$rows_total,
			'limit'=>$limit,
			'offset'=>$offset
		);
		
	}
	
	public function getDataGrid($table='')
	{
		
	}
	
}