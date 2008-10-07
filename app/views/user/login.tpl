<!-- client branding up in here -->

<h1>Blackbird Login</h1>
<?php if(isset($_GET['loggedout'])): ?>
<p class="message ok">You were successfully logged out</p>
<?php endif ?>
<form id="form_<?= $name_space ?>" name="form_<?= $name_space ?>" enctype="multipart/form-data" action="<?= BASE ?>user/processlogin" method="post" target="form_target_<?= $name_space ?>" onsubmit="Element.show('ajax');" >
<?php
Forms::text($name_space . '_login','',array('label'=>'Login','size'=>30));
Forms::text($name_space . '_password','',array('label'=>'Password','type'=>'password','size'=>30));
?>
<input type="button" value="submit" onclick="$('form_<?= $name_space ?>').submit(); return false;" />
</form>
<iframe id="form_target_<?= $name_space ?>" name="form_target_<?= $name_space ?>" class="related_iframe"></iframe>

<!-- optional OpenID stuff in here -->

<script type="text/javascript">
	Event.observe("login_password","keypress",function(event){
		if(event.keyCode == Event.KEY_RETURN){
			$("form_<?= $name_space ?>").submit();
		}
	});
</script>
