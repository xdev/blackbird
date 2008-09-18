<?php

class _ControllerFront extends ControllerFront
{
	
	/*
	Layout helper methods in here
	
	Session info in here
	*/
	
	private function __construct()
	{
		self::setUri();
		
		/*
			// make sure '.htaccess' file is present - if not, try to create it from 'htaccess' file
			if (!file_exists(CMS_FILESYSTEM.'.htaccess')) {
				if (!file_exists(CMS_FILESYSTEM.'htaccess')) {
					die('.htaccess file not found');
				}
				if (!copy(CMS_FILESYSTEM.'htaccess',CMS_FILESYSTEM.'.htaccess')) {
					die('.htaccess file could not be created');
				}
			}

			//load core classes
			if (file_exists(LIB)) {
				require_once(LIB.'database/Db.interface.php');
				require_once(LIB.'forms/Forms.class.php');
				require_once(LIB.'utils/Utils.class.php');
				require_once(LIB.'session/Session.class.php');
				require_once(LIB.'_version.php');	

				if(defined('CMS_SESSION_MANAGER')){
					require_once(CMS_SESSION_MANAGER);
				}else{
					require_once(INCLUDES.'SessionManager.class.php');
				}

				if(!isset($DB['adaptor'])){
					$DB['adaptor'] = 'Mysql';
				}

				$class = 'Adaptor' . $DB['adaptor'];
				require LIB . 'database/' . $class .  '.class.php';
				$this->db = new $class();

				$this->db->sql('SET NAMES utf8');

				self::checkDB();

				self::setConfig();
				$this->session = new SessionManager($this);
			} else {
				die('Bobolink PHP library is not properly installed');
			}

			// Check to see if we have a sufficient schema installed
			if($this->db->query("SHOW TABLES LIKE 'cms_info'")){
				if($q = $this->db->queryRow("SELECT * FROM cms_info WHERE name = 'schema_version'")){
					if($q['value'] < REQUIRED_SCHEMA_VERSION){
						die('You have an outdated SQL schema, please run the update script');
					}
				}			
			}else{
				die('You have an outdated SQL schema, please run the update script');
			}

			$this->pathA = explode("/",substr($_SERVER["REQUEST_URI"],1));
			$tA = explode("/",substr($_SERVER['PHP_SELF'],1,-(strlen('index.php') + 1)));

			if(isset($_SERVER["HTTP_REFERER"])){
				$this->refA = explode("/",$_SERVER["HTTP_REFERER"]);
				$t = array_search($_SERVER['SERVER_NAME'],$this->refA);
				$this->refA = array_slice($this->refA, ($t+1));
			}

			//if we are running from a folder, or series of folders splice away the unused bits		
			if($tA[0] != ''){
				array_splice($this->pathA,0,count($tA));
				if(isset($this->refA)){
					$this->refA = array_slice($this->refA,count($tA)); 
				}
			}		
			$this->path = $this->pathA;

			if(isset($this->pathA[0])){
				if($this->pathA[0] != 'login' && $this->pathA[0] != 'logout'){
					$this->session->check();
				}			
			}

			if(!isset($this->pathA[0])){
				Utils::metaRefresh(CMS_ROOT . "home");
			}
			*/
	}
	
	//override the singleton constructor	
	public static function getInstance()
	{
		if(!self::$instance){
			$c = __CLASS__;
			self::$instance = new $c();
		}
		return self::$instance;
	}
	
	public function setTable()
	{
		if(isset($this->pathA[1])){
			$this->table = $this->pathA[1];
			$q_label = $this->db->queryRow("SELECT display_name FROM cms_tables WHERE table_name = '$this->table'");
			if($q_label['display_name'] == ''){
				$this->label = Utils::formatHumanReadable($this->table);
			}else{
				$this->label = $q_label['display_name'];
			}
		}else{
			$this->pathA[1] = '';
			$this->table = '';
		}
	}
	
	private function checkDB()
	{
		// If CMS database tables do not exist, create them using the schema.sql file
		if (!$this->db->query("SHOW TABLES LIKE 'cms_%'")) {
			if ($schema = file_get_contents(CMS_FILESYSTEM.'core/sql/schema.sql')) {
				$schema = explode(';',$schema);
				array_pop($schema);
				foreach ($schema as $row) {
					$this->db->sql($row);
				}
			} else {
				die('Could not load SQL schema and data');
			}
		}
	}
	
