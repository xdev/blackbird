<?php

interface Plugin
{

	public function __set($name,$value);
	
	public function __get($name);
		
	public function build();

}

?>