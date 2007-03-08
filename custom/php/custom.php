<?php

class Custom extends BlackBird
{
	
	/*
	©2007
		
	Back-End
		Charles Mastin
		Joshua Rudd
	Front-End 
		Charles Mastin
		Joshua Rudd
		
	Libraries
		prototype.js
		scriptaculous
			
	*/
	
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
	*
	*/
	
	public function formatCol($col_name,$col_value,$table)
	{
		return parent::formatCol($col_name,$col_value,$table);
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
		parent::pluginColumnEdit($name,$value,$options);
	}
	
	function pluginColumnProcess($name,$value,$options)
	{
		if($options['table'] == 'product_images' && $options['col_name'] == 'image_main'){
			return array('error'=>'File needs to be a jpg.');
		}
		
		if($options['table'] == 'products' && $options['col_name'] == 'long_description'){
			if(strlen($value) < 10){
				return array('error'=>'Needs to be longer than 10 characters.');
			}else{
				return array('field'=>$options['col_name'],'value'=>$value);
			}
		}
		
		return parent::pluginColumnProcess($name,$value,$options);
	}
	
	
	function pluginTableProcess($table,$id,$mode)
	{
		switch(true){
								
			default:
				//die(Db::getLastId($table));
			break;
		
		}
	
	}
	

}


//init objects
$t = new Custom();
$t->buildPage();

?>