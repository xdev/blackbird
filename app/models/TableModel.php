<?php

class TableModel extends Model
{
	
	
	public function getData($table='')
	{
		if($table != ''){
			$this->table = $table;
		}
		
		//set necessary variables
		
		$table_parent = Utils::setVar("table_parent",'');
		$id_parent = Utils::setVar("id_parent",'');
		$this->sql = '';
		
		//these should be mapped in from the controller - not set here
		$sort_dir = Utils::setVar("sort_dir","DESC");
		$offset = Utils::setVar("offset","0");
		$limit = Utils::setVar("limit","100");
		$search = Utils::setVar("search");
		$mode = Utils::setVar("mode","main");
		
		//get table description data
		$this->tableMeta = AdaptorMysql::query("SHOW COLUMNS FROM $this->table",MYSQL_BOTH);
		$this->key = AdaptorMysql::getPrimaryKey($this->table);		
		$sort_col = Utils::setVar("sort_col",$this->key);
		
		//check for config info here
		$q_col = false;
		//get configuration data for form
		$q_c = array();
		//get all the base config
		
		$tA = Utils::checkArray(_ControllerFront::$config['tables'],array('table_name'=>$this->table),true);
		if(is_array($tA)){
			$q_c = $tA;
		}
		
		if($q_sql = $this->db->query("SELECT * FROM ".BLACKBIRD_TABLE_PREFIX."tables WHERE table_name = '$this->table' ORDER BY table_name,display_mode")){
			$q_c = array_merge($q_c,$q_sql);
		}
		
		if(!$q_col){
			if($mode == 'main'){
				$q_col = Utils::checkArray($q_c,array('table_name'=>$this->table,'display_mode'=>'main'));
				if(!$q_col){
					$q_col = Utils::checkArray($q_c,array('table_name'=>$this->table,'display_mode'=>''));
				}
			}
			if($mode == 'related'){
				$q_col = Utils::checkArray($q_c,array('table_name'=>$this->table,'display_mode'=>'related'));
				if(!$q_col){
					$q_col = Utils::checkArray($q_c,array('table_name'=>$this->table,'display_mode'=>''));
				}
			}
		}
				
		//column description information		
		$fields = array();
		if($q_col['cols_default'] == ""){
			$select_cols = '*';			
		}else{
			$select_cols = $q_col['cols_default'];
			$fields = explode(",",$select_cols);
		}
		
		if($select_cols == "*"){
			$fields = array();
			for($i=0;$i<count($this->tableMeta);$i++){
				$row = $this->tableMeta[$i];
				$fields[] = $row[0];
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
			$label = '_History_';
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
					
					$_filter = array();
					
					if(isset($_REQUEST['filter_'.$col])){
						if($_REQUEST['filter_'.$col] != ''){
							$t = $_REQUEST['filter_'.$col];
							$whereA[] = "$col = '$t'";
							//$this->filtersA[] = array('col'=>$col,'value'=>$t);
							$_filter['col'] = $col;
							$_filter['value'] = $t;
						}
					}
					
					
					//query up option data					
					($filterWhere != '') ? $w = 'WHERE ' . $filterWhere : $w = '';
					$optionA = array();
					$field = $col;
					if($q_select = AdaptorMysql::query("SELECT DISTINCT `$field` FROM `$table` $w ORDER BY `$field`")){
						

						foreach($q_select AS $row){
							$sel = '';
							if(isset($_REQUEST['filter_'.$field])){
								if($_REQUEST['filter_'.$field] == $row[$field]){
									$sel = 'selected="selected"';
								}
							}

							$tv = _ControllerFront::formatCol($field,$row[$field],$table);
							$q_c = AdaptorMysql::query("SELECT * FROM " . BLACKBIRD_TABLE_PREFIX . "cols WHERE column_name = '$field'");

							if($q_c){				
								$q_col = Utils::checkArray($q_c,array('table_name'=>$table));
								if(!$q_col){
									$q_col = Utils::checkArray($q_c,array('table_name'=>'*'));
								}

								if($q_col){
									if($q_col['filter'] != ''){
										$tA = _ControllerFront::parseConfig($q_col['filter']);
										if(isset($tA['filter_length'])){
											if(strlen(strip_tags($tv)) > $tA['filter_length']){
												$tv = substr(strip_tags($tv),0,$tA['filter_length']) . '...';
											}
										}
									}
								}
							}
							
							$optionA[] = array('value'=>$row[$field],'label'=>$tv,'selected'=>$sel);
						}
					}
					//sort it
					$optionA = Utils::arraySort($optionA,'label');					
					$_filter['options'] = $optionA;					
					$this->filtersA[$field] = $_filter;
					
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
		
		//build recordSet
		if($query_data){
			foreach($query_data as $row){
				$tA = array();
				
				for($j=0;$j<count($fields);$j++){
					$data = _ControllerFront::formatCol($fields[$j],$row[$fields[$j]],$table );
					$tA[$fields[$j]] = array('col'=>$fields[$j],'value'=>$data);	
				}
				
				//convert to the key
				$this->recordSet[$row[$this->key]] = _ControllerFront::injectData($tA,$table,'body');
				
			}
			
		}
		
		//headerData
		$tA = array();
		for($j=0;$j<count($fields);$j++){
			(isset($row[$j])) ? $value = $row[$j] : $value = '';
			$data = _ControllerFront::formatCol($fields[$j],$value,$table);			
			$tA[$fields[$j]] = array('col'=>$fields[$j],'value'=>$data);	
		}
		
		$this->headerData = _ControllerFront::injectData($tA,$table,'head');
		
		$delete_allowed = false;
		//if($this->cms->session->privs("delete",$table)){
		//	$delete_allowed = true;
		//}
		
					
		return array(
			'headerData'=>$this->headerData,
			'rowData'=>$this->recordSet,
			'sort_col'=>$sort_col,
			'sort_dir'=>$sort_dir,
			'table'=>$this->table,
			'rows_total'=>$rows_total,
			'limit'=>$limit,
			'offset'=>$offset,
			'mode'=>$mode,
			'filtersA'=>$this->filtersA,
			'filterA'=>$filterA,
			'search'=>$search
		);
		
	}
	
}