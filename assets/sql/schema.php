<?php

// In-progress idea for dynamic schema installer/updater, currently based on Drupal's system.install


/**
 * Implementation of hook_schema().
 */
function system_schema() {
	$schema['cols'] = array(
		'comment' => 'table description goes here',
		'fields' => array(
			'id' => array(
				'comment'  => 'field description goes here',
				'type'     => 'mediumint',
				'length'   => 128,
				'not null' => TRUE,
				'default'  => ''
			),
			'table_name' => array(
				'comment'  => 'field description goes here'),
				'type'     => 'varchar',
				'length'   => 40,
				'not null' => TRUE,
				'default'  => ''
			),
			'column_name' => array(
				'comment'  => 'field description goes here'),
				'type'     => 'varchar',
				'length'   => 40,
				'not null' => TRUE,
				'default'  => ''
			),
			'data_grid_name' => array(
				'comment'  => 'field description goes here'),
				'type'     => 'varchar',
				'length'   => 255,
				'not null' => TRUE,
				'default'  => ''
			),
			'display_name' => array(
				'comment'  => 'field description goes here'),
				'type'     => 'varchar',
				'length'   => 40,
				'not null' => TRUE,
				'default'  => ''
			),
			'default_value' => array(
				'comment'  => 'field description goes here'),
				'type'     => 'text',
				'not null' => TRUE
			),
			'edit_channel' => array(
				'comment'  => 'field description goes here'),
				'type'     => 'varchar',
				'length'   => 20,
				'not null' => TRUE,
				'default'  => ''
			),
			'edit_module' => array(
				'comment'  => 'field description goes here'),
				'type'     => 'varchar',
				'length'   => 20,
				'not null' => TRUE,
				'default'  => ''
			),
			'edit_mode' => array(
				'comment'  => 'field description goes here'),
				'type'     => 'varchar',
				'length'   => 20,
				'not null' => TRUE,
				'default'  => ''
			),
			'edit_config' => array(
				'comment'  => 'field description goes here'),
				'type'     => 'text',
				'not null' => TRUE
			),
			'process_channel' => array(
				'comment'  => 'field description goes here'),
				'type'     => 'varchar',
				'length'   => 20,
				'not null' => TRUE,
				'default'  => ''
			),
			'process_module' => array(
				'comment'  => 'field description goes here'),
				'type'     => 'varchar',
				'length'   => 20,
				'not null' => TRUE,
				'default'  => ''
			),
			'process_mode' => array(
				'comment'  => 'field description goes here'),
				'type'     => 'varchar',
				'length'   => 20,
				'not null' => TRUE,
				'default'  => ''
			),
			'process_config' => array(
				'comment'  => 'field description goes here'),
				'type'     => 'text',
				'not null' => TRUE
			),
			'validate' => array(
				'comment'  => 'field description goes here'),
				'type'     => 'text',
				'not null' => TRUE
			),
			'filter' => array(
				'comment'  => 'field description goes here'),
				'type'     => 'text',
				'not null' => TRUE
			),
			'help' => array(
				'comment'  => 'field description goes here'),
				'type'     => 'text',
				'not null' => TRUE
			)
		),
		'primary key' => array('id'),
		'unique keys' => array(
			'table_name'  => array('table_name'),
			'column_name' => array('column_name'),
			'edit'        => array('table_name','column_name','edit_mode')
		),
		'indexes' => array(
			'id' => array('id')
		)
	);

	// foreach ($schema as $name => $table) {
	// 	  db_create_table($ret, $name, $table);
	// 	}
	
	return $schema;
	
}

/**
 * Update files tables to associate files to a uid by default instead of a nid.
 * Rename file_revisions to upload since it should only be used by the upload
 * module used by upload to link files to nodes.
 */
function system_update_6022() {
  $ret = array();

  // Rename the nid field to vid, add status and timestamp fields, and indexes.
  db_drop_index($ret, 'files', 'nid');
  db_change_field($ret, 'files', 'nid', 'uid', array('type' => 'int', 'unsigned' => TRUE, 'not null' => TRUE, 'default' => 0));
  db_add_field($ret, 'files', 'status', array('type' => 'int', 'not null' => TRUE, 'default' => 0));
  db_add_field($ret, 'files', 'timestamp', array('type' => 'int', 'unsigned' => TRUE, 'not null' => TRUE, 'default' => 0));
  db_add_index($ret, 'files', 'uid', array('uid'));
  db_add_index($ret, 'files', 'status', array('status'));
  db_add_index($ret, 'files', 'timestamp', array('timestamp'));

  // Rename the file_revisions table to upload then add nid column. Since we're
  // changing the table name we need to drop and re-add the indexes and
  // the primary key so both mysql and pgsql end up with the correct index
  // names.
  db_drop_primary_key($ret, 'file_revisions');
  db_drop_index($ret, 'file_revisions', 'vid');
  db_rename_table($ret, 'file_revisions', 'upload');
  db_add_field($ret, 'upload', 'nid', array('type' => 'int', 'unsigned' => TRUE, 'not null' => TRUE, 'default' => 0));
  db_add_index($ret, 'upload', 'nid', array('nid'));
  db_add_primary_key($ret, 'upload', array('vid', 'fid'));
  db_add_index($ret, 'upload', 'fid', array('fid'));

  // The nid column was renamed to uid. Use the old nid to find the node's uid.
  update_sql('UPDATE {files} SET uid = (SELECT n.uid FROM {node} n WHERE {files}.uid = n.nid)');
  update_sql('UPDATE {upload} SET nid = (SELECT r.nid FROM {node_revisions} r WHERE {upload}.vid = r.vid)');

  // Mark all existing files as FILE_STATUS_PERMANENT.
  $ret[] = update_sql('UPDATE {files} SET status = 1');

  return $ret;
}

function system_update_6023() {
  $ret = array();

  // nid is DEFAULT 0
  db_drop_index($ret, 'node_revisions', 'nid');
  db_change_field($ret, 'node_revisions', 'nid', 'nid', array('type' => 'int', 'unsigned' => TRUE, 'not null' => TRUE, 'default' => 0));
  db_add_index($ret, 'node_revisions', 'nid', array('nid'));
  return $ret;
}