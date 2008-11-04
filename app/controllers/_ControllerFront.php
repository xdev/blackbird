<?php

class _ControllerFront extends ControllerFront
{
	public static $config;
	public static $session;
	
	//override the singleton constructor	
	public static function getInstance()
	{
		if(!self::$instance){
			$c = __CLASS__;
			self::$instance = new $c();
		}
		return self::$instance;
	}
	
	private function __construct()
	{
		parent::setUri();		
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
		*/
		
		// Check to see if we have a sufficient schema installed
		if(AdaptorMysql::query("SHOW TABLES LIKE '" . BLACKBIRD_TABLE_PREFIX . "info'")){
			if($q = AdaptorMysql::queryRow("SELECT * FROM " . BLACKBIRD_TABLE_PREFIX . "info WHERE name = 'schema_version'")){
				if($q['value'] < REQUIRED_SCHEMA_VERSION){
					die('You have an outdated SQL schema ['.$q['value'] .'], please run the update script for [' . REQUIRED_SCHEMA_VERSION . ']');
				}
			}			
		}else{
			die('You have an outdated SQL schema, please run the update script for [' . REQUIRED_SCHEMA_VERSION . ']');
		}
						
		//custom session
		/*
		if(defined(BLACKBIRD_TABLE_PREFIX . 'SESSION_MANAGER')){
			require_once(CMS_SESSION_MANAGER);
		}else{
			require_once(INCLUDES.'SessionManager.class.php');
		}
		*/
		self::setConfig();
		
		//create session
		require_once MODELS . 'UserModel.php';
		self::$session = new UserModel();		
		//if we're not in the user controller trying to call login logout processlogin
		if(self::$requestA[0] == 'user' && 
			(
			self::$requestA[1] == 'login' || 
			self::$requestA[1] == 'logout' || 
			self::$requestA[1] == 'loggedout' || 
			self::$requestA[1] == 'processlogin' ||
			self::$requestA[1] == 'processlogout' ||
			self::$requestA[1] == 'reset' ||
			self::$requestA[1] == 'processreset'
			)){
		}else{
			self::$session->checkSession();
		}
		
		//broken for the moment - auto install sql if necessary
		//self::checkDB();
		
				
		
	}
	
	public static function getSession()
	{
		return self::$session;
	}
	
	public static function sendEmail($message)
	{
		// To send HTML mail, the Content-type header must be set
		$headers  = 'MIME-Version: 1.0' . "\r\n";
		$headers .= 'Content-type: text/html; charset=utf-8' . "\r\n";
		
		// Additional headers
		$headers .= "To: $message[to_name] <$message[to_address]>\r\n";
		$headers .= "From: Blackbird CMS <blackbird@" . $_SERVER['SERVER_NAME'] . ">\r\n";
		
		//bool mail ( string $to , string $subject , string $message [, string $additional_headers [, string $additional_parameters ]] )
		if(mail($message['to_address'],$message['subject'],$message['body'],$headers)){
			return true;
		}
	}
	
	
	public static function parseConfig($xml)
	{
		require_once LIB.'bobolink/xml/XmlToArray.class.php';
		$p = new XmlToArray($xml);
		$out = $p->getOutput();
		//first child, not necessarily the config node?
		return $out['config'];
	}
	
	public static function injectData($a,$table,$mode)
	{
		$r = null;
		$file = CUSTOM . DS . 'plugins' . DS . 'inject_data.php';
		if(file_exists($file) && include_once$file){
			$r = config__inject_data($a,$table,$mode);			
		}
		
		if($r == null){
			$r = $a;
		}		
		
		return $r;
	}
	
	
	public static function pluginColumnEdit($name,$value,$options)
	{
		$r = null;
		$file = CUSTOM . DS . 'plugins' . DS . 'record_column_edit.php';
		if(file_exists($file) && include_once$file){
			//return true if we wish to abort default behavior for this column, otherwise carry on
			$r = config__record_column_edit($name,$value,$options);
		}
		
		if($r == null){		
			$file = APP . DS . 'plugins' . DS . 'record_column_edit.php';
			include_once $file;
			plugin__record_column_edit($name,$value,$options);
		}
				
	}
	
