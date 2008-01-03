<?php

/* $Id$ */

class Login
{
		
	function __construct($cms){
		// Make sure DB settings are set
		if (!(
			isset($GLOBALS['DATABASE']['host']) &&
			isset($GLOBALS['DATABASE']['user']) &&
			isset($GLOBALS['DATABASE']['pass']) &&
			isset($GLOBALS['DATABASE']['db'])
		)) die('Database settings not properly configured in config_custom.php file');
		
		$this->cms = $cms;
		$this->db = $this->cms->db;
								
		if($this->cms->pathA[1] == "reset"){
			//do the password reset bit
		}
		
		if(!empty($_POST['email']) && isset($_POST['reset_password'])){
			
			//create random hash
			$string = md5(time());
			$h_s = 32;
			$pass = substr($string,rand(0,$h_s),16);
			
			//check if you exists and update DB
			$q = $this->db->queryRow("SELECT id,email FROM " . CMS_USERS_TABLE . " WHERE email = '$_POST[email]'");
			if($q['email'] == $_POST['email']){
				$row_data = array(array('field'=>'password', 'value'=>sha1($pass) ));
				
				$this->db->update(CMS_USERS_TABLE,$row_data,"id",$q['id']);
				
				$html_template =  "Your password has been reset!";
				$html_template .= "<p>$pass</p>";
				$html_template .= "<p>To log in <a href=\"http://$_SERVER[HTTP_HOST]" . CMS_ROOT . "login\">Click Here</a></p>";
				
				$message = array();
				$message['to_address'] = $_POST['email'];
				$message['subject'] = 'CMS Password Reset';
				$message['body'] = $html_template;

				$this->cms->sendEmail($message);
				 
	
				
				
				Utils::metaRefresh(CMS_ROOT . 'login/confirm/' . $_POST['email']);
				
			}else{
				Utils::metaRefresh(CMS_ROOT . 'login/?e=2');
			}			
			
			
			die();
		
		}
		
		if(!empty($_POST['email']) && !empty($_POST['password'])){
	
			$pass = sha1($_POST['password']);
			$email = $_POST['email'];
						
			if($this->cms->session->login($pass,$email)){
								
				if(isset($_REQUEST['redirect'])){
					Utils::metaRefresh($_REQUEST['redirect']);
				}else{
					Utils::metaRefresh(CMS_ROOT . "home");
				}
				
			}else{
				Utils::metaRefresh(CMS_ROOT . "login/?e=1");
				die();
			}
	
		}else{
			$this->buildPage();	
			
		}
		
	}

	function buildPage()
	{	
			
		$this->cms->label = "";
		$body_id = "login";
				
		$this->cms->buildHeader('','',' class="login"');
		
		print "<form name=\"user_form\" id=\"user_form\" action=\"" . CMS_ROOT . "login/\" method=\"post\">
		<div id=\"content\">";
		
		$e = Utils::setVar("e");
				
		switch($e){
		
			case "1": print "<div class=\"message error\">Invalid email or password, please try again or <a href=\"" . CMS_ROOT . "login/reset\">Reset Password</a>.</div>"; break;
			case "2": print '<div class="message error">No user with this email exists! Please try again or have an admin create an account for you.</div>'; break;
			case "3": print "<div class=\"message error\">Break in</div>"; break;
		
		}
		
		if($this->cms->pathA[1] == "reset"){
		
			print '<div class="message ok"><p>Enter your email and a new password will be created and sent to you. After logging back in, you can change your password by editing your profile page.</p></div>';
			
		}
		
		if($this->cms->pathA[1] == "confirm"){
			$email = $this->cms->pathA[2];
			print '<div class="message ok"><p>A new password has been generated and sent to ' . $email . '</p></div>';
			
		}
				
		print '<div id="login">';
		
		print '<h1>' . CMS_CLIENT . ' CMS</h1>';
		
		Forms::text("email",'',array('label'=>'Email'));
		
		print '<div style="clear:both;"</div>';
		
		if($this->cms->pathA[1] == "reset"){
			Forms::hidden("reset_password","yes");
			print '
			<div class="buttons">
			<a class="button reset_password" href="#" onclick="validate(\'user_form\');" >Reset</a>
			<a class="button cancel" href="#" onclick="window.location = CMS.getProperty(\'cms_root\') + \'login\'" >Cancel</a>
			</div>';
			
		}else{
			if(isset($_REQUEST['redirect'])){
				Forms::hidden("redirect",$_REQUEST['redirect']);
			}
			Forms::text("password","",array('type'=>'password','label'=>'Password'));
			print '
			<div class="buttons">
			<a class="button login" href="#" onclick="validate(\'user_form\');" >Login</a>
			</div>';
			
		}
		print '<div style="clear:both;"></div>';
		print "</form>";
		print '</div>';
		
		
		print '<script type="text/javascript">
			Event.observe("password","keypress",function(event){
				if(event.keyCode == Event.KEY_RETURN){
					$("user_form").submit();
				}
			});
		</script>';
		
		$this->cms->buildFooter();
	
	}

}
?>