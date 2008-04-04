<?php

/* $Id$ */

class DataGridAjax
{
	private $_data;	
	private $recordSet;
	private $headerData;
	private	$filtersA;
	private $db;
	
	public function __construct($cms)
	{
		$this->db = $cms->db;
	}
	
	public function __set($name,$value)
	{
		$this->_data[$name] = $value;
	}
	
	public function __get($name)
	{
		if(isset($this->_data[$name])){
			return $this->_data[$name];
		}else{
			return false;
		}		
	}
	
	
	function build()
	{	
		
		if(!isset($_REQUEST['remote_method'])){
			
			//build it
			
			$this->renderTable();
			
		}
	}
	
	function renderTable()
	{
		
		if($this->table){
			$table = $this->table;
		}else{
			$table = Utils::setVar('table');
		}
		
		$options = array('mode'=>'ajax');
		
		if($this->name_space){
			$name_space = $this->name_space;
		}else{
			$name_space = Utils::setVar('name_space');
		}
		
		
		if($this->table_parent){
			$table_parent = $this->table_parent;
		}else{
			$table_parent = Utils::setVar('table_parent');
		}
		
		if($this->id_parent){
			$id_parent = $this->id_parent;
		}else{
			$id_parent = Utils::setVar('id_parent');
		}
		
		$has_privs = true;
		
		$sort_col = Utils::setVar("sort_col","id");
		$sort_dir = Utils::setVar("sort_dir","DESC");
		$sort_index = Utils::setVar("sort_index","0");
		$sort_max = Utils::setVar("sort_max","10");
		
		$search = Utils::setVar("search");
		
		$q_display = $this->db->queryRow("SELECT * FROM `cms_tables` WHERE table_name = '$table' AND display_mode = 'related'");
		
		if(!$q_display){
			$q_display = $this->db->queryRow("SELECT * FROM `cms_tables` WHERE table_name = '$table' AND display_mode = ''");
		}
		
		$controller = 'data_grid_' . $name_space;
				
		$fields = array();
		
		if($q_display['cols_default'] == ""){
			die();
		}else{
			$select_cols = $q_display['cols_default'];
			$fields = explode(",",$select_cols);
			
			if($select_cols == "*"){
				$q = $this->db->query("SHOW COLUMNS FROM $table");
				$fields = array();
				for($i=0;$i<count($q);$i++){
					$row = $q[$i];
					$fields[] = $row[0];
				}
			}
		}
		
		//$where = "WHERE $sql_col = '$sql_val' ";
		
		$filterA = array();
		$this->filtersA = array();
		$whereA = array();
		$where = 'WHERE ';
		
		
		$where .= $this->sql;
		
		$filterWhere = '';
		
		if($table == 'cms_history'){
			$filterWhere = $this->sql;
			$label = 'CMS History';
		}else if($table_parent != ''){
			$relation = $this->db->queryRow("SELECT * FROM cms_relations WHERE table_parent = '$table_parent' AND table_child = '$table'");
			$q_parent = $this->db->queryRow("SELECT * FROM $table_parent WHERE id = $id_parent");
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
						
			$q = $this->db->query("SHOW COLUMNS FROM $table");
			$search_fields = array();
			for($i=0;$i<count($q);$i++){
				$row = $q[$i];
				$search_fields[] = $row[0];
			}
			
			
			//Generate search
			$mySearch = "'%" . mysql_real_escape_string(stripslashes(trim($search))) . "%'";
			$rSearch = $this->db->generateSearch($search_fields,$mySearch);
		}
		
		
		$q_filters = $this->db->query("SELECT column_name FROM cms_cols WHERE (table_name = '*' OR table_name = '$table') AND filter != ''");
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
			$query_data = $this->db->query("SELECT $select_cols FROM `$table` $where ORDER BY `$sort_col` $sort_dir LIMIT $sort_index, $sort_max");
			if($query_data){
				$rT = count($query_data);
			}else{
				$rT = 0;
			}
			$q2 = $this->db->query("SELECT * FROM $table $where");
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
						
			$query_data = $this->db->query("SELECT $select_cols FROM `$table` $where $rSearch ORDER BY `$sort_col` LIMIT $sort_index, $sort_max");
			$rT = count($query_data);
			$q2 = $this->db->query("SELECT * FROM `$table` $where $rSearch");
			if($q2){
				$rows_total = count($q2);
			}else{
				$rows_total = 0;
			}	
		}
					
		
		
