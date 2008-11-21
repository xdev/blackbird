<?php

class TableController extends _Controller
{
	
	public function Browse()
	{
		if(_ControllerFront::$session->getPermissions('select',$this->route['table']) === false){
			$this->view(array('view'=>'error'));
			return;
		}
		
		$permission_add = null;
		if(_ControllerFront::$session->getPermissions('insert',$this->route['table']) === true){
			$permission_add = true;
		}
		$this->view(array('data'=>array('name_space'=>'main','table'=>$this->route['table'],'mode'=>'main','permission_add'=>$permission_add)));
	}
	
	public function Datagrid()
	{
		$this->layout_view = null;
		$this->view(array('data'=>$this->model->getData($_POST['table'])));
	}
	
}