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
					'type'=>'main',
					'permission_update'=>$permission_update,
					'permission_delete'=>$permission_delete
					)
			) ?>
			
			
			
			<div class="container">
				
				<?php if(isset($_GET['message'])): ?>
				<?php if($_GET['message'] == 'edit'): ?>
				<?= $this->fetchView('/_modules/_message',array(
					'class'=>'ok',
					'message'=>'Record ' . $id . ' successfully updated!'
					)
				) ?>
				<?php elseif($_GET['message'] == 'add'): ?>
				<?= $this->fetchView('/_modules/_message',array(
					'class'=>'ok',
					'message'=>'Record ' . $id . ' successfully added!'
					)
				) ?>
				<?php endif ?>
				<?php endif ?>
				
				<form id="form_<?= $name_space ?>" name="form_<?= $name_space ?>" enctype="multipart/form-data" action="<?= BASE ?>record/process" method="post" target="form_target_<?= $name_space ?>" onsubmit="Element.show('ajax');" >
					<?= $main ?>
				</form>
				<iframe id="form_target_<?= $name_space ?>" name="form_target_<?= $name_space ?>" class="related_iframe"></iframe>
				<script type="text/javascript">
					<!-- <![CDATA[ 
					document.observe('dom:loaded',function(){
						formController_<?= $name_space ?> = new formController('form_<?= $name_space ?>');
						formController_<?= $name_space ?>.broadcaster.addListener(blackbird);
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
		
		<div class="browse">
		<div class="bb_toolbar related">
			<h1>Browsing <?= Utils::titleCase(str_replace('_',' ',$relation['table_child'])) ?></h1>
			<div class="bb_toolbar_actions">
				<?php $datagrid = 'data_grid_' . $relation['name_space'] ?>					
				<?php if($relation['permission_insert'] == true): ?>
				<input type="button" value="+ Add Record" onclick="blackbird.addNewRecord('<?= $relation['table_child'] ?>','<?= $relation['name_space'] ?>');" />
				<?php endif ?>
				<input class="search" id="<?= $relation['name_space'] ?>_search" type="text" value="Live search..." size="20" onclick="clickclear(this, 'Live search...')" onblur="clickrecall(this,'Live search...')"  />
				<a class="icon undo" href="#" onclick="<?= $datagrid ?>.reset();" title="Reset Data Grid">Reset filters</a>		
			</div>
		</div>
		
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
		</div>
		<?php elseif($relation['display'] == 'image_browser'): ?>
		<div class="record edit_form detail" style="display:none;"></div>
			
			<div class="browse">
			<div class="table">
				<?= $this->fetchView('/imagebrowser/browse',array(
					'name_space'=>$relation['name_space'],
					'table'=>$relation['table_child'],
					'config'=>_ControllerFront::parseConfig($relation['config']),
					'id'=>$id)
				) ?>
			</div>
			</div>
		<?php elseif($relation['display'] == 'plugin'): ?>
			
		<?php endif ?>
	</div>
	<?php endforeach ?>
	
</div>