		//build recordSet and headerData
		if($query_data){
			foreach($query_data as $row){
				
				$tA = array();
				$rowData = array();
				
				for($j=0;$j<count($fields);$j++){
					$data = $this->cms->formatCol($fields[$j],$row[$j],$table );
					$tA[$fields[$j]] = array('col'=>$fields[$j],'value'=>$data);	
				}
				
				$rowData = $this->cms->injectData($tA,$table);
				$this->recordSet[$row['id']] = $rowData;
				
			}
			
		}
		
		//
		$tA = array();
		for($j=0;$j<count($fields);$j++){
			if(isset($row[$j])){
				$data = $this->cms->formatCol($fields[$j],$row[$j],$table);
			}else{
				$data = $this->cms->formatCol($fields[$j],'',$table);
			}
			
			$tA[$fields[$j]] = array('col'=>$fields[$j],'value'=>$data);	
		}
		
		$rowData = $this->cms->injectData($tA,$table);
		$this->headerData = $rowData;
		
		$delete_allowed = false;
		if($this->cms->session->privs("delete",$table)){
			$delete_allowed = true;
		}
		//Data Grid
		
		print "<h3 class=\"edit_info\">$label</h3>";
		
		$class = '';
		
		$click_base = $controller . ".setProperty('sort_index','";
		
		print '
		<div class="actions">';
						
		if($options['mode'] == 'ajax'){
			if($this->cms->session->privs("insert",$table)){
			print '
			<div class="left">
				<a class="icon new" href="#" onclick="CMS.addNewRecord(\'' . $table . '\',\'' . $name_space . '\'); " title="Add new record">Add New</a>
			</div>';
		
			}
		}
		
		
		if(isset($_REQUEST['search'])){
			if($_REQUEST['search'] != ''){
				$s_value = $_REQUEST['search'];
			}else{
				$s_value = 'Search...';
			}
		}else{
			$s_value = 'Search...';
		}
		
		print '<div class="right">';
			print '<p class="actions related">';
			print '<input class="search" id="' . $name_space . '_search" type="text" value="' . $s_value . '" size="20" onclick="clickclear(this, \'Search...\')" onblur="clickrecall(this,\'Search...\')"  />';
			print '<a class="icon search" href="#" onclick="' . $controller . '.search();" title="Submit search query">Search</a>';
			print '<a class="icon undo" href="#" onclick="' . $controller . '.reset();" title="Reset Data Grid">Reset</a>';
			//print '<div class="clearfix"></div>';
			print '</p>';
		//Pagination
		if($rows_total > $sort_max){
			
			
			$rem = ceil($rows_total / $sort_max);
			$lastp = floor($rows_total / $sort_max);
			$sort_t = ($sort_index / $rows_total);
			
			$p = floor($rem * $sort_t);
										
			print '<p class="pagination">';										
			
			printf('<a class="icon first" %s title="First page">First</a>', ($p != 0) ? 'href="#" onclick="' . $click_base . 0 . '\');"' : '');
			printf('<a class="icon previous" %s title="Previous page">Previous</a>', ($p != 0) ? 'href="#" onclick="' . $click_base . (($p - 1) * $sort_max).'\');"' : '' );
										
			//Record display info
			$t = $sort_index + $sort_max;
			if($t > $rows_total){
				$t = $rows_total;
			}
			
			printf('<span class="values">%s</span>',"($sort_index-$t) of $rows_total Records");
			printf('<a class="icon next" %s title="Next page">Next</a>', ($p < $rem - 1) ? 'href="#" onclick="'. $click_base . (($p + 1) * $sort_max).'\');"' : '');
			printf('<a class="icon last" %s title="Last page">Last</a>', ($p < $rem - 1) ? 'href="#" onclick="'. $click_base . ($lastp * $sort_max) . '\');"' : '');
			
			print '</p>';
								
							
							
							
			}else{
			
				print '<p class="pagination"><span class="values">' . $rows_total . ' Records</span></p>';
			
			}						
			
