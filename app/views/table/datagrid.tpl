<div class="data_grid_embed">
	
<div class="actions">
	<div class="right">
		<p class="actions related">
			<input class="search" id="main_search" type="text" value="Search..." size="20" onclick="clickclear(this, 'Search...')" onblur="clickrecall(this,'Search...')"  />
			<a class="icon search" href="#" onclick="data_grid_main.search();" title="Submit search query">Search</a>
			<a class="icon undo" href="#" onclick="data_grid_main.reset();" title="Reset Data Grid">Reset</a>
		</p>
		<!-- insert pagination logic here -->
		<p class="pagination"><span class="values"><?php print count($rowData) ?> Records</span></p>
	</div>
	<div class="clearfix"></div>
</div>
	
	
<table class="data_grid">
<thead><tr>	
<?php
//filters

//headers
foreach($headerData as $row){
	//print '<th>'.$row.'</th>';
	$field = $row;
	$click = '';
	if($sort_col == $field){			
		if($sort_dir == '' || $sort_dir == 'DESC'){
			$tDir = "ASC";
			$dir = "descending";
		}
		if($sort_dir == "ASC"){
			$tDir = "DESC";
			$dir = "ascending";
		}
		//$click = 'onclick = "' . $controller . '.sortColumn(\'' . $col['col'] . '\',\'' . $tDir . '\');"';
		print "<th class=\"active $dir\" ><a href=\"#\" $click>$field</a></th>";
	}else{
		//if(isset($col['injected'])){
		//	print '<th>' .$field.'</th>';
		//}else{
			//$click = 'onclick = "' . $controller . '.sortColumn(\'' . $col['col'] . '\',\'ASC\');"';
			print "<th><a href=\"#\" $click>$field</a></th>";
		//}
	}	
}
?>
</tr></thead>
<tbody>
<?php
//record set body... really simple
$i=0;
foreach($rowData as $key=>$value){
	//take into account permissions	
	$class = '';
	$click = ' onclick="window.location=\''. BASE .'record/edit/'.$table.'/'.$key.'\';" '; //replace with non obtrusive javascript
	
	if($i%2 == 0){ $class = ' class="odd" ';}
	print '<tr'.$class.'>';
	foreach($value as $column){
		print '<td'.$click.'>'.$column['value'].'</td>';
	}
	print '</tr>';
	$i++;
}
?>
</tbody>
</table>
</div>