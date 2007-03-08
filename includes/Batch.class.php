<?php

class Batch
{
		
	private $cms;
	private $id_set;
	private $page_state;
	
	function __construct($cms)
	{
		$this->cms = $cms;
		$this->action = Utils::setVar("action");
		$this->id_set = Utils::setVar("id_set");
		$this->table = $this->cms->table;
						
		if($this->action == "delete"){
			$body_id = "delete";
			$pagetitle = "Delete records $this->id_set from $this->table ?";
			$this->cms->label = "Delete Records From `$this->table`";
			$this->action = "delete_process";
		}
		if($this->action == "active_false"){
			$pagetitle = "De-Activate Records $this->id_set from $this->table ?";
			$this->cms->label = "De-Activate Records";
		}
		if($this->action == "active_true"){
			$pagetitle = "Activate Records $this->id_set from $this->table ?";
			$this->cms->label = "Activate Records";
		}
		
		$this->processPage();
		
	}
	
	function processPage()
	{
		
		$this->cms->buildHeader();
		print '<div id="content">';
		
		switch(true){
		
			case isset($_GET['action']):
				if ($_GET['action'] == "active_false" || $_GET['action'] == "active_true" ) $this->processActivate();
				if ($_GET['action'] == 'delete'){
					$this->cms->displayDeleteWarning($this->table,$this->id_set);
					require(INCLUDES.'Preview.class.php');
					new Preview($this->cms,$this->table,explode(",",$this->id_set) , $this->action, $_SERVER['HTTP_REFERER']);
				}
			break;
			
			case isset($_POST['action']):
				if($_POST['action'] == "delete_process"){
					$this->processDelete();
				}
			break;
		
			
		
		}
		
		$this->cms->buildFooter();
	
	}
	
	function processDelete()
	{
	
		$this->cms->processDelete($this->table,explode(",",$this->id_set) );
		Utils::metaRefresh(CMS_ROOT . 'browse/' . $this->table);
	
	}
	
	function processActivate()
	{
		
		if($this->action == "active_true"){
			$a = 1;
		}else{
			$a = 0;
		}			
		Db::sql("UPDATE $this->table SET active = $a WHERE id IN ($this->id_set)");
		Utils::metaRefresh(CMS_ROOT . 'browse/' . $this->table);	
	}
	
}

