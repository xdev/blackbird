<?php

class User
{
	
	private $cms;
	
	function __construct($cms)
	{
		$this->cms = $cms;
		$this->db = $cms->db;
		
		if(isset($_POST['action'])){
			$this->processPage();
		}else{
			$this->buildPage();
		}
	}
	
	function processPage()
	{
		$this->cms->buildHeader();
		$this->cms->buildFooter();
		
		$row_data = array();
		$row_data[] = array('field'=>'firstname','value'=>$_REQUEST['main_firstname']);
		$row_data[] = array('field'=>'lastname','value'=>$_REQUEST['main_lastname']);
		$row_data[] = array('field'=>'email','value'=>$_REQUEST['main_email']);
		
		if(!empty($_REQUEST['main_password'])){
			$row_data[] = array('field'=>'password','value'=>sha1($_REQUEST['main_password']));
		}
		
		$this->db->update('cms_users',$row_data,'id',$this->cms->session->u_id);
		
		Utils::metaRefresh(CMS_ROOT . 'user/looped');
	}
	
	
	function buildPage()
	{
		$this->cms->label = "MyAccount";
		$this->cms->buildHeader('','',' class="edit"');
		
		
		print '<div id="content" class="clearfix">';
				
		if(isset($this->cms->pathA[1])){
			if ($this->cms->pathA[1] == 'looped'){
				print '<div id="message_content">Your personal information was successfully updated! <a href="#" onclick="CMS.closeMessage();">Close</a></div>';
			}
		}
		
		
		print'
		<ul id="edit_nav">
			<li id="tab_main" class="trigger active" ><a href="#" onclick="CMS.showTab(\'main\'); return false;">Details</a></li>
		</ul>
		
		<div class="clearfix"></div>
		
		<div class="panes">
			<div id="pane_main" class="toggle main_cms_users">
				<div class="pane">';

		
		
		
		
		$u_id = $this->cms->session->u_id;
		
		$q = $this->db->queryRow("SELECT * FROM cms_users WHERE id = '$u_id'");
		
		print '<form name="form_main" id="form_main" action="' . CMS_ROOT . 'user/" method="post">';
		
		Forms::hidden("action","booya");
		Forms::text("main_firstname",$q['firstname'],array('label'=>'First Name','validate'=>'default'));
		Forms::text("main_lastname",$q['lastname'],array('label'=>'Last Name','validate'=>'default'));
		Forms::text("main_email",$q['email'],array('label'=>'Email','validate'=>'email'));
		Forms::text("main_password",'',array('label'=>'Reset Password','type'=>'password'));
		
		print '</form>';
		
		print '</div>
			</div>
		</div>';
		
		print '<div id="edit_buttons" class="clearfix">';
		print '<a class="button save" href="#" onclick="CMS.submitMain(\'main\'); return false;" >Save</a>
		<a class="button cancel" href="#" onclick="history.back();" >Cancel</a>';
		print '</div>';
		
		$this->cms->buildFooter();
	
	}




}


?>