<?php

class UserModel extends Model
{
	//run this everytime
	public function __construct()
	{
		//query the users table
		$this->logged = false;
		$this->db = AdaptorMysql::getInstance();
		//get all tables and such
		
		/*
		if (!file_exists(CMS_FILESYSTEM.'tmp')) {
			mkdir(CMS_FILESYSTEM.'tmp',0700);
			if (!file_exists(CMS_FILESYSTEM.'tmp/.htaccess')) {
				if (!file_put_contents(CMS_FILESYSTEM.'tmp/.htaccess','deny from all')) die('nofile');
			}
		}
		session_save_path(CMS_FILESYSTEM.'tmp');
		*/		
	}
	
	public function checkSession()
	{
		//are we logged in, if not die, and throw us to the login page
		session_name("Blackbird_sid");
		session_start();
		
		if(isset($_COOKIE['Blackbird_sid'])){

			if(isset($_SESSION['u_id']) && isset($_SESSION['u_token'])){
				$tid = $_SESSION['u_id'];
				$pass = $_SESSION['u_token'];

				if($q = $this->db->queryRow("SELECT * FROM `" . BLACKBIRD_TABLE_PREFIX . "users` WHERE id = '$tid' AND password = '$pass'")){

					$this->u_id = $q['id'];
					$this->u_row = $q;
					$this->logged = true;
					$this->displayname = $q['firstname'] . " " . $q['lastname'];

					if($q['super_user'] == 1){
						$this->super_user = true;
					}
					
					if (isset($q['active']) && !$q['active']) {
						$this->redirect();
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
	
	public function checkUser($login)
	{
		return $this->db->queryRow("SELECT * FROM " . BLACKBIRD_USERS_TABLE . " WHERE email = '$login'");		
	}
	
	public function resetPassword($id)
	{
		$string = md5(time());
		$h_s = 32;
		$pass = substr($string,rand(0,$h_s),16);
		
		$row_data = array(array('field'=>'password', 'value'=>sha1($pass) ));
		if($this->db->update(BLACKBIRD_USERS_TABLE,$row_data,"id",$id)){
			return $pass;
		}		
	}
	
	private function validatePasswordStrength($value)
	{
		//run regular expression
		if($value != ''){
			return true;
		}else{
			return false;
		}
	}
	
	public function updateUser($data)
	{
		$row_data = array();
		foreach($data as $key=>$value){
			$update = true;
			if($key == 'password_reset'){
				if($this->validatePasswordStrength($value)){
					$key = 'password';
					$value = sha1($value);
				}else{
					$update = false;
				}
			}
			if($update){
				$row_data[] = array('field'=>$key,'value'=>$value);
			}
		}
		
		if($this->db->update(BLACKBIRD_USERS_TABLE,$row_data,"id",$this->u_id)){
			return true;
		}
	}
	
	private function redirect()
	{
		if(isset($_SERVER['REQUEST_URI'])){
			if(substr($_SERVER['REQUEST_URI'],-(strlen('index.php') + 1)) != BASE){
				Utils::metaRefresh(BASE . "user/login/?redirect=$_SERVER[REQUEST_URI]");
			}
		}
		Utils::metaRefresh(BASE . "user/login");
		die();
	}
	
	public function login($user,$pass)
	{
		//not sure where we want to go with this one
		$email = $user;
		
		if($q = $this->db->queryRow("SELECT id FROM `" . BLACKBIRD_TABLE_PREFIX . "users` WHERE email = '$email' AND password = '$pass'")){
			
			$row_data = array();
			$row_data[] = array('field'=>'user_id','value'=>$q['id']);
			$row_data[] = array('field'=>'start_time','value'=>Utils::now());
			$row_data[] = array('field'=>'session_id','value'=>session_id());
			$this->db->insert(BLACKBIRD_TABLE_PREFIX . "sessions",$row_data);
						
			session_name("Blackbird_sid");
			session_start();
			
			$_SESSION['u_id'] = $q['id'];
			$_SESSION['u_token'] = $pass;
			return true;
			
		}else{
			return false;
		}
	}
	
	public function logout()
	{
		//session_save_path(CMS_FILESYSTEM.'tmp');
		session_name("Blackbird_sid");
		session_start();
				
		$row_data = array();
		$row_data[] = array('field'=>'end_time','value'=>Utils::now());
		$this->db->update(BLACKBIRD_TABLE_PREFIX . "sessions",$row_data,'session_id',session_id());
		
		$_SESSION = array();
				
		if (isset($_COOKIE["Blackbird_sid"])) {
			setcookie("Blackbird_sid", '', time()-42000, '/');
		}
		
		session_destroy();		
		//$this->cms->session->logged = false;
	}
	
	private function getTables()
	{
		//search through all the tables of all the groups this user belongs to.
		$t_id = $this->u_id; //1
		$q = $this->db->queryRow("SELECT groups,super_user FROM ".BLACKBIRD_TABLE_PREFIX."users WHERE id = '$t_id'");
		
		$tables = array();
		
		if($q['super_user'] == 1){
		
			$q = $this->db->query("SHOW TABLES",MYSQL_BOTH);
			
			foreach($q as $table){
				$tables[] = array('name'=>$table[0],'value'=>'browse,insert,update,delete','menu'=>0,'in_nav'=>1);
			}
					
		}else{
			
			$groups = explode(',',$q['groups']);
			
			foreach($groups as $group){
			
				$qGroup = $this->db->queryRow("SELECT `tables` FROM ".BLACKBIRD_TABLE_PREFIX."groups WHERE id = '$group'");
				$xml = simplexml_load_string($qGroup['tables']);
							
				foreach($xml->table as $mytable){
					$t = sprintf($mytable['name']);					
					$tA = Utils::checkArray(_ControllerFront::$config['tables'],array('table_name'=>$t));
					if(is_array($tA)){
						$qT = $tA;
					}else{				
						$qT = $this->db->queryRow("SELECT * FROM ".BLACKBIRD_TABLE_PREFIX."tables WHERE table_name = '$t'");
					}
					$qT = $this->db->queryRow("SELECT * FROM ".BLACKBIRD_TABLE_PREFIX."tables WHERE table_name = '$t'");
					
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
		$navA = array();
		if($this->logged==true){
			$tables = $this->prepTables();
					
			foreach($tables as $key=>$value){
				if($value['in_nav'] == 1){
			
					if(!isset($navA[$value['menu']])){
						if($value['menu'] != '' && $value['menu'] != 0){
							$q_name = $this->db->queryRow("SELECT * FROM ".BLACKBIRD_TABLE_PREFIX."menus WHERE id = '$value[menu]'");
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
					if($q = $this->db->queryRow("SELECT * FROM ".BLACKBIRD_TABLE_PREFIX."menus WHERE id = '$key'")){
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
	
	public function getPermissions($priv,$table_name)
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
	
	public function getHistory($id,$limit=50)
	{
		return $this->db->query("SELECT * FROM " . BLACKBIRD_TABLE_PREFIX . "history WHERE user_id = '$id' ORDER BY modtime DESC LIMIT $limit");
	}
	
	public function getRecord($id)
	{
		return $this->db->queryRow("SELECT * FROM " . BLACKBIRD_TABLE_PREFIX . "users WHERE id = '$id'");
	}
	
}