	public static function pluginColumnProcess($name,$value,$options)
	{
		$r = null;
		$file = CUSTOM . DS . 'plugins' . DS . 'record_column_process.php';
		if(file_exists($file) && include_once $file){
			$r = config__record_column_process($name,$value,$options);
		}
		
		if($r == null){		
			$file = APP . DS . 'plugins' . DS . 'record_column_process.php';
			@include_once $file;
			$r = plugin__record_column_process($name,$value,$options);		
		}
		
		return $r;
	}
	
	public static function formatCol($col_name,$col_value,$table)
	{
		$r = null;
		$file = CUSTOM . DS . 'plugins' . DS . 'format_column.php';
		if(file_exists($file) && include_once $file){
			$r = config__format_column($col_name,$col_value,$table);
		}
		if($r == null){
			$file = APP . DS . 'plugins' . DS . 'format_column.php';
			include_once $file;			
			$r = plugin__format_column($col_name,$col_value,$table);
		}
		return $r;
	}
		
	public static function sortPosition($table,$sql,$id,$pos)
	{
		
		$q = AdaptorMysql::query($sql);
		
		$tA = array();
		for($i=0;$i<count($q);$i++){
			if($id != $q[$i]['id']){
				$tA[] = $q[$i]['id'];
			}
		
		}
			
		array_splice($tA,($pos-1),0,$id);
		
		for($i=0;$i<count($tA);$i++){
			$sqlA = array();
			$sqlA[] = array('field'=>'position','value'=>($i+1));
			AdaptorMysql::update($table,$sqlA,'id',$tA[$i]);
		}
	
	}
	
