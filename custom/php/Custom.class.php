<?php

class Custom extends BlackBird
{
	
	public function __construct()
	{
		parent::__construct();
	}
	
	
	
	/**
	* formatCol
	* performs custom formatting of information for data_grid and filters.
	*
	* @param   string    value
	* @param   string    name
	* @param   string    table
	*
	*/
	
	public function formatCol($col_name,$col_value,$table){
		if($col_value != ''){
			
			
			
			// TABLE_NAME
			if($table == 'table_name'){
				// COLUMN_NAME
				if($col_name == 'column_name'){
					return /*stuff*/;
				}
			}
			
			
			
			return parent::formatCol($col_name,$col_value,$table);
		
		}
	}
	
	
	
	/**
	* injectData
	* injects columns in the datagrid data for a given row, switch based upon table
	*
	* @param   array    row data
	*
	*/
	
	
	public function injectData($a,$table)
	{
		if($a['id']['value']){
			
			
			
			// TABLE_NAME
			if($table == 'table_name'){
				array_splice($a,3,0,array(
					array(
						'col'=>'column_name',
						'value'=>'value',
						'injected'=>true
					)
				));
			}
			
			
			
		}
		
		return $a;
	}
	
	
	
	/**
	* processDelete
	* performs custom delete operations based upon table, default record delete
	*
	* @param   string    table
	* @param   array     id_set of records
	*
	*/
	
	public function processDelete($table,$id_set)
	{
		
		if($this->session->privs('delete',$table)){
			
			
			
			switch($table){
				
				default:
					
					parent::processDelete($table,$id_set);
					
				break;
			
			}
		
		
		
		}
	
	
	}
	
	
	
	/**
	* pluginColumnEdit
	* custom edit fields for EditRecord class
	*
	* @param   string    display name
	* @param   string    field name
	* @param   string    field_value
	* @param   array     options
	*
	*/
	
	function pluginColumnEdit($name,$value,$options)
	{
		
		
		
		// TABLE_NAME
		if($options['table'] == 'table_name'){
			// COLUMN_NAME
			if($options['col_name'] == 'column_name'){
				// Do stuff
			}
		}
		
		
		
		parent::pluginColumnEdit($name,$value,$options);
		
	}
	
	
	
	function pluginColumnProcess($name,$value,$options)
	{
		
		
		
		/* EXAMPLE FILE UPLOADER
		// TABLE_NAME
		if($options['table'] == 'table_name'){
			// COLUMN_NAME
			if($options['col_name'] == 'column_name'){
				if(is_uploaded_file($_FILES[$name]['tmp_name'])){
					$dir = '/path/to/upload/directory/'
					$filename = $_FILES[$name]['name'];
					if(move_uploaded_file($_FILES[$name]['tmp_name'],$dir.$filename)){
						return array('field'=>$options['col_name'],'value'=>$_FILES[$name]['name']);
					}
				}
			}
		}
		*/
		
		
		
		return parent::pluginColumnProcess($name,$value,$options);
		
	}
	
	
	
	function pluginTableProcess($table,$id,$mode)
	{
		switch(true){
			
			default:
			
				//die($this->db->getLastId($table));
			
			break;
		
		}
	
	}
	


}


//init objects
$t = new Custom();
$t->buildPage();

?>