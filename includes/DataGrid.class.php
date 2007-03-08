<?php

class DataGrid
{
	
	private $cms;
	private $filtersA;
	private $recordSet;
	private $headerData;
	
	function __construct($_parent)
	{
		$this->cms = $_parent;
		$this->buildPage();
	}	
	
	function buildPage()
	{
				
		//init variables for page view
		$table = $this->cms->table;
		$sort_col = Utils::setVar("sort_col","id DESC");
		$sort_dir = Utils::setVar("sort_dir");
		$sort_index = Utils::setVar("sort_index","0");
		$search = Utils::setVar("search");
			
		$privs_browse = $this->cms->session->privs("browse",$table);
		$privs_update = $this->cms->session->privs("update",$table);
		$privs_insert = $this->cms->session->privs("insert",$table);
		$privs_delete = $this->cms->session->privs("delete",$table);
		
		$action_enabled = false;
		
		if($privs_update || $privs_delete){
			$action_enabled = true;
		}
		
		if(!$privs_browse){
			die("You don't have sufficient privileges to view this table");
		}
		
		//handle the viewing preferences (into the Session)
		if($this->cms->session->getVar('sort_max') != CMS_DATA_GRID_SORT_MAX){
			$t = CMS_DATA_GRID_SORT_MAX;
		}else{
			$t = $this->cms->session->getVar('sort_max');
		}
		$sort_max = Utils::setVar("sort_max",$t);
		if (isset($_REQUEST['sort_max'])) $this->cms->session->setVar('sort_max',$_REQUEST['sort_max']);
				
		//Get the default cols for display
		$q_display = Db::queryRow("SELECT * FROM `cms_tables` WHERE table_name = '$table' AND display_mode = 'data_grid'");
		
		if(!$q_display){
			$q_display = Db::queryRow("SELECT * FROM `cms_tables` WHERE table_name = '$table' AND display_mode = ''");
		}
		
		if($this->cms->session->super_user === true && $q_display['cols_default'] == ""){
			$q_display['cols_default'] = '*';
			
		}
		
		if($q_display['cols_default'] == ""){
			die();	
		}else{
			$select_cols = $q_display['cols_default'];
			$fields = explode(",",$select_cols);
			
			if($select_cols == "*"){
				$q = Db::query("SHOW COLUMNS FROM `$table`");
				$fields = array();
				for($i=0;$i<count($q);$i++){
					$row = $q[$i];
					$fields[] = $row[0];
				}
			}
			
			if($sort_col == ""){
				if(isset($q_display['sort_default'])){
					$sort_col = $q_display['sort_default'];
				}else{
					
				}
			}
		}
		
		if($search != ""){
						
			$q = Db::query("SHOW COLUMNS FROM $table");
			$search_fields = array();
			for($i=0;$i<count($q);$i++){
				$row = $q[$i];
				$search_fields[] = $row[0];
			}
			
			//Generate search
			$mySearch = "'%" . mysql_real_escape_string(stripslashes(trim($search))) . "%'";
			$rSearch = Db::generateSearch($search_fields,$mySearch);
		}
		
				
		$filterA = array();
		$this->filtersA = array();
		$whereA = array();
		$where = 'WHERE ';
		
		$q_total = Db::query("SELECT id FROM $table");
		
		$q_filters = Db::query("SELECT column_name FROM cms_cols WHERE (table_name = '*' OR table_name = '$table') AND filter != ''");
		if($q_filters){
			//loop through and find intersections
			foreach($q_filters as $filter){
				$col = $filter['column_name'];
				if(in_array($col,$fields)){
					$filterA[] = $col;
					if(isset($_REQUEST['filter_'.$col])){
						$t = $_REQUEST['filter_'.$col];
						$whereA[] = "$col = '$t'";
						$this->filtersA[] = array('col'=>$col,'value'=>$t);
					}
				}
			}
		}		
		if(count($whereA) > 0){
			$where .= implode(' AND ',$whereA);
		}else{
			$where = '';
		}
						
		if($search == ""){
			$query_data = Db::query("SELECT $select_cols FROM $table $where ORDER BY $sort_col $sort_dir LIMIT $sort_index, $sort_max");
			$rT = count($query_data);
			$q2 = Db::query("SELECT id FROM $table $where");
		}else{
			
			if($where == ''){
				$where = 'WHERE ';
			}
			
			if($where != 'WHERE ' && $rSearch != ''){
				$where .= ' AND (';
				$rSearch = $rSearch . ')';
			}
						
			$query_data = Db::query("SELECT $select_cols FROM $table $where $rSearch ORDER BY $sort_col LIMIT $sort_index, $sort_max");
			$rT = count($query_data);
			$q2 = Db::query("SELECT id FROM $table $where $rSearch");
						
		}
		
		if($q2){
			$rows_total = count($q2);
		}else{
			$rows_total = 0;
		}
		
		
		$css = '';
		$js = '';
				
		$q_headers = Db::queryRow("SELECT * FROM cms_headers WHERE table_name = '*' AND mode = 'data_grid'");
		if($q_headers['javascript'] != ''){
			$js .= $q_headers['javascript'];
		}
		if($q_headers['css'] != ''){
			$css .= $q_headers['css'];
		}
		$q_headers = Db::queryRow("SELECT * FROM cms_headers WHERE table_name = '$table' AND mode = 'data_grid'");
		if($q_headers['javascript'] != ''){
			$js .= $q_headers['javascript'];
		}
		if($q_headers['css'] != ''){
			$css .= $q_headers['css'];
		}
		
		//$q_help = Db::queryRow("SELECT help FROM cms_tables WHERE table_name = '$table'");
		//$q_help['help']		
		$this->cms->buildHeader($js,$css,' class="data_grid"');
		
		//Search Bar
		
		//if returning from the edit pages - 
		/*
		print '
		<div id="message" >
			<div id="message_content"><a href="#">Record #141</a> was last updated</div>
		</div>';
		*/
		
		
		
		if(isset($this->cms->pathA[2])){
			$tA = explode('_',$this->cms->pathA[2]);
			if($tA[0] == 'saved'){
				print '<div id="message_content" class="nude" >Record ' . $tA[1] . ' was saved successfully. <a href="#" onclick="CMS.closeMessage();">Close</a></div>';
			}
		}
		
		
		
		print '
		<div class="actions">
			<div class="left">';
		if($this->cms->session->privs('insert',$table)){	
			print '<p class="actions"><a class="icon new" href="' . CMS_ROOT . 'add/' . $table .  '" title="Add new record">New Record</a></p>';
		}
		print '
			</div>
			<div class="right">';
			
		Forms::init(CMS_ROOT . "browse/$table/","searchrec","class=\"text\"","get");
		Forms::hidden("sort_col",$sort_col);
		
		//loop through filtersA
		if(isset($this->filtersA)){
			foreach($this->filtersA as $filter){
				print Forms::hidden('filter_' . $filter['col'],$filter['value'],array('omit_id'=>true));
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
		
		print '<input class="text" type="text" id="search" name="search" value="' . $s_value . '" size="20" onclick="clickclear(this, \'Search...\')" onblur="clickrecall(this,\'Search...\')" />';
		print '<a class="icon search" href="#" onclick="CMS.searchDataGrid();" title="Submit search query">Search</a>';
		
		print '<a class="icon undo" href="#" onclick="window.location = \'' . CMS_ROOT . 'browse/' . $table . '\';" title="Reset Data Grid">Reset</a>';

				
		print '</form>';
		
		$sort_base = CMS_ROOT . "browse/$table/?sort_col=$sort_col&amp;sort_dir=$sort_dir&amp;sort_max=$sort_max&amp;search=$search";
		$sort_url = $sort_base . $this->getFilters() . "&amp;sort_index=";
		
		//Pagination
		if($rows_total > $sort_max){
			
			$rem = ceil($rows_total / $sort_max);
			$lastp = floor($rows_total / $sort_max);
			$sort_t = ($sort_index / $rows_total);
			
			$p = floor($rem * $sort_t);
			
			
			print '<p class="pagination">';										
			
			printf('<a class="icon first" %s title="First page">First</a>', ($p != 0) ? 'href="'. $sort_url . '0"' : '');
			printf('<a class="icon previous" %s title="Previous page">Previous</a>', ($p != 0) ? 'href="' . $sort_url . (($p - 1) * $sort_max).'"' : '' );
										
			//Record display info
			$t = $sort_index + $sort_max;
			if($t > $rows_total){
				$t = $rows_total;
			}
			
			printf('<span class="values">%s</span>',"($sort_index-$t) of $rows_total Records");
			printf('<a class="icon next" %s title="Next page">Next</a>', ($p < $rem - 1) ? 'href="'. $sort_url . (($p + 1) * $sort_max).'"' : '');
			printf('<a class="icon last" %s title="Last page">Last</a>', ($p < $rem - 1) ? 'href="'. $sort_url . ($lastp * $sort_max) . '"' : '');
			
			print '</p>';
				
			
		}else{
		
			print '<p class="pagination"><span class="values">' . $rows_total . ' Records</span></p>';
		
		}
		
		
		
		
		print "\n" . '</div><div class="clearfix"></div>
		</div>
		<div id="content">';
		
		
		
		print '		
		<div id="main">';
		
		
		
		//build recordSet and headerData
		if($query_data){
			foreach($query_data as $row){
				
				$tA = array();
				$rowData = array();
				
				for($j=0;$j<count($fields);$j++){
					$data = $this->cms->formatCol($fields[$j],$row[$j],$table);
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
		
		
		//Data Grid
		print '
		<table class="data_grid">
			<thead class="header">';
			
			if(count($filterA) > 0){
			print '
			<tr class="filter">';
			if($action_enabled){
				print '<td class="snug"></td>';
			}
			
			foreach($this->headerData as $col){
						
				$field = $col['col'];
				print '<td>';
				
				if(in_array($field,$filterA)){
					
					$q_select = Db::query("SELECT DISTINCT $field FROM $table ORDER BY $field");
					$sort_url = $sort_base . $this->getFilters($field) . "&amp;sort_index=";

					print "<select id=\"filter_$field\" onchange=\"CMS.setFilter(this,'$sort_url" . $sort_index . "');\" >";
					print '<option value="">All</option>';
										
					foreach($q_select AS $row){
						
						$sel = '';
						
						if(isset($_REQUEST['filter_'.$field])){
							if($_REQUEST['filter_'.$field] == $row[$field]){
								$sel = 'selected="selected"';
							}
						}
						
						$tv = $this->cms->formatCol($field,$row[$field],$table);
						$q_c = Db::query("SELECT * FROM cms_cols WHERE column_name = '$field'");
						
						if($q_c){				
							$q_col = Utils::checkArray($q_c,array('table_name'=>$table));
							if(!$q_col){
								$q_col = Utils::checkArray($q_c,array('table_name'=>'*'));
							}
							
							if($q_col){
								if($q_col['filter'] != ''){
									$tA = Utils::parseConfig($q_col['filter']);
									if(isset($tA['filter_length'])){
										$tv = substr(strip_tags($tv),0,$tA['filter_length']) . '...';
									}
								}
							
							}
						}
						
						print '<option value="'. $row[$field] . '"' . $sel . '>' . $tv . '</option>';
					}
					
					print "</select>";
					
				}
				
				print '</td>';
			
			}
		
			print '
			</tr>';
			
			}
			
			print '			
			<tr>';
			
			if($action_enabled){
				print '<th class="snug">action</th>';
			}
		
			//Column Sorting
			
			
			foreach($this->headerData as $col){
						
				$field = $col['col'];
				$col_label = $field;
				$q_label = Db::queryRow("SELECT data_grid_name FROM cms_cols WHERE (table_name = '*' OR table_name = '$table') AND column_name = '$field'");
				if($q_label['data_grid_name'] != ''){
					$col_label = $q_label['data_grid_name'];
				}
				
				if($sort_col == $field){
					
					
					if($sort_dir == '' || $sort_dir == 'DESC'){
						$tDir = "ASC";
						$dir = "descending";
					}
					if($sort_dir == "ASC"){
						$tDir = "DESC";
						$dir = "ascending";
					}
									
					print "<th class=\"active $dir\" ><a href=\"" . CMS_ROOT . "browse/$table/?sort_col=$field&amp;sort_dir=$tDir&amp;sort_max=$sort_max&amp;search=$search" . $this->getFilters() . "\">$col_label</a></th>";
				}else{
					
					if(isset($col['injected'])){
						print "<th>$field</th>";
					}else{
						print "<th><a href=\"" . CMS_ROOT . "browse/$table/?sort_col=$field&amp;sort_dir=ASC&amp;sort_max=$sort_max&amp;search=$search" . $this->getFilters() . "\">$col_label</a></th>";
					}
				}
			}
			
		print '</tr>
		</thead>';
		
		
		
		$i = 0;
		
		//Recordset
		if($this->recordSet){
		print '<tbody class="records">';
			foreach($this->recordSet as $key => $row){
							
				$t = $i%2;
		
				$click = 'onclick="window.location = \'' . CMS_ROOT . 'edit/' . $table . '/' . $key . '\';"';
				
				$classNames = array();
				
				if($t == 0){
					$classNames[] = 'odd';
				}
				
				
				if($this->cms->session->privs('update',$table) == false){
					//$click = '';
					$classNames[] = 'locked';				
				}
				
				if(isset($row['active']['value'])){
					if($row['active']['value'] == 'false'){
						$classNames[] = 'inactive';
					}	
				}
				
				$class = 'class="' . join($classNames," ") . '"';
				
				//Create Action Column
				
				print "<tr $class>";
				
				if($action_enabled){
				
					print '
					<td class="snug">
					<input type="checkbox" name="check_' . $key . '" class="data_grid_checkbox" id="check_' . $key . '" onclick="CMS.registerClick(this);" />
					</td>';
				
				}
			
				//display column data
				foreach($row as $key => $col){
					$data = $col['value'];
					
					
					if($key == $sort_col){
						$class = 'class="active"';
					}else{
						$class = '';
					}
					
					//class="snug"
					(isset($col['injected'])) ? $class = '' : $class = $class;
					
					print "<td $click $class>$data</td>";
				}
				
				print "</tr>";
				$i++;
			
			}
			print '</tbody>';
				
		}
		print "</table>";
		
		print '
		<div class="actions">
			<div class="left">';
			
			if($query_data){
			
			if($action_enabled){
			
			print'
			<select name="batchProcess" id="batchProcess" onchange="CMS.batchProcess(\'' . $table . '\');">
				<option value="" id="selection_set">With Selected</option>';
				
				if($this->cms->session->privs('update',$table)){
				print '
				<option value="active_true">-Active True</option>
				<option value="active_false">-Active False</option>';
				}
				if($this->cms->session->privs('delete',$table)){
				print '
				<option value="delete">-Delete</option>';
				}
			
			print '	
			</select>	
			<a href="#" onclick="CMS.checkAll(true); return false;">Check All</a>
			<a href="#" onclick="CMS.checkAll(false); return false;">Un-Check All</a>';
			
			}
			
			}
			
			print'
		</div>
		<div class="right">';
		
		print 'Records <select name="sort_max" onchange="CMS.viewRows(this,\'' . CMS_ROOT . "browse/$table/?sort_col=$sort_col&amp;search=$search&amp;sort_index=$sort_index" . $this->getFilters() . '\');" >';
		
		$sort_maxList = array(10,20,50,100,250,500,10000);
		foreach($sort_maxList as $sort_row){
			if($sort_row == $sort_max){
				$selected = "selected=\"selected\"";
			}else{
				$selected = "";
			}
			$sort_label = $sort_row;
			if($sort_label == 10000){
				$sort_label = 'ALL';
			}
			
			print "<option value=\"$sort_row\" $selected>$sort_label</option>";
		}
		
		print '
		</select>	
		</div>
		<div class="clearfix"></div>';
		
		if(!$query_data && $q_total){
			print '<div class="error">';
			print 'There are no records matching this criteria. Reset Data Grid.';
			print '<a class="icon undo" href="#" onclick="window.location = \'' . CMS_ROOT . 'browse/' . $table . '\';" title="Reset Data Grid">Reset</a>';
			print '</div>';
		}
		
		
		print "</div>";
		
		print '
		<script type="text/javascript">
			<!-- <![CDATA[ 
			if($("toggle_help")){
				Event.observe("toggle_help", "click", function(){toggleHelp(); return false; }, true);
			}
			// ]]> -->
		</script>';
		print '</div>';
		
		$this->cms->buildFooter();
	
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