<div class="data_grid_embed">
	
<div class="actions">
	<div class="right">
		<p class="actions related">
			<input class="search" id="main_search" type="text" value="Search..." size="20" onclick="clickclear(this, 'Search...')" onblur="clickrecall(this,'Search...')"  />
			<a class="icon search" href="#" onclick="data_grid_main.search();" title="Submit search query">Search</a>
			<a class="icon undo" href="#" onclick="data_grid_main.reset();" title="Reset Data Grid">Reset</a>
		</p>
		<p class="pagination"><span class="values"><?php print count($rowData) ?> Records</span></p>
	</div>
	<div class="clearfix"></div>
</div>
	
	
<table class="data_grid">
<thead><tr>
<?php
foreach($headerData as $row){
	print '<th>'.$row.'</th>';
}
?>
</tr></thead>
<tbody>
<?php
$i=0;
foreach($rowData as $row){
	$class = '';
	if($i%2 == 0){ $class = ' class="odd" ';}
	print '<tr'.$class.'>';
	$click = ' onclick="window.location=\''. BASE .'record/edit/'.$table.'/'.$row['id'].'\';" ';
	foreach($row as $column){
		print '<td'.$click.'>'.$column.'</td>';
	}
	print '</tr>';
	$i++;
}
?>
</tbody>
</table>
</div>