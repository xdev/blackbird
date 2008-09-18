<?php

// handle 1 off pages of info
class PageController extends _Controller
{
	
	public function Index()
	{
		$this->layout(array('data'=>array('htmlTitle'=>'Genero Page')));
		$this->view(array('data'=>array('title'=>'fluffy the cat is cranky')));
	}
	
	public function Landing()
	{
		$this->layout(array('data'=>array('htmlTitle'=>'Landing Page')));
		$this->view(array('data'=>array('title'=>'Cornerstone','subtitle'=>'Worship Band','body'=>'Please contact so and so to schedule an audition.'),'container'=>'main'));
		$this->view(array('container'=>'sidebar','view'=>'/_modules/sample','data'=>array('testA'=>array('1','2','4'))));
	}
	
}
















