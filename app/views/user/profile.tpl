<div class="bb_toolbar">
	<h1>Viewing profile for <?= $record['firstname'] . ' ' . $record['lastname'] ?></h1>
	<div class="bb_toolbar_actions">
		<span><?= $record['email'] ?></span>
	</div>
</div>
<div id="bb_sections">
<div class="section">
<div id="dashboard" class="container">
	
	<?php if(isset($user_data)): ?>
	<div class="bb_dash">
		<div class="titlebar">
			<h2>Details</h2>
		</div>
		<div class="content">
			
			<?php if(isset($_GET['message'])): ?>
			<?= $this->fetchView('/_modules/_message',array(
				'class'=>'ok',
				'message'=>'Profile updated!'
				)
			) ?>	
			<?php endif ?>
			
			<div class="bb_module bb_module_edit">
			<form id="form_<?= $name_space ?>" name="form_<?= $name_space ?>" enctype="multipart/form-data" action="<?= BASE ?>user/processedit" method="post" target="form_target_<?= $name_space ?>" onsubmit="Element.show('ajax');" >
			<?php
			Forms::text($name_space . '_firstname',$user_data['firstname'],array('label'=>'First Name','validate'=>'default'));
			Forms::text($name_space . '_lastname',$user_data['lastname'],array('label'=>'Last Name','validate'=>'default'));
			Forms::text($name_space . '_email',$user_data['email'],array('label'=>'Email','validate'=>'default'));
			Forms::text($name_space . '_password_reset','',array('label'=>'Reset Password','type'=>'password'));
			?>
			<div class="form_item buttons">
				<label></label>
				<div class="input">
					<input type="button" value="submit" onclick="blackbird.submitMain('<?= $name_space ?>'); return false;" />
				</div>
			</div>
			</form>
			<iframe id="form_target_<?= $name_space ?>" name="form_target_<?= $name_space ?>" class="related_iframe"></iframe>
			</div>
		</div>
	</div>
	<?php endif ?>

<?= $this->fetchView('/dashboard/_chart_edits',array('percents'=>$chart_edits['percents'],'labels'=>$chart_edits['labels'])) ?>
<?= $this->fetchView('/dashboard/_chart_tables',array('percents'=>$chart_tables['percents'],'labels'=>$chart_tables['labels'])) ?>
<div class="bb_dash">
	<div class="titlebar">
		<h2>Recent Activity</h2>
	</div>
	<div class="content">
<table>
	<p>Latest 50, <a href="#">Click here to view entire history</a></p>
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
	<?php if(is_array($history)): ?>
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
	<?php endif ?>
	</tbody>
</table>
</div>



<script type="text/javascript">
	/*
	onrelease, send to a cookie that remembers the state and position of stuffs
	*/
	Sortable.create(
		$('dashboard'),
		{
			overlap		: "horizontal",
			tag			: 'div',
			constraint	: false,
			handle		: "titlebar"
	    }
	);
</script>

</div>
</div>
</div>
</div>