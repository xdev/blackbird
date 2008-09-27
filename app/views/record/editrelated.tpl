<?php
print $this->fetchView('/_modules/ui_toolbar',array(
	'controller'=>'record',
	'mode'=>$mode,
	'table'=>$this->table,
	'id'=>$id,
	'active'=>$active,
	'name_space'=>$name_space,
	'type'=>'related'));
?>	
<form id="form_<?= $name_space ?>" name="form_<?= $name_space ?>" enctype="multipart/form-data" action="<?= BASE ?>record/process" method="post" target="form_target_<?= $name_space ?>" onsubmit="Element.show('ajax');" >
<?= $main ?>
</form>
<iframe id="form_target_<?= $name_space ?>" name="form_target_<?= $name_space ?>" class="related_iframe"></iframe>