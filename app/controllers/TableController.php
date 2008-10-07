<?php

class TableController extends _Controller
{
	
	public function Browse()
	{
		$this->view(array('data'=>array('name_space'=>'main','table'=>$this->route['table'],'mode'=>'main')));
	}
	
	public function Datagrid()
	{
		$this->layout_view = null;
		$this->view(array('data'=>$this->model->getData($_POST['table'])));
	}
	
}