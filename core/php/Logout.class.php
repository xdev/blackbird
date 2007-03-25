<?php

class Logout
{

	private $cms;

	function __construct($cms)
	{
		$this->cms = $cms;
		$this->db = $cms->db;
		
		session_name("s_id");
		session_start();
		
		//$q = $this->db->queryRow("SELECT * FROM cms_sessions WHERE session_id = '$_COOKIE[s_id]'");
		$row_data = Array();
		$row_data[] = array('field'=>'end_time','value'=>Utils::now());
		$this->db->update('cms_sessions',$row_data,'session_id',session_id());
		//$this->db->insert('cms_sessions',$row_data);
		
		
		$_SESSION = array();
				
		if (isset($_COOKIE["s_id"])) {
			setcookie("s_id", '', time()-42000, '/');
		}
		
		session_destroy();
		
		$this->cms->session->logged = false;
		
		Utils::metaRefresh(CMS_ROOT . "login");
		
		//$this->buildPage();
	}
	
	function buildPage()
	{

		$pagetitle = "Logged Out";
		
		
		$this->cms->buildHeader();
				
		print '
		<div id="content">
		<p>You have been successfully logged out.</p>
		<p><a href="' . CMS_ROOT . 'login">Login</a></p>
		</div>';

		$this->cms->buildFooter();
	}

}