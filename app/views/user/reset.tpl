<h1>Reset Password</h1>
<div class="bb_module bb_module_edit">
<form id="form_<?= $name_space ?>" name="form_<?= $name_space ?>" enctype="multipart/form-data" action="<?= BASE ?>user/processreset" method="post" target="form_target_<?= $name_space ?>" onsubmit="Element.show('ajax');" >
<?php
Forms::text($name_space . '_login',$login,array('label'=>'Email'));
?>
<input type="button" value="submit" onclick="$('form_<?= $name_space ?>').submit(); return false;" />
<input type="button" value="cancel" onclick="window.parent.location = '<?= BASE ?>user/login'; return false;" />
</form>
<iframe id="form_target_<?= $name_space ?>" name="form_target_<?= $name_space ?>" class="related_iframe"></iframe>
</div>