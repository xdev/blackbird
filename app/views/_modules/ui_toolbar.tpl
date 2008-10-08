<div class="bb_toolbar <?= $type ?>">
	<h1><?php if($type == 'user'): ?>Edit User Account<?php else: ?>
	<?= ($mode == 'edit' ? 'Edit ' . Utils::titleCase(Utils::singulizer(str_replace('_',' ',$table))) . ' ' . $id : 'Add ' . Utils::titleCase(Utils::singulizer(str_replace('_',' ',$table)))) ?>
	<?php endif ?>
	</h1>
	<div class="bb_toolbar_actions">
		<?php if($type == 'main'): ?>
	 	<a href="#" onclick="blackbird.closeMain('<?= BASE ?>table/browse/<?= $table  ?>'); return false;">« Back to browse</a>&nbsp;&nbsp;
		<input type="button" value="Save Record" class="button_submit" <?= (($mode == 'edit') ? 'disabled="disabled"' : '') ?> onclick="blackbird.submitMain('<?= $name_space ?>'); return false;" />
		&nbsp;&nbsp;<a class="revert" href="#" style="display:none;" title="revert form" onclick="$('form_<?= $name_space ?>').reset(); return false;">Revert</a>
		<?php if($mode == 'edit'): ?>
		<a class="delete lightbox" href="#<?= BASE ?>record/delete/<?= $table ?>/<?= $id ?>" title="delete record">Delete</a>
		<?php endif ?>
		<!--onclick="blackbird.deleteRecord('<?= $table ?>','<?= $id ?>'); return false;"-->
		<?php elseif($type == 'related'): ?>
		<a href="#close" onclick="blackbird.closeRecord('<?= $name_space  ?>'); return false;">Close</a>&nbsp;&nbsp;
		<input type="button" value="Save Record" class="button_submit" <?= (($mode == 'edit') ? 'disabled="disabled"' : '') ?> onclick="blackbird.submitRelated('<?= $name_space ?>'); return false;" />
		&nbsp;&nbsp;<a class="revert" href="#" style="display:none;" title="revert form" onclick="$('form_<?= $name_space ?>').reset(); return false;">Revert</a>
		<?php if($mode == 'edit'): ?>
		<a class="delete" href="#" title="delete record" onclick="blackbird.deleteRecord('<?= $table ?>','<?= $id ?>'); return false;">Delete</a>
		<?php endif ?>
		<?php elseif($type == 'user'): ?>
		<input type="button" value="Save" onclick="blackbird.submitMain('<?= $name_space ?>'); return false;" />	
		<input type="button" value="Close" onclick="window.location='<?= BASE ?>'; return false;" />
		<?php endif ?>
	</div>
</div>
