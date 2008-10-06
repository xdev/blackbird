<?php

class DashboardController extends _Controller
{
	
	//dashboard I think	
	public function Index()
	{
		
		$this->view(array('data'=>array(
			'activity'=>$this->model->getActivity(),
			'users'=>$this->model->getUsers(),
			'tables'=>$this->model->getTables()
			)));
		
	}
	
}