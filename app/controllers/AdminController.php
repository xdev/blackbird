<?php

class AdminController extends _Controller
{

	public function Index()
	{
		
		$this->loadModel('Dashboard');
		$m = new DashboardModel();
		
		$this->view(array('data'=>array(
			'users'=>$m->getUsers(),
			'groups'=>$this->model->getGroups()
			)));
	}
	
}