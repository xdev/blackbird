<?php

/* $Id$ */

class EditPage
{

	private $cms;
	private $id;
	private $table;
	private $valid = false;
	private $mode;
	private $body_id;
	private $query_action;
	private $pagetitle;
	
	function __construct($cms){
		$this->cms = $cms;
		$this->db = $cms->db;
		$this->table = $this->cms->table;
		$this->id = $this->cms->id;
		
		if($this->cms->id){
			$this->mode = "edit";
			$this->pagetitle = $this->cms->label . " - Edit";
			$this->cms->label = $this->pagetitle;
			$this->body_id = "edit";
			$this->query_action = "update";
	
		}else{
			$this->mode = "insert";
			$this->pagetitle = $this->cms->label . " - Add";
			$this->cms->label = $this->pagetitle;
			$this->body_id = "insert";
			$this->query_action = "insert";
		}
		$this->buildPage();
	}
	
	function buildPage(){
		
		$history = false;
		if(!$this->cms->session->privs('browse',$this->table)){
			die("You don't have sufficient privileges to view this table");
		}
						
		if(isset($_SERVER['HTTP_REFERER'])){
			if($this->cms->refA[0] == 'browse'){
				$this->cms_page_state = preg_replace('[&]','&amp;',$_SERVER['HTTP_REFERER']);
			}else{
				$this->cms_page_state = CMS_ROOT . 'browse/' . $this->table;
			}
		}else{
			$this->cms_page_state = CMS_ROOT . 'browse/' . $this->table;
		}
				
		$js = '
		<script type="text/Javascript" src="' . CMS_ROOT . ASSETS . 'js/datagrid.js" ></script>
		<script type="text/Javascript" src="' . CMS_ROOT . ASSETS . 'js/form.js" ></script>
		<script type="text/Javascript" src="' . CMS_ROOT . ASSETS . 'js/dropdown.js" ></script>
		<script type="text/Javascript" src="' . CMS_ROOT . ASSETS . 'js/listmanager.js" ></script>';
		
		$css = '';
		
		$q_headers = $this->db->queryRow("SELECT * FROM cms_headers WHERE table_name = '*' AND mode = 'edit'");
		if($q_headers['javascript'] != ''){
			$js .= $q_headers['javascript'];
		}
		if($q_headers['css'] != ''){
			$css .= $q_headers['css'];
		}
		$q_headers = $this->db->queryRow("SELECT * FROM cms_headers WHERE table_name = '$this->table' AND mode = 'edit'");
		if($q_headers['javascript'] != ''){
			$js .= $q_headers['javascript'];
		}
		if($q_headers['css'] != ''){
			$css .= $q_headers['css'];
		}
		
		$q_help = $this->db->queryRow("SELECT help FROM cms_tables WHERE table_name = '$this->table'");
				
		$this->cms->buildHeader($js,$css,' class="edit"',$q_help['help']);
		
		print "<div id=\"content\" class=\"clearfix\" >";
		
		
		if(isset($this->cms->pathA[3])){
			if ($this->cms->pathA[3] == 'looped'){
				print '<div class="message ok">Record ' . $this->cms->id . ' was saved successfully. <a href="#" onclick="this.up().remove();">Close</a></div>';
			}
		}
		
		if($this->mode == "edit"){
			$row_data = $this->db->queryRow("SELECT * FROM `$this->table` WHERE id = '$this->id'");
		}else{
			$row_data = null;
		}
		
		$q_related = $this->db->query("SELECT * FROM cms_relations WHERE table_parent = '$this->table' ORDER BY position");
				
		//do the tab navigation
		print '
		
		<ul id="edit_nav">
				<li id="tab_main" class="trigger active" ><a href="#" onclick="CMS.showTab(\'main\'); return false;">Details</a></li>';
		$i = 0;
		if($q_related){
			if($this->mode == "edit"){
				$class = 'class="trigger"';
			}
			if($this->mode == "insert"){
				$class = 'class="trigger dim"';
			}
			foreach($q_related as $relation){
				print '<li id="tab_related' . $i . '"' . $class . ' ><a href="#" onclick="CMS.showTab(\'related' . $i . '\'); return false;">' . $relation['label'] . '</a></li>';
				$i++;
			}
		}
		
		
		
		if($this->mode == 'edit' && $this->table != 'cms_history'){
			$q_history = $this->db->query("SELECT * FROM cms_history WHERE table_name = '$this->table' AND record_id = '$this->id'");
			if($q_history && count($q_history) > 0){
				print '<li id="tab_history" class="trigger history" ><a href="#" onclick="CMS.showTab(\'history\'); return false;">History</a></li>';
				$history = true;
			}
		}
		
		
		
		print '</ul><div class="clearfix"></div><div class="panes">';
		
		if(is_array($row_data) || $this->mode == "insert"){
			
			print '<div class="toggle main_' . $this->table . '" id="pane_main"><div class="pane">';
				
			Forms::init(CMS_ROOT . "process/remote","form_main","multipart",'post','target="form_target_main" onsubmit="Element.show(\'ajax\');"');
			Forms::hidden("name_space",'main',array('omit_id'=>true));
			Forms::hidden("query_action",$this->query_action,array('omit_id'=>true));
			Forms::hidden("table",$this->cms->table,array('omit_id'=>true));
			Forms::hidden("id",$this->cms->id,array('omit_id'=>true));
			Forms::hidden("loop_back",'');
			Forms::hidden("cms_page_state",$this->cms_page_state);
			
			require(INCLUDES.'EditRecord.class.php');
			new EditRecord($this->cms,$this->table,$this->id,'main','main');			
			
			Forms::closeTag();
			
			print '<iframe id="form_target_main" name="form_target_main" class="related_iframe"></iframe>';
			
			print '</div></div>';
			
			
			if($q_related){
				
				print '<div id="ajax" style="display:none;"><div class="container"><div class="icon"></div><!--<div class="message">Loading...</div>--></div></div>';
				
				$i = 0;
				foreach($q_related as $relation){
					
					$name_space = 'related' . $i;
					if(strlen($relation['config']) > 1){
						$config = $this->cms->parseConfig($relation['config']);
					} else {
						$config = '';
					}
					
					print '<div class="toggle related_' . $relation['table_child'] . '" id="pane_' . $name_space .'" style="display:none;"><div class="pane">';
					
					if($this->mode == "edit"){
					
						if($relation['display'] == 'data_grid'){
							
							print '
							<div class="edit_form" style="display:none;">
								<div class="detail"></div>
							</div>';
							
							print '<div class="data_grid_embed">';
							
							include_once(INCLUDES.'DataGridAjax.class.php');					
							$module = new DataGridAjax($this->cms);
							$module->cms = $this->cms;
							$module->table = $relation['table_child'];
							$module->table_parent = $this->cms->table;
							$module->id_parent = $this->cms->id;
							$module->name_space = $name_space;
							$module->config = $config;						
							$module->build();
							
							print '</div>';
							
							print '
							<script type="text/javascript">
								<!-- <![CDATA[ 
								var data_grid_' . $name_space . '= new dataGrid(
									{ 	
										table: \'' . $relation['table_child'] . '\',
										table_parent: \'' . $this->cms->table . '\',
										id_parent: \'' . $this->cms->id . '\',
										name_space: \''. $name_space . '\',
										cms_root: \'' . CMS_ROOT . '\'';
										if(isset($config['sql_where'])){
											print ',sql_where: "' . $config['sql_where'] . '"';
										}
										print '
									}
								);
								CMS.broadcaster.addListener(data_grid_'.$name_space.'.listener);
								CMS.addCallback(\'' . $name_space . '\',data_grid_'.$name_space.',"getUpdate");
								// ]]> -->
							</script>';
						}
						
						
						if($relation['display'] == 'module'){
							$class = $config['module'];
							include_once(INCLUDES.'modules/' . $class . '.class.php');
							$module = new $class($this->cms);
							//$module->cms = $this->cms;
							$module->table = $relation['table_child'];
							$module->name_space = $name_space;
							$module->config = $config;
							$module->build();						
						}
						
						if($relation['display'] == 'plugin'){
							$class = $config['module'];
							include_once(INCLUDES.'Plugin.interface.php');					
							include_once(CUSTOM."php/" . $class . ".class.php");
							$module = new $class();
							$module->cms = $this->cms;
							$module->table = $relation['table_child'];
							$module->name_space = $name_space;
							$module->config = $config;
							$module->build();						
						}
					
					}
					if($this->mode == "insert"){
						print '<div class="message error">To add related content, first save the main record.</div>';
					}
					
					
					print '</div></div>';
					$i++;
				}
				
				
			}
			
			if($history){
				
				$name_space = 'history';
				print '<div class="toggle" id="pane_' . $name_space .'" style="display:none;"><div class="pane">
				
				<div class="edit_form" style="display:none;">
					<div class="detail"></div>
				</div>';
				
				
				
				print '<div class="data_grid_embed">';
				
				include_once(INCLUDES.'DataGridAjax.class.php');					
				$module = new DataGridAjax($this->cms);
				$module->cms = $this->cms;
				$module->table = 'cms_history';
				$module->name_space = $name_space;
				$module->mode = 'readonly';
				$module->sql = "table_name = '$this->table' AND record_id = '$this->id'";
				$module->build();
				
				print '</div>';
				
				print '</div>';
				
				print '
				<script type="text/javascript">
					<!-- <![CDATA[ 
					var data_grid_' . $name_space . '= new dataGrid(
						{ 	
							table: \'cms_history\',
							table_parent: \'' . $this->cms->table . '\',
							id_parent: \'' . $this->cms->id . '\',
							name_space: \''. $name_space . '\',
							cms_root: \'' . CMS_ROOT . '\'
						}
					);
					// ]]> -->
				</script>';
				
				print '</div>';
			
			}
					
			$valid = true;
			
		}else{
		
			printf('<p class="error">There was no record %s found in TABLE %s!</p></div>',$this->id,$this->table);
		
		}
		
		print '</div>';
		
		if($valid === true){
			print '
			<script type="text/javascript">
				<!-- <![CDATA[ 
				CMS.setProperty("table_parent","' . $this->table . '");
				CMS.setProperty("id_parent","' . $this->id . '");				
				
				if($("toggle_help")){
					Event.observe("toggle_help", "click", function(){CMS.toggleHelp(); return false; }, true);
				}';				
				
				
				if($this->mode == "edit"){
					print "Event.observe(window, 'load', function(){
						formController_main = new formController('main');					
					}, true);";
					
				}
				
				
			print "// ]]> -->
			</script>";
		}
		
		
		
		print '<div class="clearfix" id="edit_buttons">';
		
			if($this->cms->session->privs($this->query_action,$this->table)){
				print '<a class="button save_back" href="#" onclick="CMS.submitMain(\'main\'); return false;">Save &amp; Back to Browse</a>';
				print '<a class="button save_continue" href="#" onclick="CMS.loopBack(\'main\'); return false;" >Save &amp; Continue Editing</a>';
			}
			print '<a class="button back" href="#" onclick="window.location=\'' . $this->cms_page_state . '\'; return false;" >Back to Browse</a>';
		print '</div>';
		
		$this->cms->buildFooter();

	}

}
?>