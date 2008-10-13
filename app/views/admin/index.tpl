<div class="bb_toolbar">
	<h1>Admin</h1>
</div>

<div id="bb_sections">
	<div class="section">
		<div id="dashboard" class="container">
			<?= $this->fetchView('_groups',array('data'=>$groups)) ?>
			<?= $this->fetchView('_users',array('data'=>$users)) ?>
			
			<?php //$this->fetchView('_activity',array('data'=>$groups)) ?>
			
		</div>
	</div>
</div>

