<?php

class PageController extends _Controller
{

	public function About()
	{
		$this->layout_view = null;
		$this->view();
	}
	
}