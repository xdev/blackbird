<div class="bb_toolbar <?= $mode ?>">
	<h1><?= $table ?></h1>
	<div class="bb_toolbar_actions">
		<?php $datagrid = 'data_grid_' . $_POST['name_space'] ?>
		
		<?php if($mode == 'main'): ?>
			<input type="button" value="+ New Record" onclick="window.location='<?= BASE ?>record/add/<?= $table ?>'" />
		<?php else: ?>
			<input type="button" value="+ New Record" onclick="blackbird.addNewRecord('<?= $table ?>','<?= $_POST['name_space'] ?>');" />
		<?php endif ?>
		
			<input class="search" id="<?= $_POST['name_space'] ?>_search" type="text" value="Search..." size="20" onclick="clickclear(this, 'Search...')" onblur="clickrecall(this,'Search...')"  />
			<a class="icon search" href="#" onclick="<?= $datagrid ?>.search();" title="Submit search query">Search</a>
			<a class="icon undo" href="#" onclick="<?= $datagrid ?>.reset();" title="Reset Data Grid">Reset</a>		
		<?php
		//pagination
		
		$click = $datagrid . ".setProperty('offset','";
		
		if($rows_total > $limit){
			
			$rem = ceil($rows_total / $limit);
			$lastp = floor($rows_total / $limit);
			$sort_t = ($offset / $rows_total);

			$p = floor($rem * $sort_t);

			//print '<p class="pagination">';

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
			//print '</p>';

		}else{
			print '<span class="values">' . count($rowData) . ' Records</span>';
		}

		?>
	</div>
</div>


<div class="container data_grid_embed <?= $mode ?>">


<table class="data_grid">
	<thead>
		<?php if(count($filtersA) > 0): ?>
			<tr class="filter">
				<?php foreach($headerData as $field): ?>
					<td>
						<?php if(in_array($field,$filterA)): ?>
							<select id="filter_<?= $field ?>" onchange="<?= $datagrid ?>.setFilter('<?= $field ?>',this);">
							<option value="">All</option>
							<?php foreach($filtersA[$field]['options'] as $row): ?>
							<option value="<?= $row['value'] ?>" <?= $row['selected'] ?>><?= $row['label'] ?></option>								
							<?php endforeach ?>							
							</select>
						<?php endif ?>
					</td>
				<?php endforeach ?>
			</tr>
		<?php endif ?>
		<tr>
			<?php foreach($headerData as $field): ?>
				<?php $click = '' ?>
				<?php if($sort_col == $field): ?>
					<th class="active <?= ($sort_dir == 'DESC' || !$sort_dir ? 'descending' : 'ascending') ?>"><a href="#" onclick="<?= $datagrid ?>.sortColumn('<?= $field ?>','<?= ($sort_dir == 'DESC' || !$sort_dir ? 'ASC' : 'DESC') ?>');"><?= $field ?></a></th>
				<?php elseif(isset($col['injected'])): ?>
					<th><?= $field ?></th>
				<?php else: ?>
					<th><a href="#" onclick="<?= $datagrid ?>.sortColumn('<?= $field ?>','ASC');"><?= $field ?></a></th>
				<?php endif ?>
			<?php endforeach ?>
		</tr>
	</thead>
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