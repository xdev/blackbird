<div class="bb_dash">
	<div class="titlebar">
		<h2>Groups</h2>
	</div>
	<div class="content">
		<p style="padding-left:10px;padding-top:10px;">
			<input type="button" value="+ Add Group" onclick="window.location='<?= BASE ?>record/add/<?= BLACKBIRD_TABLE_PREFIX ?>groups';" />
			<!--<a href="<?= BASE ?>record/add/<?= BLACKBIRD_TABLE_PREFIX ?>groups">Add New Group</a>-->
		</p>
		<table>
			<thead>
				<th>Name</th>
				<th>Users</th>
				<th>Admin</th>
			</thead>
			<tbody>
			<?php $i=0; ?>
			<?php foreach($data as $row): ?>
			<tr class="<?= $i++%2 ? 'even' : 'odd' ?>">
				<td><a href="<?= BASE ?>record/edit/<?= BLACKBIRD_TABLE_PREFIX ?>groups/<?= $row['id'] ?>"><?= $row['name'] ?></a></td>
				<td><?= rand(0,7) . ' Users' ?></td>
				<td><?= _ControllerFront::formatCol('admin',$row['admin'],'') ?></td>
			</tr>
			<?php endforeach ?>
			</tbody>
		</table>	
	</div>
</div>
