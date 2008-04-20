<?php

/* $Id$ */

class SessionManager extends Session
{

	public function __construct($cms)
	{
		$this->db = $cms->db;
		$this->cms = $cms;
		// Write sessions to a 'tmp' directory in the CMS ROOT
		if (!file_exists(CMS_FILESYSTEM.'tmp')) {
			mkdir(CMS_FILESYSTEM.'tmp',0700);
			if (!file_exists(CMS_FILESYSTEM.'tmp/.htaccess')) {
				if (!file_put_contents(CMS_FILESYSTEM.'tmp/.htaccess','deny from all')) die('nofile');
			}
		}
		session_save_path(CMS_FILESYSTEM.'tmp');
	}

	public function login($pass,$email,$time = 1200)
	{
		
		
		
		if($q = $this->db->queryRow("SELECT id FROM `cms_users` WHERE email = '$email' AND password = '$pass'")){
			
			$row_data = array();
			$row_data[] = array('field'=>'user_id','value'=>$q['id']);
			$row_data[] = array('field'=>'start_time','value'=>Utils::now());
			$row_data[] = array('field'=>'session_id','value'=>session_id());
			$this->db->insert('cms_sessions',$row_data);
		
			session_name("BlackbirdCMS_sid");
			session_start();
			
			$_SESSION['u_id'] = $q['id'];
			$_SESSION['u_token'] = $pass;
			
			return true;
			
		}else{
			return false;
		}
		
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
		session_name("BlackbirdCMS_sid");
		session_start();
				
		$this->logged = false;
		
		if(isset($_COOKIE['BlackbirdCMS_sid'])){
			
			if(isset($_SESSION['u_id']) && isset($_SESSION['u_token'])){
				$tid = $_SESSION['u_id'];
				$pass = $_SESSION['u_token'];
				
				if($q = $this->db->queryRow("SELECT * FROM `cms_users` WHERE id = '$tid' AND password = '$pass'")){
					$this->u_id = $q['id'];
					$this->u_row = $q;
					$this->logged = true;
					$this->displayname = $q['firstname'] . " " . $q['lastname'];
															
					if($q['super_user'] == 1){
						$this->super_user = true;
					}
					
					$this->getTables();					
					
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
	}
	
	/**
	* check
	* checks to see if user is qualified for operation
	*
	* @param   string   priv
	*
	* @return  boolean  authentication
	*/
	
	private function getTables()
	{
		//search through all the tables of all the groups this user belongs to.
		$t_id = $this->u_id;
		$q = $this->db->queryRow("SELECT groups,super_user FROM cms_users WHERE id = '$t_id'");
		
		$tables = array();
		
		if($q['super_user'] == 1){
		
			$q = $this->db->query("SHOW TABLES");
			
			foreach($q as $table){
				$tables[] = array('name'=>$table[0],'value'=>'browse,insert,update,delete','menu'=>0,'in_nav'=>1);
			}
					
		}else{
			
			$groups = explode(',',$q['groups']);
			
			foreach($groups as $group){
			
				$qGroup = $this->db->queryRow("SELECT `tables` FROM cms_groups WHERE id = '$group'");
				$xml = simplexml_load_string($qGroup['tables']);
							
				foreach($xml->table as $mytable){
					$t = sprintf($mytable['name']);
					$tA = Utils::checkArray($this->cms->config['cms_tables'],array('table_name'=>$t));
					if(is_array($tA)){
						$qT = $tA;
					}else{				
						$qT = $this->db->queryRow("SELECT * FROM cms_tables WHERE table_name = '$t'");
					}
					if($qT['menu_id'] != '' && $qT['menu_id'] != 0){
						$menu = $qT['menu_id'];
					}else{
						$menu = 'cms_admin';
					}
					
					
					$tt = array('name'=>$t,'value'=>sprintf($mytable),'menu'=>$menu,'in_nav'=>$qT['in_nav']);
					$tables[] = $tt;	
				}
				
			}
		
		}
		
		$this->tables = $tables;
	
	
	}
	
	public function getNavigation()
	{
		$tables = $this->prepTables();
		$navA = array();
		
		foreach($tables as $key=>$value){
			if($value['in_nav'] == 1){
			
				if(!isset($navA[$value['menu']])){
					if($value['menu'] != '' && $value['menu'] != 0){
						$q_name = $this->db->queryRow("SELECT * FROM cms_menus WHERE id = '$value[menu]'");
						$name = $q_name['name'];
					}else{
						$name = 'Admin';
					}
					$navA[$value['menu']] = array('id'=>$value['menu'],'name'=>$name,'tables'=>array($key));
				}else{
					$navA[$value['menu']]['tables'][] = $key;
				}
			}
		}
		
		//order all the menu sets by their position-- needs improvement		
		$tempA = array();
		foreach($navA as $key=>$value)
		{
			if($key != 'cms_admin'){
				if($q = $this->db->queryRow("SELECT * FROM cms_menus WHERE id = '$key'")){
					$tempA[] = array('position'=>$q['position'],'value'=>$value,'key'=>$key);
				}
			}else{
				//adds to the end, could push to sorted array later
				$tempA[] = array('position'=>10000,'value'=>$value,'key'=>$key);
			}
		}
		$tempA = Utils::arraySort($tempA,'position');
		$navA = array();
		foreach($tempA as $item)
		{
			$navA[] = $tempA[$item['key']] = $item['value'];
		}
		
		return $navA;
	}
	
	public function prepTables()
	{
		//find total sum of privileges
		$tables = $this->tables;
		
		Utils::arraySort($tables,'name');		
		$new = array();
		
		foreach($tables as $table){
			if(!isset($new[$table['name']])){
				$new[$table['name']] = array('privs'=>$table['value'],'menu'=>$table['menu'],'in_nav'=>$table['in_nav']);
			}else{
				$new[$table['name']]['privs'] .= ',' . $table['value'];
			}
		}
				
		foreach($new as $key=>$value){
			$privs = array_unique(split(',',$value['privs']));
			$new[$key] = array('privs'=>$privs,'menu'=>$value['menu'],'in_nav'=>$value['in_nav']);	
		}
		
		$tables = $new;
		return $tables;
	}
	
	public function privs($priv,$table_name)
	{
		
		$tables = $this->prepTables();
	
		if(isset($tables[$table_name])){
			if(in_array($priv,$tables[$table_name]['privs'])){
				return true;
			}else{
				return false;
			}
		}
		return false;
	
	}
	

}
?>