	public function setTable()
	{
		if(isset($this->pathA[1])){
			$this->table = $this->pathA[1];
			$q_label = $this->db->queryRow("SELECT display_name FROM " . BLACKBIRD_TABLE_PREFIX . "tables WHERE table_name = '$this->table'");
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
		/*
		if (!AdaptorMysql::query("SHOW TABLES LIKE BLACKBIRD_TABLE_PREFIX . '%'")) {
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
		*/
	}
	
	private function setConfig()
	{
		
		self::$config = array();
		
		$tA = array();
		$tA[] = array('table_name'=>'*','column_name'=>'id','edit_module'=>'readonly','edit_mode'=>'edit');
		$tA[] = array('table_name'=>'*','column_name'=>'active','edit_module'=>'boolean','filter'=>'<config><filter>1</filter></config>');
		
		$tA[] = array('table_name'=>'*','column_name'=>'created','edit_module'=>'readonly','edit_mode'=>'edit','process_module'=>'timestamp');
		$tA[] = array('table_name'=>'*','column_name'=>'modified','edit_module'=>'readonly','edit_mode'=>'edit','process_module'=>'timestamp');
		$tA[] = array('table_name'=>'*','column_name'=>'date','edit_module'=>'selectDate');		
		$tA[] = array('table_name'=>'*','column_name'=>'state','edit_module'=>'selectState');
		$tA[] = array('table_name'=>'*','column_name'=>'country','edit_module'=>'selectCountry');
		
		$tA[] = array('table_name'=>BLACKBIRD_TABLE_PREFIX . 'groups','column_name'=>'admin','edit_module'=>'boolean','filter'=>'<config><filter>1</filter></config>');
		$tA[] = array('table_name'=>BLACKBIRD_TABLE_PREFIX . 'groups','column_name'=>'tables','edit_module'=>'plugin','process_module'=>'plugin');
		
		$tA[] = array('table_name'=>BLACKBIRD_TABLE_PREFIX . 'users','column_name'=>'password','display_name'=>'Password Reset','edit_module'=>'plugin','edit_mode'=>'edit','process_module'=>'plugin','process_mode'=>'update');
		$tA[] = array('table_name'=>BLACKBIRD_TABLE_PREFIX . 'users','column_name'=>'password','edit_module'=>'plugin','edit_mode'=>'insert','process_module'=>'plugin','process_mode'=>'insert');
		$tA[] = array('table_name'=>BLACKBIRD_TABLE_PREFIX . 'users','column_name'=>'groups','edit_module'=>'plugin','process_module'=>'plugin');
		$tA[] = array('table_name'=>BLACKBIRD_TABLE_PREFIX . 'users','column_name'=>'super_user','edit_module'=>'hidden');
		
		$tA[] = array('table_name'=>BLACKBIRD_TABLE_PREFIX . 'history','column_name'=>'table_name','filter'=>'<config><filter>1</filter></config>');
		$tA[] = array('table_name'=>BLACKBIRD_TABLE_PREFIX . 'history','column_name'=>'action','filter'=>'<config><filter>1</filter></config>');
		$tA[] = array('table_name'=>BLACKBIRD_TABLE_PREFIX . 'history','column_name'=>'user_id','filter'=>'<config><filter>1</filter></config>');
		
		$tA[] = array('table_name'=>BLACKBIRD_TABLE_PREFIX . 'cols','column_name'=>'edit_channel','edit_module'=>'selectStatic','edit_config'=>'
				<config><data_csv>main,related</data_csv></config>');
		$tA[] = array('table_name'=>BLACKBIRD_TABLE_PREFIX . 'cols','column_name'=>'edit_mode','edit_module'=>'selectStatic','edit_config'=>'
				<config><data_csv>edit,insert</data_csv></config>');
		$tA[] = array('table_name'=>BLACKBIRD_TABLE_PREFIX . 'cols','column_name'=>'process_channel','edit_module'=>'selectStatic','edit_config'=>'
				<config><data_csv>main,related</data_csv></config>');
		$tA[] = array('table_name'=>BLACKBIRD_TABLE_PREFIX . 'cols','column_name'=>'process_mode','edit_module'=>'selectStatic','edit_config'=>'
				<config><data_csv>update,insert</data_csv></config>');		
		$tA[] = array('table_name'=>BLACKBIRD_TABLE_PREFIX . 'cols','column_name'=>'edit_module','edit_module'=>'selectStatic','edit_config'=>'
				<config><data_csv>plugin,boolean,hidden,readonly,checkbox,fileField,selectDefault,selectParent,selectFiles,selectStatic,selectDate,selectDateTime,selectState,selectCountry,text,textarea,listManager</data_csv></config>');	
		$tA[] = array('table_name'=>BLACKBIRD_TABLE_PREFIX . 'cols','column_name'=>'process_module','edit_module'=>'selectStatic','edit_config'=>'
				<config><data_csv>plugin,position,file</data_csv></config>');
				
		/*
		$tA[] = array('table_name'=>BLACKBIRD_TABLE_PREFIX . 'cols','column_name'=>'table_name','edit_module'=>'selectDefault','edit_config'=>'
				<config><select_sql>SHOW TABLES</select_sql><col_value>0</col_value><col_display>0</col_display><allow_null>false</allow_null></config>');
		*/
	
		$q = AdaptorMysql::query("SHOW COLUMNS FROM `" . BLACKBIRD_TABLE_PREFIX . "cols`");		
		self::$config['cols'] = self::normalizeArray($tA,$q);
		
		$tA = array();
		//need to add active to user
		$tA[] = array('table_name'=>BLACKBIRD_TABLE_PREFIX . 'users','cols_default'=>'id,firstname,lastname,email,groups','in_nav'=>1);
		$tA[] = array('table_name'=>BLACKBIRD_TABLE_PREFIX . 'groups','cols_default'=>'id,active,name,admin','in_nav'=>1);
		$tA[] = array('table_name'=>BLACKBIRD_TABLE_PREFIX . 'history','cols_default'=>'*','in_nav'=>1);
		$tA[] = array('table_name'=>BLACKBIRD_TABLE_PREFIX . 'cols','cols_default'=>'id,table_name,column_name,edit_module,edit_mode,process_module,process_mode,validate,filter,help');
		$tA[] = array('table_name'=>BLACKBIRD_TABLE_PREFIX . 'tags','cols_default'=>'id,name','in_nav'=>1);
		
		$q = AdaptorMysql::query("SHOW COLUMNS FROM `" . BLACKBIRD_TABLE_PREFIX . "tables`");
		self::$config['tables'] = self::normalizeArray($tA,$q);
		
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