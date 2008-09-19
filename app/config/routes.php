<?php

$routes[] = array('uri' => "/(?<controller>table)\/(?<action>browse)\/(?<table>[a-z_0-9]+)/");
$routes[] = array('uri' => "/(?<controller>record)\/(?<action>edit)\/(?<table>[a-z_0-9]+)\/(?<id>[0-9]+)/");
$routes[] = array('uri' => "/(?<controller>record)\/(?<action>add)\/(?<table>[a-z_0-9]+)/");