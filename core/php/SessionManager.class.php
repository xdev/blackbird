<?php

class SessionManager extends Session
{

	public function __construct($cms)
	{
		$this->db = $cms->db;
	}

	public function login($id,$pass,$email,$time = 1200)
	{
		session_name("s_id");
		session_start();
			
		$_SESSION['u_id'] = $id;
		$_SESSION['u_token'] = $pass;
		
	}
	
	/**
	* check
	* checks to see if user is logged and authenticated
	* redirects to login otherwise
	* should loop through logout.php    
	*
	*/
	
	public function check()
	{
					
		session_name("s_id");
		session_start();
		
		$this->logged = false;
		
		if(isset($_COOKIE['s_id'])){
			
			if(isset($_SESSION['u_id']) && isset($_SESSION['u_token'])){
				$tid = $_SESSION['u_id'];
				$pass = $_SESSION['u_token'];
				
				$q = $this->db->queryRow("SELECT id,firstname,lastname,super_user FROM `cms_users` WHERE id = '$tid' AND password = '$pass'");
				
				if(isset($q['id'])){
					$this->u_id = $q['id'];
					$this->logged = true;
					$this->displayname = $q['firstname'] . " " . $q['lastname'];
															
					if($q['super_user'] == 1){
						$this->super_user = true;
					}
					
				}else{
					
					$this->redirect();
				}
			}else{
			
				$this->redirect();
			}
								
		}else{
			
			$this->redirect();
		}

	
	}
	
	private function redirect()
	{
		
		if(isset($_SERVER['REQUEST_URI'])){
			if(substr($_SERVER['REQUEST_URI'],-(strlen('index.php') + 1)) != CMS_ROOT){
				Utils::metaRefresh(CMS_ROOT . "login/?redirect=$_SERVER[REQUEST_URI]");
			}
		}
		
		Utils::metaRefresh(CMS_ROOT . "login");		
		die();
	}
	
	/**
	* check
	* checks to see if user is qualified for operation
	*
	* @param   string   priv
	*
	* @return  boolean  authentication
	*/
	
	public function getTables($mode='')
	{
				
		//search through all the tables of all the groups this user belongs to.
		$t_id = $this->u_id;
		$q = $this->db->queryRow("SELECT groups,super_user FROM cms_users WHERE id = '$t_id'");
		
		$tables = array();
		
		if($q['super_user'] == 1){
		
			$q = $this->db->query("SHOW TABLES");
			
			foreach($q as $table){
				$tables[] = array('name'=>$table[0],'value'=>'browse,insert,update,delete');
			}
		
		}else{
			
			$groups = explode(',',$q['groups']);
			
			
			foreach($groups as $group){
			
				$qGroup = $this->db->queryRow("SELECT `tables` FROM cms_groups WHERE id = '$group'");
				$xml = simplexml_load_string($qGroup['tables']);
							
				foreach($xml->table as $mytable){
					$t = sprintf($mytable['name']);
									
					if($mode == 'navigation'){
					
						$qT = $this->db->queryRow("SELECT * FROM cms_tables WHERE table_name = '$t'");
						if($qT['in_nav'] == 1){
							$tables[] = array('name'=>$t,'value'=>sprintf($mytable));
						}
					
					}else{
						$tables[] = array('name'=>$t,'value'=>sprintf($mytable));
					}
					
					
				}
				
			}
		
		}
		
		$tables = Utils::arraySort($tables,'name');
		
		$new = array();
		
		foreach($tables as $table){
			if(!isset($new[$table['name']])){
				$new[$table['name']] = $table['value'];				
			}else{
				$new[$table['name']] .= ',' . $table['value'];
			}
		}
				
		foreach($new as $key=>$value){
			$tA = explode(',',$value);
			$privs = array_unique($tA);
			$new[$key] = $privs;		
		}
		
		$tables = $new;	
		return $tables;
	
	
	}
	
	
	
	public function tablePrivs($table)
	{
	
		$tables = $this->getTables();
				
		foreach($tables as $key=>$value){
			if(isset($tables[$table])){
				return true;
			}
		}
		
		return false;
		
	}
	
	
	public function privs($priv,$table)
	{
		
		$tables = $this->getTables();
		if(isset($tables[$table])){
			if(in_array($priv,$tables[$table])){
				return true;
			}else{
				return false;
			}
		}
		return false;
	
	}
	

}
?>