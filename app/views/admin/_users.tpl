<div class="bb_dash">
	<div class="titlebar">
		<h2>Users</h2>
	</div>
	<div class="content">
		<p style="padding-left:10px;padding-top:10px;">This text needs to explain the way in which users work, that they can belong to multiple groups if necessary, etc.</p>
		<p style="padding-left:10px;padding-top:10px;"><input type="button" value="+ Add User" onclick="window.location='<?= BASE ?>record/add/<?= BLACKBIRD_USERS_TABLE ?>';" />
			&nbsp;&nbsp;<a href="<?= BASE ?>table/browse/<?= BLACKBIRD_USERS_TABLE ?>">Browse Users</a>
		</p>
		<table>
			<thead>
				<tr>
					<th>Name</th>
					<th>Activity</th>
					<th>Groups</th>
				</tr>
			</thead>
			<tbody>
			<?php $i=0; ?>
			<?php $data = Utils::arraySort($data,'name'); ?>
			<?php foreach($data as $row): ?>
			<tr class="<?= $i++%2 ? 'even' : 'odd' ?>">
				<td><img class="gravatar" alt="Gravatar" title="Gravatar" src="<?= $row['gravatar'] ?>16" />&nbsp;<a href="<?= BASE ?>record/edit/<?= BLACKBIRD_USERS_TABLE ?>/<?= $row['user_id'] ?>"><?= $row['name'] ?></a></td>
				
				<?php if($row['activity'] != ''): ?>
					<?php $diff = Utils::getTimeDifference($row['activity'],Utils::now()) ?>
					<?php if($diff['days'] >= 30): ?>
					<td>Latest activity over a month ago</td>
					<?php elseif($diff['days'] >= 1): ?>
					<td>Latest activity <?= $diff['days'] ?> days ago</td>
					<?php elseif($diff['hours'] >= 1): ?>
					<td>Latest activity <?= $diff['hours'] ?> hours ago</td>
					<?php elseif($diff['minutes'] >= 1): ?>
					<td>Latest activity <?= $diff['minutes'] ?> minutes ago</td>
					<?php else: ?>
					<td>Latest activity moments ago</td>
					<?php endif ?>
				<?php else: ?>
				<td>Hasn't logged in yet</td>
				<?php endif ?>
				<td><?= $row['groups'] ?></td>
			</tr>
			<?php endforeach ?>
			</tbody>
		</table>	
	</div>
</div>
