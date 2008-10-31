<div class="divider"></div>

<?= $this->fetchView('/_modules/ui_toolbar',array(
	'controller'=>'record',
	'mode'=>$mode,
	'table'=>$this->table,
	'id'=>$id,
	'name_space'=>$name_space,
	'type'=>'related',
	'permission_insert'=>$permission_insert,
	'permission_update'=>$permission_update,
	'permission_delete'=>$permission_delete
	));
?>

<div class="container">
	<form id="form_<?= $name_space ?>" name="form_<?= $name_space ?>" <?= (($mode == 'add') ? 'class="unsaved"' : '') ?>enctype="multipart/form-data" action="<?= BASE ?>record/process" method="post" target="form_target_<?= $name_space ?>" onsubmit="Element.show('ajax');" >
		<?= $main ?>
	</form>
	<iframe id="form_target_<?= $name_space ?>" name="form_target_<?= $name_space ?>" class="related_iframe"></iframe>
</div>

<?php //if($mode == 'edit'): ?>
<script type="text/javascript">
	<!-- <![CDATA[
	if(window.formController_<?= $name_space ?> !== undefined){
		delete formController_<?= $name_space ?>;
	}
	formController_<?= $name_space ?> = new formController('form_<?= $name_space ?>');
	formController_<?= $name_space ?>.broadcaster.addListener(blackbird);
	// ]]> -->
</script>
<?php //endif ?>