<div class="bb_toolbar">
	<h1>Dashboard</h1>
</div>

<div id="bb_sections">
<div class="section">
<div id="dashboard" class="container">
<div class="bb_dash">
	<div class="titlebar">
		<h2>Users</h2>
		<a class="toggle" href="#" title="Open/Close">Toggle</a>
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
		<a class="toggle" href="#" title="Open/Close">Toggle</a>
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
		<a class="toggle" href="#" title="Open/Close">Toggle</a>
	</div>
	<div class="content">
	<?php
	print $this->fetchView('_tables',array(
		'data'=>$tables));
	?>
	</div>
</div>

<script type="text/javascript">
Sortable.create(
	$('dashboard'),
	{
		overlap		: "horizontal",
		tag			: 'div',
		constraint	: false,
		handle		: "titlebar"
    }
);
</script>
</div>
</div>
</div>