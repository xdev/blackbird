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
	$router[] = array('route'=>"^/(" . $uri[0] . ")/" . $uri[1] . "$",'controller'=>$uri[0],'action'=>$uri[1]);
}


//browse
/*
$router[] = array('route'=>"^/browse(.*)",'controller'=>'browse','action'=>'index','table'=>$uri[1],'id'=>$uri[2]);

$router[] = array('route'=>"^/edit(.*)",'controller'=>'edit','action'=>'edit','table'=>$uri[1],'id'=>$uri[2]);

$router[] = array('route'=>"^/add(.*)",'controller'=>'edit','action'=>'add','table'=>$uri[1],'id'=>$uri[2]);

//advanced pattern matching
//$router[] = array('route'=>"^/blog/archive/([0-9]+)/([0-9]+)$",'controller'=>'blog','action'=>'archive');

//optional override
$router[] = array('route'=>"^/browse/test/([0-9]+)$",'controller'=>'browse','action'=>'something');

//controller/action override
//$router[] = array('route'=>"^/gateway$",'controller'=>'xml','action'=>'data');

//user defined routes


/*
$router->connect(":controller/:id");
$router->connect("browse/:table",array('controller'=>'browse'));
$router->connect("edit/:table/:id",array('controller'=>'edit','action'=>'edit'));
$router->connect("add/:table/:id",array('controller'=>'edit','action'=>'add'));
$router->connect("login
*/