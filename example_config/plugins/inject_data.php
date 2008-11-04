<?php

function config__inject_data($a,$table,$mode)
{
	/*
	if($table == 'events'){
		$tA = 
		array(
			'col'=>'img_thumb',
			'value'=>'<img src="/files/events/img_thumb/' .  $a['img_thumb']['value'] . '" />'				
		);
		array_splice($a,2,1,array($tA));
	}
	*/
	
	//always return, regardless of alterations
	return $a;
}