	private function setConfig()
	{
		
		$this->config = array();
		
		$tA = array();
		$tA[] = array('table_name'=>'*','column_name'=>'id','edit_module'=>'readonly','edit_mode'=>'edit');
		$tA[] = array('table_name'=>'*','column_name'=>'active','edit_module'=>'boolean','filter'=>'<config><filter>1</filter></config>');
		
		$tA[] = array('table_name'=>'*','column_name'=>'created','edit_module'=>'readonly','edit_mode'=>'edit','process_module'=>'timestamp');
		$tA[] = array('table_name'=>'*','column_name'=>'modified','edit_module'=>'readonly','edit_mode'=>'edit','process_module'=>'timestamp');
		$tA[] = array('table_name'=>'*','column_name'=>'date','edit_module'=>'selectDate');		
		$tA[] = array('table_name'=>'*','column_name'=>'state','edit_module'=>'selectState');
		$tA[] = array('table_name'=>'*','column_name'=>'country','edit_module'=>'selectCountry');
		
		$tA[] = array('table_name'=>'cms_groups','column_name'=>'admin','edit_module'=>'boolean','filter'=>'<config><filter>1</filter></config>');
		$tA[] = array('table_name'=>'cms_groups','column_name'=>'tables','edit_module'=>'plugin','process_module'=>'plugin');
		
		$tA[] = array('table_name'=>'cms_users','column_name'=>'password','display_name'=>'Password Reset','edit_module'=>'plugin','edit_mode'=>'edit','process_module'=>'plugin','process_mode'=>'update');
		$tA[] = array('table_name'=>'cms_users','column_name'=>'password','edit_module'=>'plugin','edit_mode'=>'insert','process_module'=>'plugin','process_mode'=>'insert');
		$tA[] = array('table_name'=>'cms_users','column_name'=>'groups','edit_module'=>'plugin','process_module'=>'plugin');
		$tA[] = array('table_name'=>'cms_users','column_name'=>'super_user','edit_module'=>'hidden');
		
		$tA[] = array('table_name'=>'cms_history','column_name'=>'table_name','filter'=>'<config><filter>1</filter></config>');
		$tA[] = array('table_name'=>'cms_history','column_name'=>'action','filter'=>'<config><filter>1</filter></config>');
		$tA[] = array('table_name'=>'cms_history','column_name'=>'user_id','filter'=>'<config><filter>1</filter></config>');
		
		$tA[] = array('table_name'=>'cms_cols','column_name'=>'edit_channel','edit_module'=>'selectStatic','edit_config'=>'
				<config><data_csv>main,related</data_csv></config>');
		$tA[] = array('table_name'=>'cms_cols','column_name'=>'edit_mode','edit_module'=>'selectStatic','edit_config'=>'
				<config><data_csv>edit,insert</data_csv></config>');
		$tA[] = array('table_name'=>'cms_cols','column_name'=>'process_channel','edit_module'=>'selectStatic','edit_config'=>'
				<config><data_csv>main,related</data_csv></config>');
		$tA[] = array('table_name'=>'cms_cols','column_name'=>'process_mode','edit_module'=>'selectStatic','edit_config'=>'
				<config><data_csv>update,insert</data_csv></config>');		
		$tA[] = array('table_name'=>'cms_cols','column_name'=>'edit_module','edit_module'=>'selectStatic','edit_config'=>'
				<config><data_csv>plugin,boolean,hidden,readonly,checkbox,fileField,selectDefault,selectParent,selectFiles,selectStatic,selectDate,selectDateTime,selectState,selectCountry,text,textarea,listManager</data_csv></config>');	
		$tA[] = array('table_name'=>'cms_cols','column_name'=>'process_module','edit_module'=>'selectStatic','edit_config'=>'
				<config><data_csv>plugin,position,file</data_csv></config>');
				
		/*
		$tA[] = array('table_name'=>'cms_cols','column_name'=>'table_name','edit_module'=>'selectDefault','edit_config'=>'
				<config><select_sql>SHOW TABLES</select_sql><col_value>0</col_value><col_display>0</col_display><allow_null>false</allow_null></config>');
		*/
	
		$q = $this->db->query("SHOW COLUMNS FROM `cms_cols`");		
		$this->config['cms_cols'] = self::normalizeArray($tA,$q);
		
		$tA = array();
		//need to add active to user
		$tA[] = array('table_name'=>'cms_users','cols_default'=>'id,firstname,lastname,email,groups','in_nav'=>1);
		$tA[] = array('table_name'=>'cms_groups','cols_default'=>'id,active,name,admin','in_nav'=>1);
		$tA[] = array('table_name'=>'cms_history','cols_default'=>'*','in_nav'=>1);
		$tA[] = array('table_name'=>'cms_cols','cols_default'=>'id,table_name,column_name,edit_module,edit_mode,process_module,process_mode,validate,filter,help');
		$tA[] = array('table_name'=>'cms_tags','cols_default'=>'id,name','in_nav'=>1);
		
		$q = $this->db->query("SHOW COLUMNS FROM `cms_tables`");
		$this->config['cms_tables'] = self::normalizeArray($tA,$q);
		
	}
	
	private function normalizeArray($a1,$a2){
		foreach($a1 as $key=>$value){
			foreach($a2 as $field){
				if(!isset($a1[$key][$field['Field']])){					
					$a1[$key][$field['Field']] = '';					
				}				
			}				
		}
		return $a1;
	}
		
	
	
	
}