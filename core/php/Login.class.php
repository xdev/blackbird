<?php

class Login
{
	
	private $cms;
	
	function __construct($cms){
		// Make sure DB settings are set
		if (!(
			isset($GLOBALS['DATABASE']['host']) &&
			isset($GLOBALS['DATABASE']['user']) &&
			isset($GLOBALS['DATABASE']['pass']) &&
			isset($GLOBALS['DATABASE']['db'])
		)) die('Database settings not properly configured in config_custom.php file');
		
		$this->cms = $cms;
		$this->db = $cms->db;
								
		if($this->cms->pathA[1] == "reset"){
			//do the password reset bit
		}
		
		if(!empty($_POST['email']) && isset($_POST['reset_password'])){
			
			//create random hash
			$string = md5(time());
			$h_s = 32;
			$pass = substr($string,rand(0,$h_s),16);
			
			//check if you exists and update DB
			$q = $this->db->queryRow("SELECT id,email FROM cms_users WHERE email = '$_POST[email]'");
			if($q['email'] == $_POST['email']){
				$row_data = array(array('field'=>'password', 'value'=>sha1($pass) ));
				
				$this->db->update("cms_users",$row_data,"id",$q['id']);
				
				//email the new password back to the user
				//print $pass;
				
				require(LIB . 'email/class.phpmailer.php');


				$html_template =  "Your password has been reset!";
				$html_template .= "<p>$pass</p>";
				$html_template .= "<p>To log in <a href=\"http://$_SERVER[HTTP_HOST]" . CMS_ROOT . "login\">Click Here</a></p>";
				
				 
				$text_template = strip_tags($html_template);
				
				$mail = new PHPMailer();
					
				$mail->IsSMTP();
				$mail->Host     = "localhost";
				
				$mail->From     = "cms_daemon@localhost";
				$mail->AddAddress($_POST['email']);
				
				$mail->IsHTML(true);
				$mail->Subject  = "CMS Password Reset";
				$mail->Body    = $html_template;
				$mail->AltBody = $text_template;
				
				if(!$mail->Send())
				{
				   print "Message could not be sent. <p>";
				   print "Mailer Error: " . $mail->ErrorInfo;
				   exit;
				}
				$mail->ClearAddresses();
				
				
				Utils::metaRefresh(CMS_ROOT . 'login/confirm/' . $_POST['email']);
				
			}else{
				Utils::metaRefresh(CMS_ROOT . 'login/?e=2');
			}			
			
			
			die();
		
		}
		
		if(!empty($_POST['email']) && !empty($_POST['password'])){
	
			$pass = sha1($_POST['password']);
			$email = $_POST['email'];
			
			$q = $this->db->queryRow("SELECT id FROM `cms_users` WHERE email = '$email' AND password = '$pass'");
			
			if(isset($q['id'])){
				$this->cms->session->login($q['id'],$pass,$email);
				
				$row_data = array();
				$row_data[] = array('field'=>'user_id','value'=>$q['id']);
				$row_data[] = array('field'=>'start_time','value'=>Utils::now());
				$row_data[] = array('field'=>'session_id','value'=>session_id());
				$this->db->insert('cms_sessions',$row_data);
				
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
				
		$this->cms->buildHeader();
				
		print '<style type="text/css">
				
				#page
				{
					background: none;
					border: none;
				}
				#masthead
				{
					visibility: hidden;
				}
				#footer
				{
					width: 300px;
					margin-left: auto;
					margin-right: auto;
				}
				.buttons
				{
					padding-bottom: 30px;
				}
				</style>';
		
		print "<form name=\"user_form\" id=\"user_form\" action=\"" . CMS_ROOT . "login/\" method=\"post\">
		<div id=\"content\">";
		
		$e = Utils::setVar("e");
				
		switch($e){
		
			case "1": print "<div class=\"error\">Invalid email or password, please try again.<br /><a class=\"error\" href=\"" . CMS_ROOT . "login/reset\">Reset Password</a></div>"; break;
			case "2": print '<div class="error">No user with this email exists! Please try again or have an admin create an account for you.</div>'; break;
			case "3": print "<div class=\"error\">Break in</div>"; break;
		
		}
				
		print '<div id="login">';
		
		print '<div style="font-size: 16px; margin-bottom:10px;">' . CMS_CLIENT . ' CMS</div>';
		
		Forms::text("email",'',array('label'=>'Email'));
		
		if($this->cms->pathA[1] == "reset"){
			Forms::hidden("reset_password","yes");
			print '<div class="buttons">';
			print '<a class="button reset_password" href="#" onclick="validate(\'user_form\');" >Reset</a>';
			//Forms::button("submit","Reset");
			print '</div>';
			
		}else{
			if(isset($_REQUEST['redirect'])){
				Forms::hidden("redirect",$_REQUEST['redirect']);
			}
			Forms::text("password","",array('type'=>'password','label'=>'Password'));
			print '<div class="buttons">';
			print '<a class="button login" href="#" onclick="validate(\'user_form\');" >Login</a>';
			//Forms::button("submit","Login");
			print '</div>';
			
		}
		
		
		print '</div>';
		
		print "</div></form>";
		
		
		$this->cms->buildFooter();
	
	}

}
?>