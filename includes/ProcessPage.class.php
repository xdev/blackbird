<?php

class ProcessPage
{

	private $page_state;		
	private $cms;

	function __construct($cms){
		
		/*
		foreach($_POST as $key=>$value){
			print $key . '=' . $value . '<br>';
		}
		*/
		
		$this->cms = $cms;
		$this->cms->buildHeader();
						
		require LIB . 'cms/ProcessRecord.class.php';
		
		
		(isset($_POST['name_space'])) ? $name_space = $_POST['name_space'] : $name_space = '';
		new ProcessRecord($this->cms,$name_space);
		
		$this->query_action = Utils::setVar('query_action');
		$table = Utils::setVar('table');
		
		if($this->query_action == "update"){
			$id = Utils::setVar('id');
		}
		if($this->query_action == "insert"){
			$id = Db::getInsertId($table) - 1;
		}
		
		if(isset($GLOBALS['errors'])){
			
			print'
			<script type="text/javascript">
				parent.CMS.broadcaster.broadcastMessage("onErrors",
					{ 
						name_space	: \'' . $name_space . '\',
						mode		: \'' . $this->query_action . '\',
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
		
		}else{
		
		
			
			if(isset($_POST['loop_back'])){
				if($_POST['loop_back'] == 'loop'){
					Utils::metaRefresh(CMS_ROOT . 'edit/' . $table . '/' . $id);
				}
				if($_POST['loop_back'] == ''){
					if(isset($_POST['cms_page_state'])){
						Utils::metaRefresh($_POST['cms_page_state']);
					}else{
						if(isset($table)){
							Utils::metaRefresh(CMS_ROOT . 'browse/' . $table);
						}else{
							Utils::metaRefresh(CMS_ROOT . 'home');
						}
					}
				}
			
			}else{
				Utils::metaRefresh(CMS_ROOT . 'home');
			}
		
		
		}
		
		
		
	}


}

?>