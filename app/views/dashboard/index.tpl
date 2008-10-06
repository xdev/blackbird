<div class="bb_toolbar">
	<h1>Dashboard</h1>
</div>

<div id="bb_sections">
<div class="section">
<div class="container">

<div class="bb_dash">
	<div class="titlebar">
		<h2>Users</h2>
	</div>
	<div class="content">
	<?php
	print $this->fetchView('_users',array(
		'data'=>$users));
	?>
	</div>
</div>

<div class="bb_dash">
	<div class="titlebar">
		<h2>Activity</h2>
	</div>
	<div class="content">
	<?php
	print $this->fetchView('_activity',array(
		'data'=>$activity));
	?>
	</div>
</div>

<div class="bb_dash">
	<div class="titlebar">
		<h2>Tables</h2>
	</div>
	<div class="content">
	<?php
	print $this->fetchView('_tables',array(
		'data'=>$tables));
	?>
	</div>
</div>

</div>
</div>
</div>