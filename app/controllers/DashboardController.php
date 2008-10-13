<?php

class DashboardController extends _Controller
{
	
	public function Index()
	{
		$this->view(array('data'=>array(
			'activity'=>$this->model->getActivity(),
			'users'=>$this->model->getUsers(),
			'tables'=>$this->model->getTables(),
			'chart_users'=>$this->model->getChartUsers(),
			'chart_tables'=>$this->model->getChartTables(),
			'chart_edits'=>$this->model->getChartEdits()
			)));
		
	}
	
}