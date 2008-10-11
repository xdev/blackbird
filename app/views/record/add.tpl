<?php

print $this->fetchView('/_modules/ui_toolbar',array(
	'controller'=>'record',
	'mode'=>'insert',
	'table'=>$this->table,
	'id'=>$id,
	'name_space'=>'main',
	'type'=>'main',
	'permission_insert'=>$permission_insert
	));
?>
<div id="bb_module">
<div class="bb_module bb_module_edit">
<div class="section <?= $name_space ?>_<?= $table ?>" id="section_<?= $name_space ?>">
<form id="form_<?= $name_space ?>" name="form_<?= $name_space ?>" enctype="multipart/form-data" action="<?= BASE ?>record/process" method="post" target="form_target_<?= $name_space ?>" onsubmit="Element.show('ajax');" >
<?= $main ?>
</form>
<iframe id="form_target_<?= $name_space ?>" name="form_target_<?= $name_space ?>" class="related_iframe"></iframe>
</div>
</div>
</div>