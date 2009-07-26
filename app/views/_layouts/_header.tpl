<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
	"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">

<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">

<!--
Copyright 2004-2009
Authors Charles Mastin & Joshua Rudd
c @ charlesmastin.com
contact @ joshuarudd.com


Portions of this software rely upon the following software which are covered by their respective license agreements
* Prototype.js
* Scriptaculous Library
* Jquery
* JqueryUI

-->

	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
		<meta name="base" content="<?= BASE ?>" />
		<title><?= BLACKBIRD_CLIENT ?></title>
		<!-- base css -->
		<link rel="stylesheet" href="<?= BASE ?>assets/css/reset.css" type="text/css" media="screen" charset="utf-8" />
		<link rel="stylesheet" href="<?= BASE ?>assets/css/screen.css" type="text/css" media="screen" charset="utf-8" />
		<link rel="stylesheet" href="<?= BASE ?>assets/css/edit.css" type="text/css" media="screen" charset="utf-8" />
		<link rel="stylesheet" href="<?= BASE ?>assets/css/data.css" type="text/css" media="screen" charset="utf-8" />
		<link rel="stylesheet" href="<?= BASE ?>assets/css/imagebrowser.css" type="text/css" media="screen" charset="utf-8" />
				
		<!-- library code -->
		<script type="text/javascript" src="<?= BASE ?>assets/js/jquery-1.3.1.min.js"></script>
		<script type="text/javascript" src="<?= BASE ?>assets/js/jquery-ui-personalized-1.6rc5.min.js"></script>
		<script type="text/javascript" src="<?= BASE ?>assets/js/prototype.js"></script>
		<script type="text/javascript" src="<?= BASE ?>assets/js/scriptaculous/scriptaculous.js?load=effects,dragdrop"></script>
		<script type="text/javascript" src="<?= BASE ?>assets/js/functions.js"></script>
		<script type="text/javascript" src="<?= BASE ?>assets/js/eventbroadcaster.js"></script>		
		<!-- app code -->
		<script type="text/javascript" src="<?= BASE ?>assets/js/blackbird.js"></script>
		<!-- widget code -->
		<script type="text/javascript" src="<?= BASE ?>assets/js/datagrid.js"></script>		
		<script type="text/javascript" src="<?= BASE ?>assets/js/form.js"></script>
		<script type="text/javascript" src="<?= BASE ?>assets/js/dropdown.js"></script>	
		<script type="text/javascript" src="<?= BASE ?>assets/js/validator.js"></script>	
		<script type="text/javascript" src="<?= BASE ?>assets/js/listmanager.js"></script>
		<script type="text/javascript" src="<?= BASE ?>assets/js/imagebrowser.js"></script>
		<!--[if IE 7]>
			<link rel="stylesheet" href="<?= BASE ?>assets/css/ie7.css" type="text/css" media="screen" charset="utf-8" />
		<![endif]-->
		
		<?= $this->getCustomHeaders() ?>
		
	</head>