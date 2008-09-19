<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
	"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">

<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">

<!--
Copyright 2004-2008 
Authors Charles Mastin & Joshua Rudd
c @ charlesmastin.com
contact @ joshuarudd.com


Portions of this software rely upon the following software which are covered by their respective license agreements
* Prototype.js
* Scriptaculous Library

-->
	
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
		<title>Blackbird</title>
		<?php 
		print $this->css();
		print '		
		<script type="text/javascript" src="' . BASE . 'assets/js/prototype.js" ></script>
		<script type="text/javascript" src="' . BASE . 'assets/js/scriptaculous/scriptaculous.js?load=effects,dragdrop" ></script>';
		?>
	</head>
	
	<body>
		
		<div id="bb_navigation">
			<?= $content['ui_nav'] ?>
		</div>
		
		<div id="bb_session_nav">
			<?= $content['ui_breadcrumb'] ?>
			<?= $content['ui_session'] ?>
		</div>
		
		<div id="bb_main">
			<?= $content['ui_toolbar'] ?>
			
			<div id="bb_module">
				<div id="pane_main">
				<?= $content['main'] ?>
				</div>
			</div>
		</div>
		
	</body>
	
</html>
