<?php

class UserModel extends Model
{
	//run this everytime
	public function __construct()
	{
		//query the users table
		$this->db = AdaptorMysql::getInstance();
		//get all tables and such
		$this->getTables();
	}
	
	private function getTables()
	{
		//search through all the tables of all the groups this user belongs to.
		$t_id = 1;//$this->u_id;
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
		$tables = $this->prepTables();
		$navA = array();
		
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