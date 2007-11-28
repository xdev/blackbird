<?php

class Ajax
{
	
	private $cms;
	private $id;
	private $table;
	private $name_space;
	
	function __construct($cms)
	{
		$this->cms = $cms;
		$this->db = $cms->db;
		$this->name_space = Utils::setVar("name_space");
		
		$action = Utils::setVar("action");
		$mode = Utils::setVar("mode");		
		
		$_name_space = '';
		if(strlen($this->name_space) > 1){
			$_name_space = $this->name_space . "_";
		}
		
		$this->table = Utils::setVar('table');
		$this->cms->id = Utils::setVar('id_parent');
		$this->table_parent = Utils::setVar('table_parent');
		
		//module
		if($action == "loadModule"){
			
			$class = Utils::setVar("module");
			
			include_once(INCLUDES . "modules/$class.class.php");
			$module = new $class($this->cms);
			$module->name_space = $this->name_space;
			$module->table = $this->table;
						
			if($this->table_parent != ''){
				$q_relation = $this->db->queryRow("SELECT * FROM cms_relations WHERE table_parent = '$this->table_parent' AND table_child = '$this->table'");	
				$module->config = Utils::parseConfig($q_relation['config']);
			}			
			
			if(isset($_POST['remote_method'])){
				$method = $_POST['remote_method'];
				$module->$method();
			}
		
		}
		
		//plugin
		if($action == "loadPlugin"){
			
			$plugin = Utils::setVar("plugin");
			
			include_once(INCLUDES . "Plugin.interface.php");					
			include_once(CUSTOM . "php/$plugin.class.php");
			$module = new $plugin($this->cms);
			
			$module->cms = $this->cms;
			$module->name_space = $this->name_space;
			$module->table = $this->table;
			
			if($this->table_parent != ''){
				$q_relation = $this->db->queryRow("SELECT * FROM cms_relations WHERE table_parent = '$this->table_parent' AND table_child = '$this->table'");
				$module->config = Utils::parseConfig($q_relation['config']);
			}
			
			if(isset($_POST['remote_method'])){
				$method = $_POST['remote_method'];
				$module->$method();
			}
		
		}
		
		//edit record
		if($action == "editRecord"){
		
			$id = Utils::setVar('id');
			Forms::init(CMS_ROOT . "process/remote","form_" . $this->name_space,"multipart",'post','target="form_target_' . $this->name_space .'" onsubmit="Element.show(\'ajax\');"');
			
			Forms::hidden("name_space",$this->name_space);
			Forms::hidden("table_parent",$_POST['table_parent']);
			Forms::hidden("table",$this->table);
			Forms::hidden("id",$id);
			Forms::hidden("query_action",$mode);
			
			include_once(INCLUDES . "EditRecord.class.php");
			new EditRecord($this->cms,$this->table,$id,$this->name_space,'related');			
			
			print '<div class="buttons">';
			if($this->cms->session->privs($mode,$this->table)){
				print '<a class="button save" href="#" onclick="CMS.submitRelated(\'' . $this->name_space . '\');" >Save</a>';				
			}
			print '<a class="button cancel" href="#" onclick="CMS.closeRecord(\'' . $this->name_space . '\')" >Cancel</a>';		
			print '</div>';
			
			Forms::closeTag();
			
			print '<iframe id="form_target_' . $this->name_space .'" name="form_target_' . $this->name_space .'" class="related_iframe"></iframe>';
			print '<div class="clearfix"></div>';
			
			/*
			if($mode == 'update'){
				print '
				<script type="text/javascript">
					formController_' . $this->name_space . ' = new formController(\'' . $this->name_space . '\');	
				</script>';
			}
			*/
					
		}
		
		if($action == "getDataGrid"){
		
			$this->getDataGrid();
		
		}
		
		//delete record
		if($action == "deleteRecord"){
			
			$this->cms->processDelete($this->table,array($_POST['id']) );
			print '.';

		}			
		
	}
	
	function getDataGrid()
	{
				
		$relation = $this->db->queryRow("SELECT * FROM cms_relations WHERE table_parent = '$_POST[table_parent]' AND table_child = '$this->table'");
		
		include_once(INCLUDES . "DataGridAjax.class.php");					
		$module = new DataGridAjax($this->cms);
		$module->cms = $this->cms;
		$module->table = $relation['table_child'];
		$module->name_space = $this->name_space;
		
		if($this->table == 'cms_history'){
			
			$tid = $this->cms->id;
			$module->table = 'cms_history';
			$module->mode = 'readonly';
			$module->sql = "table_name = '$_POST[table_parent]' AND record_id = '$tid'";
		}
		$module->build();		
			
	}
	
}

?>