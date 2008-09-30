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
		$this->css[] = 'imagebrowser.css';
		// Remove duplicates
		$files = array_unique($this->css);
		// Build links
		$r = '';
		foreach ($files as $filename) {
			$r .= "\r\t\t" . sprintf(
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
		
		$this->view(array('container'=>'ui_nav','view'=>'/_modules/ui_nav','data'=>array('tableA'=>$tablesA)));
		
		$this->view(array('container'=>'ui_breadcrumb','view'=>'/_modules/ui_breadcrumb','data'=>array('table'=>$this->route['table'],'tablename'=>$this->route['table'])));
		
		$this->view(array('container'=>'ui_session','view'=>'/_modules/ui_session','data'=>''));
	}
	
}