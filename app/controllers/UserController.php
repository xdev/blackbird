<?php

class UserController extends _Controller
{
	public function __construct($route)
	{
		parent::__construct($route,false);
		$this->model = _ControllerFront::getSession();
	}
	//	
	public function Login()
	{
		//render login page
		$this->view(array('data'=>array(
			'name_space'=>'login'
			)));
	}
	
	public function Processlogin()
	{
		$this->layout_view = null;
		
		$login = $_POST['login_login'];
		$pass = sha1($_POST['login_password']);
		
		if($this->model->login($login,$pass)){
			//redirect to home?
			$this->view();
		}
	}
	
	public function Logout()
	{
		$this->model->logout();
		
		$this->view();
	}
	
	public function Edit()
	{
		$this->view();
	}
	
	public function Add()
	{
		$this->view();
	}
	
	public function Process()
	{
		//handle an update or insert to the table		
	}
		
}