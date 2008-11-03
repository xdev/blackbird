<?php

$routes[] = array('uri' => "/^\/(?P<controller>table)\/(?P<action>browse)\/(?P<table>[a-z_0-9]+)$/");
$routes[] = array('uri' => "/^\/(?P<controller>record)\/(?P<action>edit)\/(?P<table>[a-z_0-9]+)\/(?P<id>[a-zA-Z_0-9]+)$/");
$routes[] = array('uri' => "/^\/(?P<controller>record)\/(?P<action>add)\/(?P<table>[a-z_0-9]+)$/");
$routes[] = array('uri' => "/^\/(?P<controller>user)\/(?P<action>profile)\/(?P<user>[0-9]+)$/");
$routes[] = array('uri' => "/^\/(about)$/",'controller'=>'page','action'=>'about');