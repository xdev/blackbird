<?php

class UserModel extends Model
{
	//run this everytime
	public function __construct()
	{
		//query the users table
		$this->logged = false;
		$this->admin = false;
		$this->super_user = false;
		
		$this->db = AdaptorMysql::getInstance();
		//get all tables and such
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
					
					$this->user = $q;
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
	}
	
	private function getTables()
	{
		//search through all the tables of all the groups this user belongs to.
		$t_id = $this->u_id; //1
		$q = $this->db->queryRow("SELECT * FROM ".BLACKBIRD_TABLE_PREFIX."users WHERE id = '$t_id'");
		
		$tables = array();
		
		if($q['super_user'] == 1){
		
			$q = $this->db->query("SHOW TABLES",MYSQL_BOTH);
			
			foreach($q as $table){
				//merge this data with the menu id from cms_tables, otherwise, add it to a special segment (other) = 0
				//die(print_r($table));
				$qT = $this->db->queryRow("SELECT * FROM ".BLACKBIRD_TABLE_PREFIX."tables WHERE table_name = '$table[0]'");
				$menu = -1;
				if($qT['menu_id'] != ''){
					$menu = $qT['menu_id'];
				}
				$tables[] = array('name'=>$table[0],'permissions'=>'select,insert,update,delete','menu'=>$menu,'in_nav'=>1);
			}
			
			//this person has all the privs
			$this->super_user = true;
			$this->admin = true;
					
		}else{
			
			//get groups from linking - in a join, of course
			
			if($q_groups = $this->db->query("SELECT * FROM ".BLACKBIRD_TABLE_PREFIX."users__groups WHERE user_id = '$t_id'")){				
				foreach($q_groups as $group){

					$qGroup = $this->db->queryRow("SELECT * FROM ".BLACKBIRD_TABLE_PREFIX."groups WHERE id = '$group[group_id]'");

					//sets repeatedly but will not revoke (summation of privs)
					if($qGroup['admin'] == 1){
						$this->admin = true;
					}				

					$group_id = $group['group_id'];
					if($q_permissions = $this->db->query("SELECT * FROM " . BLACKBIRD_TABLE_PREFIX . "permissions WHERE group_id = '$group_id' ORDER BY table_name")){


						foreach($q_permissions as $row){
							$t = $row['table_name'];
							//$t = sprintf($mytable['name']);					
							$tA = Utils::checkArray(_ControllerFront::$config['tables'],array('table_name'=>$t));
							if(is_array($tA)){
								$qT = $tA;
							}else{				
								$qT = $this->db->queryRow("SELECT * FROM ".BLACKBIRD_TABLE_PREFIX."tables WHERE table_name = '$t'");
							}

							if($qT['menu_id'] != 0){
								$menu = $qT['menu_id'];
								$in_nav = $qT['in_nav'];
							}else{
								$menu = '';
								$in_nav = 0;
							}

							//we're a table, but we didn't put a _tables record to map to a group or whatnot
							//show in default group?
							if($qT === false){
								$in_nav = 1;
							}

							$permissions = '';
							
							$tt = array('name'=>$t,'permissions'=>$this->formatPermissions($row),'menu'=>$menu,'in_nav'=>$in_nav);
							$tables[] = $tt;	
						}

					}
				}
				
				if($this->admin === true){
					//if we're an admin, add specific permissions hardcoded here - this might come out of config for the site as well (later)
					$tables[] = array('name'=>BLACKBIRD_TABLE_PREFIX.'groups','permissions'=>'select,insert,update,delete','menu'=>'','in_nav'=>0);
					$tables[] = array('name'=>BLACKBIRD_TABLE_PREFIX.'users','permissions'=>'select,insert,update,delete','menu'=>'','in_nav'=>0);
					//consider blanket access, but then again, maybe not, this could be another breakout checkbox on the group
					$tables[] = array('name'=>BLACKBIRD_TABLE_PREFIX.'history','permissions'=>'select','menu'=>'','in_nav'=>0);
					
					//leave the other tables only for super_user role					
				}
				
				
			}	
			
			
			
			
		}
		
		$this->tables = $tables;
		
	}
	
	private function formatPermissions($row)
	{
		$r = array();
		$tA = array('select','insert','update','delete');
		for($i=0;$i<count($tA);$i++){
			if($row[$tA[$i].'_priv'] == '1'){
				$r[] = $tA[$i]; 
			}
		}
		return join(',',$r);
	}
	
	public function getNavigation()
	{
		$navA = array();
		if($this->logged==true){
			$tables = $this->prepTables();
					
			foreach($tables as $key=>$value){
				if($value['in_nav'] == 1){
					if(!isset($navA[$value['menu']])){
						// != '' && $value['menu'] != 0
						if($value['menu'] != -1){
							$q_name = $this->db->queryRow("SELECT * FROM ".BLACKBIRD_TABLE_PREFIX."menus WHERE id = '$value[menu]'");
							$name = $q_name['name'];
						}else{
							$name = '__DEFAULT__';
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
				if($key == -1){
					$tempA[] = array('position'=>-1,'value'=>$value,'key'=>-1);
				}elseif($q = $this->db->queryRow("SELECT * FROM ".BLACKBIRD_TABLE_PREFIX."menus WHERE id = '$key'")){
					$tempA[] = array('position'=>$q['position'],'value'=>$value,'key'=>$key);
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
				$new[$table['name']] = array('privs'=>$table['permissions'],'menu'=>$table['menu'],'in_nav'=>$table['in_nav']);
			}else{
				$new[$table['name']]['privs'] .= ',' . $table['permissions'];
			}
		}
				
		foreach($new as $key=>$value){
			$privs = array_unique(split(',',$value['privs']));
			
			if($privs[0]){
				$new[$key] = array('privs'=>$privs,'menu'=>$value['menu'],'in_nav'=>$value['in_nav']);
			}else{
				unset($new[$key]);
			}
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