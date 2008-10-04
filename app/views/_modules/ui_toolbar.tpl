<div class="bb_toolbar <?= $type ?>">
	<h1><?php if($type == 'user'): ?>Edit User Account<?php else: ?>
	<?= ($mode == 'edit' ? 'Edit: ' . Utils::titleCase(Utils::singulizer(str_replace('_',' ',$table))) . ' ' . $id : 'New ' . Utils::titleCase(Utils::singulizer(str_replace('_',' ',$table)))) ?>
	<?php endif ?>
	</h1>
	<div class="bb_toolbar_actions">
		<!-- set up change handler to set main_active to this value upon change.. ehh -->
		<?php if($active != null): ?>
		<select id="active_<?= $name_space ?>" onchange="">
			<option value="1">Active</option>
			<option value="0"<?= ($active ? '' : ' selected="selected"') ?>>Inactive</option>
		</select>
		<?php endif ?>
		<?php if($type == 'main'): ?>		
		<input type="button" value="Save" onclick="blackbird.submitMain('<?= $name_space ?>'); return false;" />
	 	<input type="button" value="Close" onclick="window.location='<?= BASE ?>table/browse/<?= $table  ?>'; return false;" />
		<?php elseif($type == 'related'): ?>
		<input type="button" value="Save" onclick="blackbird.submitRelated('<?= $name_space ?>'); return false;" />
		<input type="button" value="Close" onclick="blackbird.closeRecord('<?= $name_space  ?>'); return false;" />
		<?php elseif($type == 'user'): ?>
		<input type="button" value="Save" onclick="blackbird.submitMain('<?= $name_space ?>'); return false;" />	
		<input type="button" value="Close" onclick="window.location='<?= BASE ?>'; return false;" />	
		<?php endif ?>
	</div>
</div>
