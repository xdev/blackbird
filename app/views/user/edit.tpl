<?= $this->fetchView('/_modules/ui_toolbar',array(
		'controller'=>'record',
		'mode'=>'edit',
		'table'=>BLACKBIRD_USERS_TABLE,
		'id'=>'0',
		'active'=>null,
		'name_space'=>$name_space,
		'type'=>'user')
) ?>


<div class="bb_module bb_module_edit">
<form id="form_<?= $name_space ?>" name="form_<?= $name_space ?>" enctype="multipart/form-data" action="<?= BASE ?>user/processedit" method="post" target="form_target_<?= $name_space ?>" onsubmit="Element.show('ajax');" >
<?php
Forms::text($name_space . '_firstname',$row['firstname'],array('label'=>'First Name','validate'=>'default'));
Forms::text($name_space . '_lastname',$row['lastname'],array('label'=>'Last Name','validate'=>'default'));
Forms::text($name_space . '_email',$row['email'],array('label'=>'Email','validate'=>'default'));
Forms::text($name_space . '_password_reset','',array('label'=>'Reset Password','type'=>'password'));
?>
<!--<input type="button" value="submit" onclick="blackbird.submitMain('<?= $name_space ?>'); return false;" />-->
</form>
<iframe id="form_target_<?= $name_space ?>" name="form_target_<?= $name_space ?>" class="related_iframe"></iframe>
</div>