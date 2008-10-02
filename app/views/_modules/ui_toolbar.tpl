<div class="bb_toolbar <?= $type ?>">
	<h1><?= ($mode == 'edit' ? 'Edit ' . $table . ': ' . $id : 'New ' . $table . ' record') ?></h1>
	<?php if($mode == 'edit'): ?>
	<dl class="bb_record_meta">
		<?php if(isset($created)): ?>
		<dt>Created</dt>
		<dd><?= $created ?></dd>
		<?php endif ?>
		<?php if(isset($modified)): ?>
		<dt>Modified</dt>
		<dd><?= $modified ?></dd>
		<?php endif ?>
	</dl>
	<?php endif ?>
	<div class="bb_toolbar_actions">
		<!-- set up change handler to set main_active to this value upon change.. ehh -->
		<?php if($active != null): ?>
		<select id="active_<?= $name_space ?>" onchange="">
			<option value="1">Active</option>
			<option value="0"<?= ($active ? '' : ' selected="selected"') ?>>Inactive</option>
		</select>
		<?php endif ?>
		<?php if($type == 'main'){; ?>		
		<input type="button" value="Save" onclick="blackbird.submitMain('<?= $name_space ?>'); return false;" />
	 	<input type="button" value="Close" onclick="window.location='<?= BASE ?>table/browse/<?= $table  ?>'; return false;" />
		<?php }else{ ?>
		<input type="button" value="Save" onclick="blackbird.submitRelated('<?= $name_space ?>'); return false;" />
		<input type="button" value="Close" onclick="blackbird.closeRecord('<?= $name_space  ?>'); return false;" />
		<?php } ?>
	</div>
</div>
