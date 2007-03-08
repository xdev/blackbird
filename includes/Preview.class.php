<?php

class Preview
{
	
	
	
	function __construct($cms,$table,$id_set,$action,$cms_page_state)
	{
		$this->cms = $cms;
		$this->table = $table;
		$this->id_set = $id_set;
		$this->action = $action;
		$this->cms_page_state = $cms_page_state;
		$this->previewTable();
	}
	
	function previewTable()
	{
	
				
		$select_cols = "*";
		$q = Db::query("SHOW COLUMNS FROM $this->table");
		
		$fields = array();
		
		for($i=0;$i<count($q);$i++){
			$row = $q[$i];
			$fields[] = $row[0];
		}
		
		if(is_array($this->id_set)){
			$id_A = implode(',',$this->id_set);
			$query_data = Db::query("SELECT * FROM $this->table WHERE id IN ($id_A)");
		}else{
			$query_data = Db::queryRow("SELECT * FROM $this->table WHERE id = '$id'");
		}
		
		print'<table class="data_grid">
				<tbody class="header">';
		
		
		print '<tr>';
			
		for($i=0;$i<count($fields);$i++){
			$field = $fields[$i];
			if($i == (count($fields) - 1)){
				$class = "class=\"last_col\"";
			}else{
				$class = "class=\"\"";
			}
			print "<th>$field</th>";
			
		}
		print '</tr>
		</tbody>
		<tbody class="data_grid">';
				
		foreach($query_data as $row){
	
		
		print "<tr>";
		
		
		for($i=0;$i<count($fields);$i++){
			$col = $row[$i];
			$data = $this->cms->formatCol($fields[$i],$col,$this->table);
			if($i == (count($fields) - 1)){
				$class = "class=\"last_col\"";
			}else{
				$class = "class=\"result_cell\"";
			}
			print "<td $class >$data</td>";
		}
		
		print "</tr>";
		
		}
		
		print "</tbody></table>";
		
		Forms::init(CMS_ROOT . "process/batch/$this->table");
		Forms::hidden("id_set",implode(',',$this->id_set));
		Forms::hidden("table",$this->table);	
		Forms::hidden("action",$this->action);
		Forms::hidden("cms_page_state",$this->cms_page_state);
	
		print '
		<div class="buttons" >
			<a class="button delete" onclick="$(myform).submit();" href="#">Delete</a>
			<a class="button cancel" onclick="history.back()" href="#">Cancel</a>
		</div>';	
		
		print "</form>";
	
	}

}
	
?>