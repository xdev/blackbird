<ul id="bb_toggle_sections">
	<li><a id="bb_toggle_main" class="selected" href="#main">Edit Record</a></li>
	<?php if(is_array($related)) foreach ($related as $relation): ?>
	<li><a id="bb_toggle_<?= $relation['name_space'] ?>" class="<?= ($mode == "insert" ? 'dim' : '') ?>" href="#<?= $relation['name_space'] ?>"><?= $relation['label'] ?></a></li>
	<?php endforeach ?>
</ul>

<div id="bb_sections" class="bb_module bb_module_edit">
	
	<div class="section <?= $name_space ?>_<?= $table ?>" id="section_<?= $name_space ?>">
		
		<div class="record">
		
			<?= $this->fetchView('/_modules/ui_toolbar',array(
					'controller'=>'record',
					'mode'=>'edit',
					'table'=>$table,
					'id'=>$id,
					'name_space'=>$name_space,
					'type'=>'main')
			) ?>
			
			<div class="container">
				<form id="form_<?= $name_space ?>" name="form_<?= $name_space ?>" enctype="multipart/form-data" action="<?= BASE ?>record/process" method="post" target="form_target_<?= $name_space ?>" onsubmit="Element.show('ajax');" >
					<?= $main ?>
				</form>
				<iframe id="form_target_<?= $name_space ?>" name="form_target_<?= $name_space ?>" class="related_iframe"></iframe>
				<script type="text/javascript">
					<!-- <![CDATA[ 
					document.observe('dom:loaded',function(){
						formController_<?= $name_space ?> = new formController('form_<?= $name_space ?>');
						blackbird.setProperty('id_parent','<?= $id ?>');
						blackbird.setProperty('table_parent','<?= $table ?>');
					});
					// ]]> -->
				</script>
			</div>
		</div>
		
	</div>

	<?php if(is_array($related)) foreach($related as $relation): ?>
	<div class="section" id="section_<?= $relation['name_space'] ?>" style="display:none;">
		<?php if($relation['display'] == 'data_grid'): ?>
		<div class="record edit_form detail" style="display:none;"></div>
		
		<div class="table">
			<script type="text/javascript">
				<!-- <![CDATA[
				document.observe("dom:loaded",function(){
				data_grid_<?= $relation['name_space'] ?> = new dataGrid(
					{
						mode: "related",
						table: "<?= $relation['table_child'] ?>",
						table_parent: "<?= $relation['table_parent'] ?>",
						id_parent: "<?= $id ?>",
						name_space: "<?= $relation['name_space'] ?>",
						base: "<?= BASE ?>"
						<?= (isset($relation['sql_where']) ? sprintf(',sql_where: "%s"',$relation['sql_where']) : '') ?>
					}
				);
				blackbird.broadcaster.addListener(data_grid_<?= $relation['name_space'] ?>);
				});
				// ]]> -->
			</script>
		</div>
		<?php elseif($relation['display'] == 'image_browser'): ?>
		<div class="record edit_form detail" style="display:none;"></div>
			
			<div class="table">
				<?= $this->fetchView('/imagebrowser/browse',array(
					'name_space'=>$relation['name_space'],
					'table'=>$relation['table_child'],
					'config'=>_ControllerFront::parseConfig($relation['config']),
					'id'=>$id)
				) ?>
			</div>
			
		<?php elseif($relation['display'] == 'plugin'): ?>
			
		<?php endif ?>
	</div>
	<?php endforeach ?>
	
</div>