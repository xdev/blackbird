<h1>Edit User</h1>
<div class="bb_module bb_module_edit">
<form id="form_<?= $name_space ?>" name="form_<?= $name_space ?>" enctype="multipart/form-data" action="<?= BASE ?>user/process" method="post" target="form_target_<?= $name_space ?>" onsubmit="Element.show('ajax');" >
<?php
Forms::text($name_space . '_firstname',$row['firstname'],array('label'=>'First Name'));
Forms::text($name_space . '_lastname',$row['lastname'],array('label'=>'Last Name'));
Forms::text($name_space . '_email',$row['email'],array('label'=>'Email'));
Forms::text($name_space . '_password','',array('label'=>'Password','type'=>'password'));
?>
<input type="button" value="submit" onclick="$('form_<?= $name_space ?>').submit(); return false;" />
</form>
<iframe id="form_target_<?= $name_space ?>" name="form_target_<?= $name_space ?>" class="related_iframe"></iframe>
</div>