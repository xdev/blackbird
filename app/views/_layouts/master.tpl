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
		/*print $this->js() ?>
		<?php print $this->css() ?>
		<?php print $this->ieConditionals() ?>
		<?php print $this->feed() ?>
		*/
		print '
		<link rel="stylesheet" type="text/css" media="screen" href="' . WEB . 'assets/css/style.css" />
		<script type="text/javascript" src="' . WEB . 'assets/js/prototype.js" ></script>
		<script type="text/javascript" src="' . WEB . 'assets/js/scriptaculous/scriptaculous.js?load=effects,dragdrop" ></script>';
		
		?>
	</head>
	
	<body>
		
		<div id="bb_navigation">
			<?php			
			/*			
			<?php print $this->logo() ?>
			<?php print $this->tables() ?>
			<?php print $this->actions() ?>
			*/
			?>
		</div>
		
		<div id="bb_session_nav">
			<?php
			/*
			<?php print $this->breadcrumb() ?>
			<?php print $this->session() ?>
			*/
			?>
		</div>
		
		<div id="bb_main">
			<?= $content['main'] ?>
		</div>
		
	</body>
	
</html>
