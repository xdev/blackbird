<ul>
	<?php if(_ControllerFront::$session->logged === true){ ?>
	<li>Logged in as <a href="<?= BASE ?>user/edit"><?= _ControllerFront::$session->displayname ?></a></li>
	<?php } ?>
	<li><a href="<?= BASE ?>user/admin">Admin</a></li><li><a href="<?= BASE ?>user/logout">Logout</a></li>
	<li>Blackbird 2.0_alpha</li>
</ul>