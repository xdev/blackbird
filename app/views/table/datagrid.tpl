<div class="bb_toolbar <?= $mode ?>">
	<h1>Browse: <?= Utils::titleCase(str_replace('_',' ',$table)) ?></h1>
	<div class="bb_toolbar_actions">
		<?php $datagrid = 'data_grid_' . $_POST['name_space'] ?>
		
		<?php if($mode == 'main'): ?>
		<input type="button" value="+ New Record" onclick="window.location='<?= BASE ?>record/add/<?= $table ?>'" />
		<?php else: ?>
		<input type="button" value="+ New Record" onclick="blackbird.addNewRecord('<?= $table ?>','<?= $_POST['name_space'] ?>');" />
		<?php endif ?>
		<input class="search" id="<?= $_POST['name_space'] ?>_search" type="text" value="<?= ($search == '') ? 'Search...' : $search ?>" size="20" onclick="clickclear(this, 'Search...')" onblur="clickrecall(this,'Search...')"  />
		<a class="icon search" href="#" onclick="<?= $datagrid ?>.search();" title="Submit search query">Search</a>
		<a class="icon undo" href="#" onclick="<?= $datagrid ?>.reset();" title="Reset Data Grid">Reset</a>		
		
	</div>
</div>

<div class="container data_grid_embed <?= $mode ?>">
	<table class="data_grid">
		<thead>
			<tr>
				<?php foreach($headerData as $field): ?>
				<?php if($sort_col == $field): ?>
				<th class="active <?= ($sort_dir == 'DESC' || !$sort_dir ? 'descending' : 'ascending') ?>"><a href="#" onclick="<?= $datagrid ?>.sortColumn('<?= $field ?>','<?= ($sort_dir == 'DESC' || !$sort_dir ? 'ASC' : 'DESC') ?>');"><?= $field ?></a></th>
				<?php elseif(isset($col['injected'])): ?>
				<th><?= $field ?></th>
				<?php else: ?>
				<th><a href="#" onclick="<?= $datagrid ?>.sortColumn('<?= $field ?>','ASC');"><?= $field ?></a></th>
				<?php endif ?>
				<?php endforeach ?>
			</tr>
			<?php if(count($filtersA) > 0): ?>
			<tr class="filter">
				<?php foreach($headerData as $field): ?>
				<td class="<?= $sort_col == $field ? 'active' : '' ?>">
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
		</thead>
		<tbody>
			<?php $i=0 ?>
			<?php foreach($rowData as $key=>$value): ?>
			<tr class="<?= $i++%2 ? 'even' : 'odd' ?>">
				<?php foreach($value as $column): ?>
				<?php if($mode == 'related'): ?>
				<td class="<?= $column['col'] == $sort_col ? 'sort' : '' ?>" onclick="<?= $datagrid ?>.editRecord(<?= $key ?>,this)">
				<?php else: ?>
				<td class="<?= $column['col'] == $sort_col ? 'sort' : '' ?>" onclick="window.location='<?= BASE ?>record/edit/<?= $table ?>/<?= $key ?>';">
				<?php endif ?>
					<?= $column['value'] ?>
				</td>
				<?php endforeach ?>
			</tr>
			<?php endforeach ?>
		</tbody>
	</table>
</div>

<div class="datagrid_navigation">
	<?php if($rows_total > $limit): ?>
	
	<?php
		$rem    = ceil($rows_total / $limit);
		$lastp  = floor($rows_total / $limit);
		$sort_t = ($offset / $rows_total);
		$p      = floor($rem * $sort_t);
	?>
	
	<?php if($p): ?>
	<a class="icon first" href="#" onclick="<?= $datagrid ?>.setProperty('offset','0');" title="First page">First</a>
	<a class="icon previous" href="#" onclick="<?= $datagrid ?>.setProperty('offset','<?= (($p - 1) * $limit) ?>');" title="Previous page">Previous</a>
	<?php else: ?>
	<a class="icon first" title="You are at the first page">First</a>
	<a class="icon previous" title="You are at the first page">Previous</a>
	<?php endif ?>

	<span class="values"><?= $offset+1 ?> - <?= (($offset + $limit > $rows_total) ? $rows_total : $offset + $limit) ?> of <?= $rows_total ?> Records</span>
	
	<?php if($p < $rem - 1): ?>
	<a class="icon next" href="#" onclick="<?= $datagrid ?>.setProperty('offset','<?= (($p + 1) * $limit) ?>');" title="Next page">Next</a>
	<a class="icon last" href="#" onclick="<?= $datagrid ?>.setProperty('offset','<?= ($lastp * $limit) ?>');" title="Last page">Last</a>
	<?php else: ?>
	<a class="icon next" title="You are at the last page">Next</a>
	<a class="icon last" title="You are at the last page">Last</a>
	<?php endif ?>
		
	<?php else: ?>
	
	<span class="values"><?= count($rowData) ?> Records</span>
	
	<?php endif ?>
	<span># of Rows</span>
	<select name="limit" onchange="<?= $datagrid ?>.setLimit(this);" >
	<?php $limitList = array(10,20,50,100,250,500,10000); ?>
	<?php foreach($limitList as $sort_row): ?>
		<option value="<?= $sort_row ?>" <?= ($sort_row == $limit) ? 'selected="selected"' : '' ?>><?= ($sort_row == 10000) ? 'ALL' : $sort_row ?></option>";
	<?php endforeach ?>
	</select>
</div>
