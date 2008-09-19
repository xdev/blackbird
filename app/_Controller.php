<?php

class _Controller extends Controller
{
	
	protected $front;
	
	public function __construct($route)
	{
		parent::__construct($route);
		$this->mode = '';
		$this->id = '';
		$this->front = _ControllerFront::getInstance();
	}
	
	public function render()
	{
		//if we have a layout - should get more specific, if we have an html master layout
		if($this->layout_view){
			$this->prepUI();
		}
		
		return parent::render();
	}
	
	public function css()
	{
		// Default CSS files
		$this->css[] = 'reset.css';
		$this->css[] = 'prototype.css';
		$this->css[] = 'prototype_edit.css';
		$this->css[] = 'data.css';
		//$this->css[] = 'edit.css';
		$this->css[] = 'imagebrowser.css';
		// Remove duplicates
		$files = array_unique($this->css);
		// Build links
		$r = '';
		foreach ($files as $filename) {
			$r .= sprintf(
				'<link rel="stylesheet" href="%s" type="text/css" media="screen" charset="utf-8" />',
				BASE . 'assets/css/' . $filename
			);
		}
		return $r;
	}
	
	
	
	public function prepUI()
	{
		
		$file = MODELS . 'UserModel.php';
		include $file;
		$m = new UserModel();
		$tablesA = $m->getNavigation();
		
		
		$_tablesA = array(
			array(
				'name'=>'Content',
				'tables'=>array(
					'Awards',
					'Contact',
					'Distributor links',
					'Distributors',
					'Files',
					'Home features',
					'Home images',
					'News',
					'Product features',
					'Publications',
					'Publications categories',
					'Sections'
					
					)),
			array(
				'name'=>'Admin',
				'tables'=>array(
					'History',
					'Groups',
					'Users'					
					))
			
		);
				
		$this->view(array('container'=>'ui_nav','view'=>'/_modules/ui_nav','data'=>array('tableA'=>$tablesA)));
		
		$this->view(array('container'=>'ui_toolbar','view'=>'/_modules/ui_toolbar',
			'data'=>array(
				'controller'=>$this->route['controller'],
				'table'=>$this->route['table'],
				'tablename'=>$this->route['table'],
				'mode'=>$this->mode,
				'id'=>$this->id)));
		
		$this->view(array('container'=>'ui_breadcrumb','view'=>'/_modules/ui_breadcrumb','data'=>array('table'=>$this->route['table'],'tablename'=>$this->route['table'])));
		
		$this->view(array('container'=>'ui_session','view'=>'/_modules/ui_session','data'=>''));
	}
	
}