<div class="bb_toolbar <?= $type ?>">
	<h1><?= ($mode == 'edit' ? 'Edit ' . Utils::titleCase(Utils::singulizer(str_replace('_',' ',$table))) . ': ' . $id : 'New ' . Utils::titleCase(Utils::singulizer(str_replace('_',' ',$table)))) ?></h1>
	<div class="bb_toolbar_actions">
		<!-- set up change handler to set main_active to this value upon change.. ehh -->
		<?php if($type == 'main'): ?>
	 	<a href="<?= BASE ?>table/browse/<?= $table  ?>">« Back to browse</a>&nbsp;&nbsp;
		<input type="button" value="Save Record" onclick="blackbird.submitMain('<?= $name_space ?>'); return false;" />
		&nbsp;&nbsp;<a href="#" title="only shows up when editing existing record and changes have been made">Revert</a>
		<?php else: ?>
		<a href="#close" onclick="blackbird.closeRecord('<?= $name_space  ?>'); return false;">Close</a>&nbsp;&nbsp;
		<input type="button" value="Save" onclick="blackbird.submitRelated('<?= $name_space ?>'); return false;" />
		&nbsp;&nbsp;<a href="#" title="only shows up when editing existing record and changes have been made">Revert</a>
		<?php endif ?>
	</div>
</div>
