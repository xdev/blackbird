<?php

/* $Id$ */

class Remote
{
	private $cms;

	function __construct($cms){
		
		$this->cms = $cms;
		$this->db = $cms->db;
		
		require INCLUDES . 'ProcessRecord.class.php';
		
		$this->name_space = $_POST['name_space'];
				
		$_name_space = '';
		if(strlen($this->name_space) > 1){
			$_name_space = $this->name_space . "_";
		}
		
		if(isset($_REQUEST['die'])){
			
			foreach($_POST as $key=>$value){
				print $key . ' = ' . $value . '<br />';
			}
			
			die();
		}
		
		$pr = new ProcessRecord($this->cms,$this->name_space);
		
		
		$this->mode = Utils::setVar('query_action');
		$table = Utils::setVar('table');
						
		if($this->mode == "update"){
			$id = Utils::setVar('id');
		}
		if($this->mode == "insert"){
			$id = $this->db->getInsertId($table) - 1;
		}
		
		if(isset($GLOBALS['errors'])){
		
			print'
			<script type="text/javascript">
				parent.CMS.broadcaster.broadcastMessage("onRemoteErrors",
					{ 
						
						name_space	: \'' . $this->name_space . '\',
						mode		: \'' . $this->mode . '\',
						id			: ' . $id . ',
						errors		: [';
												
						for($i=0;$i<count($GLOBALS['errors']);$i++){
							$error = $GLOBALS['errors'][$i];
							print "['$error[field]','$error[error]']";
							if($i < count($GLOBALS['errors']) - 1){
								print ',';
							}
						}
												
			print ']		
					}
				);		
			</script>';
			
			die();
		
		}
		//are we a submission from the main record
		//if so loop or send back to datagrid
		
		$url = '';
		
		if(isset($_POST['loop_back'])){
			if($_POST['loop_back'] == 'loop'){
				$url = CMS_ROOT . 'edit/' . $table . '/' . $id . '/looped';
			}
			if($_POST['loop_back'] == ''){
				if(isset($_POST['cms_page_state'])){
					$url = $_POST['cms_page_state'];// . '/saved_' . $id;
					
				}else{
					if(isset($table)){
						$url = CMS_ROOT . 'browse/' . $table;// . '/saved_' . $id;
					}else{
						$url = CMS_ROOT . 'home';
					}
				}
			}
						
			print'
			<script type="text/javascript">
				parent.CMS.loadUrl(\'' . $url . '\');
			</script>';
			
			die();
		
		}
		
		
		
		
		
		
		//if we are a related record just tidy up
				
		print'
		<script type="text/javascript">
			parent.CMS.broadcaster.broadcastMessage("onRemoteComplete",
				{ 
					name_space	: \'' . $this->name_space . '\',
					mode		: \'' . $this->mode . '\',
					id			: ' . $id . '
				}
			);		
		</script>';
		
		
				
	}

}

?>