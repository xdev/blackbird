<?php

class _Controller extends Controller
{
	
	protected $front;
	
	public function __construct($route)
	{
		parent::__construct($route);
		$this->front = _ControllerFront::getInstance();
		$this->prepUI();
	}
	
	public function actions()
	{
		return '
		<ul id="bb_main_nav_actions">
			<li id="bb_nav_action_expand">Expand</li>
			<li id="bb_nav_action_collapse">Collapse</li>
		</ul>';
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
				WEB . 'assets/css/' . $filename
			);
		}
		return $r;
	}
	
	public function prepUI()
	{
		$tA = array(
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
		
		$this->view(array('container'=>'ui_nav','view'=>'/_modules/ui_nav','data'=>array('tableA'=>$tA)));
		
		$this->view(array('container'=>'ui_toolbar','view'=>'/_modules/ui_toolbar','data'=>''));
		
		$this->view(array('container'=>'ui_breadcrumb','view'=>'/_modules/ui_breadcrumb','data'=>''));
		
		$this->view(array('container'=>'ui_session','view'=>'/_modules/ui_session','data'=>''));
	}
	
}