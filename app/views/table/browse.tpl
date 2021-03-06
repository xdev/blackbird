<div id="bb_sections" class="browse bb_module bb_module_edit">
	<div class="section" id="section_<?= $name_space ?>">
		
		<div class="bb_toolbar main">
			<h1>Browsing <?= _ControllerFront::getTableName($table) ?></h1>
			<div class="bb_toolbar_actions">
				<?php $datagrid = 'data_grid_' . $name_space ?>
				<?php if($permission_add==true): ?>
				<input type="button" value="+ Add Record" onclick="window.location='<?= BASE ?>record/add/<?= $table ?>'" />
				<?php endif ?>
				<input class="search" id="<?= $name_space ?>_search" type="text" value="Live search..." size="20" onclick="clickclear(this, 'Live search...')" onblur="clickrecall(this,'Live search...')"  />
				<a class="icon undo" href="#" onclick="<?= $datagrid ?>.reset();" title="Reset Data Grid">Reset filters</a>		
			</div>
		</div>		
		
		<!-- need to view this -->
		<?php if(isset($_GET['message'])): ?>
		<?php if($_GET['message'] == 'delete'): ?>
		<?= $this->fetchView('/_modules/_message',array(
			'class'=>'error',
			'message'=>'Record ' . $_GET['id'] . ' removed!'
			)
		) ?>
		<?php endif ?>
		<?php endif ?>
		
		
		<div class="table">
			<script type="text/javascript">
				<!-- <![CDATA[ 
				data_grid_<?= $name_space ?> = new dataGrid(
					{
						table: "<?= $this->route['table'] ?>",
						name_space: "<?= $name_space ?>",
						base: "<?= BASE ?>"
					}
				);
				// ]]> -->
			</script>
		</div>
	</div>
</div>