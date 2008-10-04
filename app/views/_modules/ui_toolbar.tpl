<div class="bb_toolbar <?= $type ?>">
	<h1><?php if($type == 'user'): ?>Edit User Account<?php else: ?>
	<?= ($mode == 'edit' ? 'Edit: ' . Utils::titleCase(Utils::singulizer(str_replace('_',' ',$table))) . ' ' . $id : 'New ' . Utils::titleCase(Utils::singulizer(str_replace('_',' ',$table)))) ?>
	<?php endif ?>
	</h1>
	<div class="bb_toolbar_actions">
		<?php if($type == 'main'): ?>
	 	<a href="<?= BASE ?>table/browse/<?= $table  ?>">« Back to browse</a>&nbsp;&nbsp;
		<input type="button" value="Save Record" onclick="blackbird.submitMain('<?= $name_space ?>'); return false;" />
		&nbsp;&nbsp;<a href="#" title="only shows up when editing existing record and changes have been made">Revert</a>
		<?php elseif($type == 'related'): ?>
		<a href="#close" onclick="blackbird.closeRecord('<?= $name_space  ?>'); return false;">Close</a>&nbsp;&nbsp;
		<input type="button" value="Save" onclick="blackbird.submitRelated('<?= $name_space ?>'); return false;" />
		&nbsp;&nbsp;<a href="#" title="only shows up when editing existing record and changes have been made">Revert</a>
		<?php elseif($type == 'user'): ?>
		<input type="button" value="Save" onclick="blackbird.submitMain('<?= $name_space ?>'); return false;" />	
		<input type="button" value="Close" onclick="window.location='<?= BASE ?>'; return false;" />
		<?php endif ?>
	</div>
</div>
