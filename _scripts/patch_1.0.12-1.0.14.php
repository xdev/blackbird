<?php

/*
This script will update your config xml to the new format

<name>value</name>
vs
<option name="name">value</option>

AND update your database to remove enums

*/

function setConfig($k,$v)
{
	define($k,$v);
}

//set up path environment
$base = system('pwd') . '/';
$tA = explode('/',$base);
$tA = array_slice($tA,1,-3);
$base = '/'.join('/',$tA).'/';

require $base.'cms_config/config_custom.php';

$myA = array();

//
$i=0;
foreach ( $argv as $key => $value ) {
	
	print $key . '='.$value . "\r\n";
	if($i>0){
		//explode right on =
		$v = explode('=',$value);
		//explode left on --
		$k = explode('--',$v[0]);
		$myA[$k[1]] = $v[1];
	}
	
	$i++;
}

define('LIB',$base.'cms/bobolink/');
require LIB.'database/Db.interface.php';
require LIB.'database/AdaptorMysql.class.php';
require LIB.'utils/Utils.class.php';
$db = new AdaptorMysql();

//run schema patch
$sql = "alter table `cms_cols` change column `edit_channel` `edit_channel` varchar(20) not null default '' after `default_value`;
alter table `cms_cols` change column `edit_module` `edit_module` varchar(20) not null default '' after `edit_channel`;
alter table `cms_cols` change column `edit_mode` `edit_mode` varchar(20) not null default '' after `edit_module`;
alter table `cms_cols` change column `process_channel` `process_channel` varchar(20) not null default '' after `edit_config`;
alter table `cms_cols` change column `process_mode` `process_mode` varchar(20) not null default '' after `process_module`;
alter table `cms_headers` change column `mode` `mode` varchar(255) not null default '' after `table_name`;
alter table `cms_relations` change column `display` `display` varchar(255) not null default '' after `column_child`;
alter table `cms_tables` change column `display_mode` `display_mode` varchar(20) not null default '' after `sort_default`;
CREATE TABLE `cms_info` (
  `name` varchar(40) NOT NULL default '',
  `value` varchar(255) NOT NULL default '',
  PRIMARY KEY  (`name`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
insert into `cms_info` values('schema_version','1.0.14');";

$schema = explode(';',$sql);
array_pop($schema);
foreach ($schema as $row) {
	$db->sql($row);
}

function processTable($q,$table)
{	
	//prep columns from initial rowage
	global $db;
	
	foreach($q as $row){
		$sqlA = array();
		foreach($row as $key=>$value){
			
			if(!is_numeric($key) && $key != 'id'){
				if($value != ''){
					if($value = reformatData($value)){
						$sqlA[] = array('field'=>$key,'value'=>$value);
					}
				}
			}
			
		}
		
		if($db->update($table,$sqlA,'id',$row['id'])){
			print $table .' updated!';
		}
			
	}
}

function reformatData($input)
{
	//read into array from Utils::parseConfig
	$tA = Utils::parseConfig($input);
	$r = '<config>'."\n";
	foreach($tA as $key=>$value){
		$r .= "\t".'<'.$key.'>'.$value.'</'.$key.'>'."\n";
	}
	$r .= '</config>';
	return $r;
	
}


$q = $db->query("
	SELECT 
	id,edit_config,process_config,validate,filter 
	FROM 
	cms_cols 
	WHERE 
		(edit_config != '' ||
		process_config != '' ||
		validate != '' ||
		filter != '')
	ORDER BY id");
	
processTable($q,'cms_cols');

$q = $db->query("
	SELECT 
	id,config 
	FROM 
	cms_relations 
	WHERE 
		(config != '') 
	ORDER BY id");
	
processTable($q,'cms_relations');

?>