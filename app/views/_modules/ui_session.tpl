<ul>
	<?php if(_ControllerFront::$session->logged === true): ?>
	<li>Logged in as <a href="<?= BASE ?>user/profile/<?= _ControllerFront::$session->u_id ?>"><?= _ControllerFront::$session->displayname ?></a></li>
	<?php if(_ControllerFront::$session->admin === true): ?>
	<li><a href="<?= BASE ?>admin">Admin</a></li>
	<?php endif ?>
	<li><a href="#" onclick="blackbird.promptLogout(); return false;">Logout</a></li>
	<?php else: ?>
	<li><a href="<?= BASE ?>user/login">Login</a></li>
	<?php endif ?>
	<li id="aboutBlackbird"><a class="lightbox" title="About Blackbird" href="#<?= BASE ?>about">About Blackbird</a></li>
</ul>