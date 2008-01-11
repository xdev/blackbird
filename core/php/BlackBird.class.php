<?php

/* $Id$ */

class BlackBird
{
	
	private $_data;
	public $pathA;
	public $session;
	public $db;	
	public $js_includes;

	function __construct()
	{
		
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
				
			$this->session = new SessionManager($this);
		} else {
			die('Bobolink PHP library is not properly installed');
		}
		
		// If CMS database tables do not exist, create them using the schema.sql file
		if (!$this->db->query("SHOW TABLES LIKE 'cms_%'")) {
			if ($schema = file_get_contents(CMS_FILESYSTEM.'core/sql/schema.sql')) {
				$schema = explode(';',$schema);
				array_pop($schema);
				foreach ($schema as $row) {
					$this->db->sql($row);
				}
			} else {
				die('could not load SQL schema and data');
			}
		}
		
		$this->pathA = explode("/",substr($_SERVER["REQUEST_URI"],1));
		$tA = explode("/",substr($_SERVER['PHP_SELF'],1,-(strlen('index.php') + 1)));
		
		//if we are running from a folder, or series of folders splice away the unused bits		
		if($tA[0] != ''){
			array_splice($this->pathA,0,count($tA));
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
		
	}
	
	public function __set($name,$value)
	{
		$this->_data[$name] = $value;
	}
	