			print '
			</div>
			<div class="clearfix"></div>
		</div>';
	
				
		print '
		<table class="data_grid' . $class . '">
			<thead>';
				
				
				if(count($filterA) > 0){
			print '
			<tr class="filter">';
			if($delete_allowed){
				print '<td class="snug"></td>';
			}
			
			//Filters			
			foreach($this->headerData as $col){
						
				$field = $col['col'];
				print '<td>';
				
				if(in_array($field,$filterA)){
					
					
					if($filterWhere != ''){
						$where = 'WHERE ' . $filterWhere;
					}else{
						$where = '';
					}
										
					if($q_select = $this->db->query("SELECT DISTINCT `$field` FROM `$table` $where ORDER BY `$field`")){
						$onchange='onchange="' . $controller . '.setFilter(\''. $field . '\',this);"';
						print "<select id=\"filter_$field\" $onchange>";
						print '<option value="">All</option>';
						
						
						foreach($q_select AS $row){
							
							$sel = '';
							
							if(isset($_REQUEST['filter_'.$field])){
								if($_REQUEST['filter_'.$field] == $row[$field]){
									$sel = 'selected="selected"';
								}
							}
							
							$tv = $this->cms->formatCol($field,$row[$field],$table);
							$q_c = $this->db->query("SELECT * FROM cms_cols WHERE column_name = '$field'");
							
							if($q_c){				
								$q_col = Utils::checkArray($q_c,array('table_name'=>$table));
								if(!$q_col){
									$q_col = Utils::checkArray($q_c,array('table_name'=>'*'));
								}
								
								if($q_col){
									if($q_col['filter'] != ''){
										$tA = Utils::parseConfig($q_col['filter']);
										if(isset($tA['filter_length'])){
											if(strlen(strip_tags($tv)) > $tA['filter_length']){
												$tv = substr(strip_tags($tv),0,$tA['filter_length']) . '...';
											}
										}
									}
								}
								
								
							}
							
							print '<option value="'. $row[$field] . '"' . $sel . '>' . $tv . '</option>';
						
						}
						print "</select>";
					}
					
				}
				
				print '</td>';
			
			}
		
			print '
			</tr>';
			
			}
				
			print '<tr>';
				
				if($options['mode'] == 'ajax' && $delete_allowed){
					print '<th class="snug">action</th>';
				}
		
		foreach($this->headerData as $col){
						
			$field = $col['col'];
			
			
			if($sort_col == $field){
					
					
				if($sort_dir == '' || $sort_dir == 'DESC'){
					$tDir = "ASC";
					$dir = "descending";
				}
				if($sort_dir == "ASC"){
					$tDir = "DESC";
					$dir = "ascending";
				}
				
				$click = 'onclick = "' . $controller . '.sortColumn(\'' . $col['col'] . '\',\'' . $tDir . '\');"';
				print "<th class=\"active $dir\" ><a href=\"#\" $click>$field</a></th>";
				
			}else{
				if(isset($col['injected'])){
					print '<th>' .$field.'</th>';
				}else{
					$click = 'onclick = "' . $controller . '.sortColumn(\'' . $col['col'] . '\',\'ASC\');"';
					print "<th><a href=\"#\" $click>$field</a></th>";
				}
				
			}
					
		}
		
		print '</tr>
		</thead>		
		<tbody id="table_' . $table . '" class="records">';
		
		$i = 0;
		
		
		
 		if($this->recordSet){
			foreach($this->recordSet as $key => $row){
							
				$t = $i%2;
				
				$classNames = array();
				
				if($t == 0){ $classNames[] = "odd"; }
				
				if($this->cms->session->privs('update',$table) == false){
					$click = '';
					$classNames[] = "locked";
				}
				
				if(isset($row['active']['value'])){
					if($row['active']['value'] == 'false'){
						$classNames[] = 'inactive';
					}	
				}
				
				//Create Action Column
				$class = 'class="' . join($classNames," ") . '"';
				
				print "<tr $class>";
				
				if($options['mode'] == 'ajax' && $delete_allowed){
					print'
					<td class="snug">
					<a class="icon delete" href="#" onclick="CMS.deleteRecord(\'' . $table . '\',\'' . $key . '\',\'' . $name_space . '\');"></a>
					</td>';
				}
				
				
				if($options['mode'] == 'ajax'){
					//format and display column data
					$click = 'onclick="' . $controller . '.editRecord(\''. $key . '\',this);"';
				}else{
					$click = '';
				}
				
						
				//display column data
				foreach($row as $key => $col){
					$data = $col['value'];
					$classNames = Array();
					
					if($key == $sort_col){ $classNames[] = "active"; }
					
					//
					if(isset($col['injected'])){ $classNames[] = "snug"; }
					
					$class = 'class="' . join($classNames," ") . '"';
					print "<td $click $class>$data</td>";
					
				}
				
				print "</tr>";
				$i++;
			
			}
				
		}
		print "</tbody></table>";
	
	}
	
	
	private function getFilters($col = ''){
		$r = '';
		foreach($this->filtersA as $filter){
			if($filter['col'] != $col){
				$r .= "&amp;filter_" . $filter['col'] . "=" . $filter['value'];
			}
		}
		return $r;	
	}	
	
	

}
?>