<?php

//auto mapping, action specified for thoroughness

//index
$router[] = array('route'=>"^/$",'controller'=>DEFAULT_CONTROLLER,'action'=>DEFAULT_ACTION);

//controller
if(count($uri) == 1){
	$router[] = array('route'=>"^/" . $uri[0] . "$",'controller'=>$uri[0],'action'=>DEFAULT_ACTION);
}

//controller+action
if(count($uri) == 2){
	$router[] = array('route'=>"^/" . $uri[0] . "/" . $uri[1] . "$",'controller'=>$uri[0],'action'=>$uri[1]);
}


//advanced pattern matching
//$router[] = array('route'=>"^/blog/archive/([0-9]+)/([0-9]+)$",'controller'=>'blog','action'=>'archive');

//optional override
//$router[] = array('route'=>"^/blog/index/(.*)",'controller'=>'blog','action'=>'index');

//controller/action override
//$router[] = array('route'=>"^/gateway$",'controller'=>'xml','action'=>'data');

//user defined routes