	public function __get($name)
	{
		if (isset($this->_data[$name])){
			return $this->_data[$name];
		}else{
			return false;
		}
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
	
	public function buildPage(){
		
		
		
		switch($this->pathA[0]){
		
			case "ajax":
				require_once(INCLUDES.'Ajax.class.php');
				new Ajax($this);			
			break;
			
			case "edit":
				$this->setTable();
				$this->id = $this->pathA[2];
				require_once(INCLUDES.'EditPage.class.php');
				new EditPage($this);
			break;
			
			case "add":
				$this->setTable();
				require_once(INCLUDES.'EditPage.class.php');
				new EditPage($this);
			break;
			
			case "browse":
				$this->setTable();
				require_once(INCLUDES.'DataGrid.class.php');
				new DataGrid($this);			
			break;
			
			case "process":
				$this->setTable();
				switch(true){
				
					case($this->pathA[1] == "batch"):
						$this->table = $this->pathA[2];
						require_once(INCLUDES.'Batch.class.php');
						new Batch($this);
					break;
					
					case($this->pathA[1] == "remote"):
						require_once(INCLUDES.'Remote.class.php');
						new Remote($this);
					break;
					
					default:
						require_once(INCLUDES.'ProcessPage.class.php');
						new ProcessPage($this);
					break;
					
				}
			break;			
			
			case "home":
				require_once(INCLUDES.'Home.class.php');
				new Home($this);				
			break;
			
			case "user":
				require_once(INCLUDES.'User.class.php');
				new User($this);				
			break;
			
			case "logout":
				require_once(INCLUDES.'Logout.class.php');
				new Logout($this);
			break;
			
			case "login":
				require_once(INCLUDES.'Login.class.php');
				new Login($this);
			break;
				
			default:
				//this catches exceptions when using httpd.conf alias
				//not used when using .htaccess
				Utils::metaRefresh(CMS_ROOT . "home");
			break;
		}
				
	}
	
	public function processDelete($table,$id_set)
	{	
	
		switch($table){
	
			default:
				
				foreach($id_set as $id){
					$this->db->sql("DELETE FROM `$table` WHERE id = $id");
							
					$row_data = array();
					$row_data[] = array('field'=>'table_name','value'=>$table);
					$row_data[] = array('field'=>'record_id','value'=>$id);
					$row_data[] = array('field'=>'action','value'=>'delete');
					$row_data[] = array('field'=>'user_id','value'=>$this->session->u_id);
					$row_data[] = array('field'=>'session_id','value'=>session_id());
					$this->db->insert('cms_history',$row_data);
				}				
								
			break;
		
		}
	
	}
	
	public function displayDeleteWarning($table,$id_set)
	{
		switch($table){
	
			default:
			
			break;
			
		}
	
	}
	
	public function injectData($a,$table)
	{
		return $a;
	}
	
	public function formatCol($col_name,$col_value,$table)
	{
	
		$boolSet = array("active","admin");
		
		if(in_array($col_name,$boolSet)){
			if($col_value == 0){ return "false";}
			if($col_value == 1){ return "true";}
		}
		
		if($col_name == 'groups'){
			
			//split list
			$tA = explode(',',$col_value);
			$r = array();
			foreach($tA as $item){
				$q = $this->db->queryRow("SELECT name FROM cms_groups WHERE id = '$item'");
				$r[] = $q['name'];
			}
			
			return join(', ',$r);
			
			
		}	
		
		if($col_name == 'user_id' && $table == 'cms_history'){
		
			$q = $this->db->queryRow("SELECT email FROM " . CMS_USERS_TABLE . " WHERE id = '$col_value'");
			return $q['email'];
		
		}
		
		if(strlen($col_value) > 100){
			$data = substr($col_value,0,100) . "...";
			return strip_tags($data);
		}
		
		return $col_value;
	
	
	}	
	
	public function pluginColumnEdit($name,$value,$options)
	{
		
		if($options['col_name'] == 'password' && $options['table'] == CMS_USERS_TABLE){
			$options['type'] = 'password';
			Forms::text($name,'',$options);		
		}
		
		if($options['col_name'] == 'user_id' && $options['table'] == 'cms_history'){
			$q = $this->db->queryRow("SELECT email FROM " . CMS_USERS_TABLE . " WHERE id = '$value'");
			Forms::readonly($name,$q['email'],$options);		
		}
		
		if($options['col_name'] == 'groups' && $options['table'] == CMS_USERS_TABLE){
			
			$q = $this->db->query("SELECT id,name FROM cms_groups ORDER BY name");
			$r = '<ul>';
			$tA = explode(',',$value);
			
			foreach($q as $group){
				(in_array($group['id'] ,$tA) ) ? $v = 'Y' : $v = '';
				$r .= '<li>' . Forms::checkboxBasic('group_' . $group['id'],$v,array('class'=>'checkbox noparse','label'=>$group['name'])) . '</li>';
			}
			
			$r .= '</ul>';
			$options['label'] = "Groups";
			Forms::buildElement($name,$r,$options);
			Forms::hidden($name,'',array('omit_id'=>true));
		
		}
		
		if($options['col_name'] == 'tables' && $options['table'] == 'cms_groups'){
			
			$q = $this->db->query("SHOW TABLE STATUS");
			$tA = explode(',',$value);
			$privA = array('browse','insert','update','delete');
			
			
			$xml = simplexml_load_string($value);
			$tableA = array();
			if($xml){
				foreach($xml->table as $mytable){
					$t = sprintf($mytable['name']);
					$tableA[$t] = sprintf($mytable);
				}
			}
			
			$r = '<table>
			<tr><th>Table</th>';
			
				foreach($privA as $priv){
					
					$r .= '<th>' . $priv . '</th>';
					
				}
			$r .= '</tr>';
						
			foreach($q as $table){
			
			
				if($table['Comment'] != 'private'){
				
					$r .= '<tr>';
					$r .= '<td>' .  Utils::formatHumanReadable($table['Name']) . '</td>';
					
					$tP = array();
					if(isset($tableA[$table['Name']])){
						$tP = explode(',',$tableA[$table['Name']]);
					}
					
					foreach($privA as $priv){
						
						(in_array($priv ,$tP) ) ? $v = 'Y' : $v = '';
						$r .= '<td>' . Forms::checkboxBasic('table_' . $table['Name'] . '_' . $priv,$v, array('class'=>'checkbox noparse','label'=>'')) . '</td>';
					
					}
					
					
					$r .= '</tr>';
				}
			
			}
			
			$r .= '</table>';
			
			$options['label'] = "Tables";
			Forms::buildElement($name,$r,$options);
			Forms::hidden($name,'',array('omit_id'=>true));
						
		}
		
	}
	
	//need to add namespaces in these functions - ie use name instead of temp strings
	
	public function pluginColumnProcess($name,$value,$options)
	{
	
		if($options['col_name'] == 'tables' && $options['table'] == 'cms_groups'){
			
			$q = $this->db->query("SHOW TABLE STATUS");
			$r = '<data>';
			
			$privA = array('browse','insert','update','delete');
			foreach($q as $table){
			
				if($table['Comment'] != 'private'){
					//
					$p = array();
					foreach($privA as $priv){
						if(isset($_REQUEST['table_' . $table['Name']. '_' . $priv])){
							if($_REQUEST['table_' . $table['Name']. '_' . $priv] == 'Y'){
								$p[] = $priv;
							}
						}
					}
					
					if(count($p)>0){
						$p = join(',',$p);
						$r .= '<table name="' . $table['Name'] . '">' . $p . '</table>';
					}
					
				}
			}
			
			$r .= '</data>';
			
			return array('field'=>'tables','value'=>$r);
		
		}
		
		if($options['col_name'] == 'groups' && $options['table'] == CMS_USERS_TABLE){
			
			$q = $this->db->query("SELECT * FROM cms_groups");
			foreach($q as $group){
				if(isset($_REQUEST['group_' . $group['id']])){
					if($_REQUEST['group_' . $group['id']] == 'Y'){
						$r[] = $group['id'];
					}
				}
			}
		
			
			//trim last character;
			$r = join(',',$r);
			return array('field'=>'groups','value'=>$r);			
		
		}
		
		if($options['col_name'] == 'password' && $options['table'] == CMS_USERS_TABLE){
			
			if(strlen($value) > 1){
				return array('field'=>'password','value'=>sha1($value));			
			}else{
				return false;
			}
		}
	
	}
	
	public function pluginTableProcess($table,$id,$mode)
	{
		
	}
		
	public function buildHeader($js="",$css="",$body_class="",$help="")
	{

print '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
    "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd"> 
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<!-- 
Copyright 2004-2007 
Authors Charles Mastin & Joshua Rudd
c @ charlesmastin.com
contact @ joshuarudd.com


Portions of this software rely upon the following software which are covered by their respective license agreements
* Prototype.js
* Scriptaculous Library
* Lightbox
* Magpie rss reader
-->
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<meta http-equiv="cache-control" content="no-cache" />
	<title>' . CMS_CLIENT . ' CMS</title>
	<!-- Main CSS -->
	<link rel="stylesheet" type="text/css" media="screen" href="' . CMS_ROOT . ASSETS . 'css/style.css" />
	<!-- Core Javascript -->
	<script type="text/javascript" src="' . CMS_ROOT . ASSETS . 'js/prototype.js" ></script>
	<script type="text/javascript" src="' . CMS_ROOT . ASSETS . 'js/scriptaculous/scriptaculous.js?load=effects,dragdrop" ></script>
	<script type="text/javascript" src="' . CMS_ROOT . ASSETS . 'js/functions.js" ></script>
	<script type="text/javascript" src="' . CMS_ROOT . ASSETS . 'js/eventbroadcaster.js" ></script>
	<script type="text/javascript" src="' . CMS_ROOT . ASSETS . 'js/cms.js" ></script>
	<script type="text/javascript" src="' . CMS_ROOT . ASSETS . 'js/validator.js" ></script>
	<script type="text/javascript">
		<!-- <![CDATA[
		CMS.setProperty("cms_root","' . CMS_ROOT . '");
		// ]]> -->
	</script>
	<!-- IE conditionals -->
	<!--[if lt IE 7]>
		<script src="' . CMS_ROOT . ASSETS . 'js/ie6.js" type="text/javascript" language="javascript" charset="utf-8"></script>
		<link rel="stylesheet" href="' . CMS_ROOT . ASSETS . 'css/ie6.css" type="text/css" media="screen" charset="utf-8" />
	<![endif]-->
	<!--[if IE 7]>
		<link rel="stylesheet" href="' . CMS_ROOT . ASSETS . 'css/ie7.css" type="text/css" media="screen" charset="utf-8" />
	<![endif]-->';
	if(file_exists(CUSTOM.'css/custom.css')){
		print '
		<!-- Custom CSS -->
		<link rel="stylesheet" type="text/css" media="screen" href="' . CMS_ROOT . CUSTOM . 'css/custom.css" />' . "\r";
	}
		
	if($js != ''){
		print '<!-- Custom Javascript -->' . "\r";
		print $js;
	}
	if($css != ''){
		print '<!-- Custom CSS -->';
		print $css;
	}
	
	// Check for debug mode
	if(isset($_GET['debug'])) $body_class = ' class="debug"';
	
	print '</head>
<body id="body"'.$body_class.'>
	<div id="page">
		<div id="header">
			<div id="masthead" onclick="window.location = \'' . CMS_ROOT . 'home\'" >
				<h1>'.CMS_CLIENT.' CMS</h1>';
	
	if($this->session->logged===true){
		
		print '
		</div>
		<div id="navigation">
		<p id="logged_info"><a href="' . CMS_ROOT . 'user">' . $this->session->displayname . '</a> - <a href="' . CMS_ROOT . 'logout">Logout</a></p>';		
				
	
		if($tables = $this->session->getNavigation()){
			print '<ul id="nav">';
			
			foreach($tables as $row){
			
				print '
				<li>
				<a href="#">' . $row['name'] . '</a>
				<ul>';
				
				foreach($row['tables'] as $item){
					
					$q_label = $this->db->queryRow("SELECT display_name,menu_id FROM cms_tables WHERE table_name = '$item'");
					if($q_label['display_name'] == ''){
						$label = Utils::formatHumanReadable($item);
					}else{
						$label = $q_label['display_name'];
					}
					
					print '<li><a href="' . CMS_ROOT . 'browse/' . $item . '">' . $label . '</a></li>'; 
				}
			
			
			print '</ul>
			</li>';
			
			}
			
			print '</ul>';
		
		}
		
		
		print '</div>';
	}else{
		print '</div>';		
	}
	
	print "<h1 id=\"page_label\">$this->label</h1>";
	if(strlen($help) > 1){
		print '<a id="toggle_help" class="icon help" href="#help" onclick="return false" title="Show/hide help">Help</a>';
	}
	if(strlen($help) > 1){
		print '
		<div id="help" style="display:none;">
			<div id="help_content">
			' . $help . '
			</div>
		</div>';
	}
	print '<div class="clearfix"></div></div>';
	
	
		
	
		
	}
	
	public function sendEmail($message)
	{
		
		// Need to add ability to send / resend welcome message
		
		require_once(LIB . 'email/class.phpmailer.php');
		$mail = new PHPMailer();
		
		$emailMax = 1;
		$row = $message;
		
		if(isset($message[0])){
			if(is_array($message[0])){
				$emailMax = count($message);
			}
		}
		
		for($i=0;$i<$emailMax;$i++){
			if($emailMax>1){
				$row = $message[$i];
			}
			//switch this up based upon config variables, array variables (overrides) or use defaults	
			$mail->IsMail();
			$mail->Host     = "localhost";
			$mail->From     = "cms_daemon@localhost";
		
			//this is always based upon the $message array value
			$mail->AddAddress($row['to_address']);
			$mail->IsHTML(true);
			$mail->Subject  = $row['subject'];
			$mail->Body     = $row['body'];
			$mail->AltBody  = strip_tags($row['body']);
		
			if(!$mail->Send())
			{
			   print "Message could not be sent. <p>";
			   print "Mailer Error: " . $mail->ErrorInfo;
			   exit;
			}else{
				print '';
			}
			$mail->ClearAddresses();
		
		}
				
	}
	
	/*
	* sort_position
	*
	* @param   string   table name
	* @param   string   sql record set query
	* @param   string   record id
	* @param   string   new position
	*
	* @return  null     
	*
	*/
	
	public function sortPosition($table,$sql,$id,$pos){
		
		$q = $this->db->query($sql);
		
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
			$this->db->update($table,$sqlA,'id',$tA[$i]);
		}
	
	}

	
	
	public function buildFooter()
	{
		print'
		</div>
		<div id="footer">BlackBird &copy; 2004-' . date('Y') . ' ' . BLACKBIRD_VERSION . ' (<a href="http://svn.underdeconstruction.com/listing.php?repname=blackbird&rev=' . BLACKBIRD_REVISION . '">' . BLACKBIRD_REVISION . ')</a></div>
		</div>
		</body>
		</html>';

	}
	

	
}

?>