<div class="titlebar">
	<p id="closeLightbox">Close</p>
	<h1>Really Logout?</h1>
</div>
<div class="content">

<?php if($changes): ?>
<?= $this->fetchView('/_modules/_message',array(
	'class'=>'error',
	'message'=>'You have unsaved changes!'
	)
) ?>
<input type="button" value="Logout Anyway" onclick="blackbird.logout();" />
<input type="button" value="Cancel" onclick="blackbird.closeLightbox();" />
<?php else: ?>
<input type="button" value="Logout" onclick="blackbird.logout();" />
<input type="button" value="Cancel" onclick="blackbird.closeLightbox();" />
<?php endif ?>
</div>