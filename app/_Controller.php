<?php

class _Controller extends Controller
{
	
	protected $front;
	
	public function __construct($route)
	{
		parent::__construct($route);
		$this->front = _ControllerFront::getInstance();
	}
	
}