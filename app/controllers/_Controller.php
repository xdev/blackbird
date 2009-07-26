<?php

class _Controller extends Controller
{
		
	public function __construct($route,$loadmodel=true)
	{
		parent::__construct($route,$loadmodel);		
	}
	
	public function render()
	{
		//if we have a layout - should get more specific, if we have an html master layout
		if($this->layout_view){
			$this->prepUI();
		}
		
		return parent::render();
	}
	
	public function getCustomHeaders()
	{
		$r = '';
		if($q = AdaptorMysql::query("SELECT * FROM `" . BLACKBIRD_TABLE_PREFIX . "headers`")){
			foreach($q as $row){
				$r .= $row['javascript'] . "\t\t\n" . $row['css'] . "\t\t\n";
			}
		}
		return $r;
	}
	
	public function prepUI()
	{
				
		$tablesA = _ControllerFront::$session->getNavigation();
		
		$this->view(array('container'=>'ui_nav','view'=>'/_modules/ui_nav','data'=>array('tableA'=>$tablesA)));
		
		//do something here
		if(isset($this->route['table'])){
			$table = $this->route['table'];
			$tablename = _ControllerFront::getTableName($table);
		}else{
			$table = '';
			$tablename = '';
		}
		
		$this->view(array('container'=>'ui_breadcrumb','view'=>'/_modules/ui_breadcrumb','data'=>array('table'=>$table,'tablename'=>$tablename)));
		
		$this->view(array('container'=>'ui_session','view'=>'/_modules/ui_session','data'=>''));
	}
	
}