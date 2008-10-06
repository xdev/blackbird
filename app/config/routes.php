<?php

$routes[] = array('uri' => "/^\/(?<controller>table)\/(?<action>browse)\/(?<table>[a-z_0-9]+)$/");
$routes[] = array('uri' => "/^\/(?<controller>record)\/(?<action>edit)\/(?<table>[a-z_0-9]+)\/(?<id>[a-zA-Z_0-9]+)$/");
$routes[] = array('uri' => "/^\/(?<controller>record)\/(?<action>add)\/(?<table>[a-z_0-9]+)$/");
$routes[] = array('uri' => "/^\/(?<controller>user)\/(?<action>profile)\/(?<user>[0-9]+)$/");
$routes[] = array('uri' => "/^\/(about)$/",'controller'=>'page','action'=>'about');