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
		$this->layout_view = 'login';
		$this->layout_file = VIEWS . '_layouts' . DS . $this->layout_view . '.' . VIEW_EXTENSION;
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
		}else{
			
			$link = '<a href="' . BASE . 'user/reset' . (isset($login) ? "?login=$login" : '') . '">';
						
			$this->errorData = array();
			$this->errorData[] = array('field'=>'password','error'=>'Incorrect password! Please try again or ' . $link . 'reset your password</a>.');
			
			//$GLOBALS['errors'] = $this->errorData;
			$this->view(array('view'=>'/_errors/remote','data'=>array(
				'mode'=>'',
				'query_action'=>'',
				'channel'=>'',
				'name_space'=>'login',
				'table'=>'',
				'id'=>'',
				'errors'=>$this->errorData)));
			
		}
	}
	
	public function Processreset()
	{
		
		$this->layout_view = null;
		$this->errorData = array();
		
		//check that it's a valid user
		if($q = $this->model->checkUser($_POST['login_login'])){
			//reset password
			if($pass = $this->model->resetPassword($q['id'])){
			
				//prep email
				$html_template =  "<h2>Your password has been reset!</h2>";
				$html_template .= "<p>$pass</p>";
				$html_template .= "<p>To log in <a href=\"http://$_SERVER[HTTP_HOST]" . BASE . "user/login\">Click Here</a></p>";

				$message = array();
				$message['to_address'] = $q['email'];
				$message['to_name'] = $q['firstname'] . ' ' . $q['lastname'];
				$message['subject'] = 'Blackbird Password Reset';
				$message['body'] = $html_template;
				
				//email it out
				_ControllerFront::sendEmail($message);
				//refresh to a new page or something with a success message? or display inline, perhaps ,replacing the form with the success message (2.1)
				$this->view();
			}else{
				//needs to go in general error zone
				$this->errorData[] = array('field'=>'login','error'=>'Unable to reset password!');
			}									
		}else{			
			$this->errorData[] = array('field'=>'login','error'=>'Fail! No user exists.');			
		}
		
		if(count($this->errorData) > 0){
			$this->view(array('view'=>'/_errors/remote','data'=>array(
				'mode'=>'',
				'query_action'=>'',
				'channel'=>'',
				'name_space'=>'login',
				'table'=>'',
				'id'=>'',
				'errors'=>$this->errorData)));			
		}
	}
	
	public function Reset()
	{
		
		$this->layout_view = 'login';
		$this->layout_file = VIEWS . '_layouts' . DS . $this->layout_view . '.' . VIEW_EXTENSION;
		
		$login = Utils::setVar("login","");		
		
		$this->view(array('data'=>array(
			'name_space'=>'login',
			'login'=>$login
			)));
	}
	
	public function Logout()
	{
		$this->layout_view = 'login';
		$this->layout_file = VIEWS . '_layouts' . DS . $this->layout_view . '.' . VIEW_EXTENSION;
		$this->model->logout();
		$this->view();
		Utils::metaRefresh(BASE . 'user/login?loggedout');
	}
	
	public function Edit()
	{
		$this->view(array('data'=>array(
			'name_space'=>'user',
			'row'=>$this->model->u_row
			)));
	}
	
	public function Processedit()
	{
		$this->layout_view = null;
		//manually process stuff eh
		if($this->model->updateUser(array(
			'firstname'=>$_POST['user_firstname'],
			'lastname'=>$_POST['user_lastname'],
			'email'=>$_POST['user_email'],
			'password_reset'=>$_POST['user_password_reset']))){
			//send on to something nice
			
			$this->view();
				
		}else{
			
			//return errors
			
		}
	}
	
	public function Admin()
	{
		$this->view();
	}
	
	public function Profile()
	{
		$history = $this->model->getHistory($this->route['user'],50);
		$record = $this->model->getRecord($this->route['user']);
		$this->view(array('data'=>array(
			'history'=>$history,
			'record'=>$record)
			)			
		);
	}
		
}