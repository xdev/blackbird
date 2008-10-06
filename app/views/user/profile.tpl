<div class="bb_toolbar">
	<h1>Viewing profile for <?= $record['firstname'] . ' ' . $record['lastname'] ?></h1>
	<div class="bb_toolbar_actions">
		<span><?= $record['email'] ?></span>
	</div>
</div>
<div id="bb_sections">
<div class="section">
<div class="container">
<p>Latest 50, <a href="#">Click here to view entire history</a></p>
<div class="bb_dash">
	<div class="titlebar">
		<h2>Activity</h2>
	</div>
	<div class="content">
<table class="data_grid" >
	<thead>
		<tr>
			<th>Action</th>
			<th>Table</th>
			<th>Record (Key)</th>
			<th>Time</th>
		</tr>
	</thead>
	<tbody>
	<?php $i=0; ?>
	<?php foreach($history as $row): ?>
	<tr class="<?= $i++%2 ? 'even' : 'odd' ?>">
		<td class="<?= $row['action'] ?>"><?= $row['action'] ?></td>
		<td><a href="<? BASE ?>table/browse/<?= $row['table_name'] ?>"><?= $row['table_name'] ?></a></td>
		<?php if($row['action'] != 'delete'): ?>
		<td><a href="<? BASE ?>record/edit/<?= $row['table_name'] ?>/<?= $row['record_id'] ?>"><?= $row['record_id'] ?></td>
		<?php else: ?>
		<td><?= $row['record_id'] ?></td>
		<?php endif ?>
		<?php $diff = Utils::getTimeDifference($row['modtime'],Utils::now()) ?>
		<?php if($diff['days'] > 30): ?>
		<td>A long time ago</td>
		<?php elseif($diff['days'] >= 1): ?>
		<td><?= $diff['days'] ?> days ago</td>
		<?php elseif($diff['hours'] >= 1): ?>
		<td><?= $diff['hours'] ?> hours ago</td>
		<?php elseif($diff['minutes'] >= 1): ?>
		<td><?= $diff['minutes'] ?> minutes ago</td>
		<?php else: ?>
		<td>moments ago</td>
		<?php endif ?>		
	</tr>
	<?php endforeach ?>
	</tbody>
</table>
</div>
</div>
</div>
</div>
</div>