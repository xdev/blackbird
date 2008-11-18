<div class="bb_dash">
	<div class="titlebar">
		<h2>Users</h2>
	</div>
	<div class="content">
		<table>
			<tbody>
			<?php $i=0; ?>
			<?php foreach($data as $row): ?>
			<tr class="<?= $i++%2 ? 'even' : 'odd' ?>">
				<td><img class="gravatar" alt="Gravatar" title="Gravatar" src="<?= $row['gravatar'] ?>16" />&nbsp;<a href="<?= BASE ?>user/profile/<?= $row['user_id'] ?>"><?= $row['name'] ?></a></td>
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
				<td>No activity</td>
				<?php endif ?>
			</tr>
			<?php endforeach ?>
			</tbody>
		</table>	
	</div>
</div>
