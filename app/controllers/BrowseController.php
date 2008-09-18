<?php

class BrowseController extends _Controller
{
	
	public function Index()
	{
		$this->view();
		
		$this->front->setTable();
	}
	
}