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
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
		<meta name="base" content="<?= BASE ?>" />
		<title><?= BLACKBIRD_CLIENT ?></title>
		<?= $this->css() ?>
		<!-- library code -->
		<script type="text/javascript" src="<?= BASE ?>assets/js/prototype.js"></script>
		<script type="text/javascript" src="<?= BASE ?>assets/js/scriptaculous/scriptaculous.js?load=effects,dragdrop"></script>
		<script type="text/javascript" src="<?= BASE ?>assets/js/functions.js"></script>
		<script type="text/javascript" src="<?= BASE ?>assets/js/eventbroadcaster.js"></script>		
		<!-- app code -->
		<script type="text/javascript" src="<?= BASE ?>assets/js/blackbird.js"></script>
		<!-- widget code -->
		<script type="text/javascript" src="<?= BASE ?>assets/js/datagrid.js"></script>		
		<script type="text/javascript" src="<?= BASE ?>assets/js/form.js"></script>
		<script type="text/javascript" src="<?= BASE ?>assets/js/validator.js"></script>	
		<script type="text/javascript" src="<?= BASE ?>assets/js/listmanager.js"></script>
		<script type="text/javascript" src="<?= BASE ?>assets/js/imagebrowser.js"></script>
		<!--[if IE 7]>
			<link rel="stylesheet" href="<?= BASE ?>assets/css/ie7.css" type="text/css" media="screen" charset="utf-8" />
		<![endif]-->
	</head>