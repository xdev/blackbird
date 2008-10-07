<div class="bb_dash">
	<div class="titlebar">
		<h2>Database Tables</h2>
		<a class="toggle" href="#" title="Open/Close">Toggle</a>
	</div>
	<div class="content">
		<table>
			<thead>
				<tr>
					<th>Name</th>
					<th>Records</th>
					<th>Size</th>
					<th>Last Updated</th>
				</tr>
			</thead>
			<tbody>
				<?php $i=0; ?>
				<?php foreach($data as $row): ?>
				<?php if(strpos($row['Name'], BLACKBIRD_TABLE_PREFIX) === false): ?>
				<tr class="<?= $i++%2 ? 'even' : 'odd' ?>">
					<td><a href="<? BASE ?>table/browse/<?= $row['Name'] ?>"><?= Utils::titleCase(str_replace('_',' ',$row['Name'])) ?></a></td>
					<td><?= $row['Rows'] ?></td>
					<td><?= Utils::humanFileSize($row['Data_length']) ?></td>
					<?php $diff = Utils::getTimeDifference($row['Update_time'],Utils::now()) ?>
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
				<?php endif ?>
				<?php endforeach ?>
			</tbody>
		</table>
	</div>
</div>