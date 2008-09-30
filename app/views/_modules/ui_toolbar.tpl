<div class="bb_toolbar <?= $type ?>">
	<h1><?php printf(($mode == 'edit') ? 'Edit Record' : 'New Record'); ?></h1>
	<?php
	if($mode == 'edit'){
		printf('
		<dl class="bb_record_meta">
			<dt>Id</dt>
			<dd>%s</dd>
			%s
			%s
		</dl>',
		$id,
		(isset($created)) ? '<dt>Created</dt><dd>'.$created.'</dd>' : '',
		(isset($modified)) ? '<dt>Modified</dt><dd>'.$modified.'</dd>' : ''
		);
	}
	?>
	<div class="bb_toolbar_actions">
		<!-- set up change handler to set main_active to this value upon change.. ehh -->
		<select id="active_<?= $name_space ?>" onchange="">
			<?php
			if($active == 1){
				$s1 = 'selected="selected"';
				$s2 = '';
			}else{
				$s1 = '';
				$s2 = 'selected="selected"';
			}
			?>
			<option value="1" <?= $s1 ?>>Active</option>
			<option value="0" <?= $s2 ?>>Inactive</option>
	     </select>
		
		<?php if($type == 'main'){; ?>		
		  	<input type="button" value="Save" onclick="blackbird.submitMain('<?= $name_space ?>'); return false;" />
	  		<input type="button" value="Close" onclick="window.location='<?= BASE ?>table/browse/<?= $table  ?>'; return false;" />
		<?php }else{ ?>
		  	<input type="button" value="Save" onclick="blackbird.submitRelated('<?= $name_space ?>'); return false;" />
		  	<input type="button" value="Close" onclick="blackbird.closeRecord('<?= $name_space  ?>'); return false;" />
		 <?php } ?>		
	</div>
</div>
