<h1>Login</h1>
<div class="bb_module bb_module_edit">
<form id="form_<?= $name_space ?>" name="form_<?= $name_space ?>" enctype="multipart/form-data" action="<?= BASE ?>user/processlogin" method="post" target="form_target_<?= $name_space ?>" onsubmit="Element.show('ajax');" >
<?php
Forms::text($name_space . '_login','',array('label'=>'Login'));
Forms::text($name_space . '_password','',array('label'=>'Password','type'=>'password'));
?>
<input type="button" value="submit" onclick="$('form_<?= $name_space ?>').submit(); return false;" />
</form>
<iframe id="form_target_<?= $name_space ?>" name="form_target_<?= $name_space ?>" class="related_iframe"></iframe>
</div>

<script type="text/javascript">
	Event.observe("login_password","keypress",function(event){
		if(event.keyCode == Event.KEY_RETURN){
			$("form_<?= $name_space ?>").submit();
		}
	});
</script>