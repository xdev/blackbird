<?php

require 'config.php';

if(file_exists('custom/php/custom.php')){
	require 'includes/BlackBird.class.php';
	require 'custom/php/custom.php';
}else{
	require 'includes/BlackBird.class.php';
	$t = new BlackBird();
	$t->buildPage();
}	

?>