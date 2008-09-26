<div class="bb_toolbar <?= $mode ?>">
	<h1><?= $table ?></h1>
	<div class="actions">
	<div class="left">
		<?php
		$datagrid = 'data_grid_' . $_POST['name_space'];
		if($mode == 'main'){
			print '<input type="button" value="+ New Record" onclick="window.location=\''. BASE . 'record/add/' . $table . '\'" />';
		}else{
			print '<input type="button" value="+ New Record" onclick="blackbird.addNewRecord(\'' . $table . '\',\'' . $_POST['name_space'] . '\');" />';
		}
		?>
	</div>
	<div class="right">
		<p class="actions related">
			<input class="search" id="<?= $_POST['name_space'] ?>_search" type="text" value="Search..." size="20" onclick="clickclear(this, 'Search...')" onblur="clickrecall(this,'Search...')"  />
			<a class="icon search" href="#" onclick="<?= $datagrid ?>.search();" title="Submit search query">Search</a>
			<a class="icon undo" href="#" onclick="<?= $datagrid ?>.reset();" title="Reset Data Grid">Reset</a>
		</p>
		<?php
		//pagination

		$click = $datagrid . ".setProperty('offset','";

		if($rows_total > $limit){

			$rem = ceil($rows_total / $limit);
			$lastp = floor($rows_total / $limit);
			$sort_t = ($offset / $rows_total);

			$p = floor($rem * $sort_t);

			print '<p class="pagination">';

			printf('<a class="icon first" %s title="First page">First</a>', ($p != 0) ? 'href="#" onclick="' . $click . 0 . '\');"' : '');
			printf('<a class="icon previous" %s title="Previous page">Previous</a>', ($p != 0) ? 'href="#" onclick="' . $click . (($p - 1) * $limit).'\');"' : '' );

			//Record display info
			$t = $offset + $limit;
			if($t > $rows_total){
				$t = $rows_total;
			}

			printf('<span class="values">%s</span>',"($offset-$t) of " . $rows_total . " Records");
			printf('<a class="icon next" %s title="Next page">Next</a>', ($p < $rem - 1) ? 'href="#" onclick="'. $click . (($p + 1) * $limit).'\');"' : '');
			printf('<a class="icon last" %s title="Last page">Last</a>', ($p < $rem - 1) ? 'href="#" onclick="'. $click . ($lastp * $limit) . '\');"' : '');
			print '</p>';

		}else{
			print '<p class="pagination"><span class="values">' . count($rowData) . ' Records</span></p>';
		}

		?>
	</div>
	<div class="clearfix"></div>
</div>
</div>


<div class="data_grid_embed <?= $mode ?>">


<table class="data_grid">
<thead><tr>
<?php
//filters

//datagrid javascript controller reference.. this should be removed for a non-obstrusive approach, coming later

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
		$click = 'onclick = "' . $datagrid . '.sortColumn(\'' . $field . '\',\'' . $tDir . '\');"';
		print "<th class=\"active $dir\" ><a href=\"#\" $click>$field</a></th>";
	}else{
		//if(isset($col['injected'])){
		//	print '<th>' .$field.'</th>';
		//}else{
			$click = 'onclick = "' . $datagrid . '.sortColumn(\'' . $field . '\',\'ASC\');"';
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

	if($mode == 'related'){
		$click = ' onclick="'.$datagrid.'.editRecord('.$key.',this);" ';
	}

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