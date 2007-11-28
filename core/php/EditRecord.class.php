<?php

class EditRecord
{


	private $id;
	private $cms;
	private $table;
	private $name_space;
	private $mode;
	private $query_action;
	
	function __construct($cms,$table,$id,$name_space="",$channel){
		
		$this->cms = $cms;
		$this->db = $this->cms->db;
		$this->table = $table;
		$this->id = $id;
		$this->name_space = $name_space;
		$this->channel = $channel;
		if($this->id){
			$this->mode = "edit";
			$this->query_action = "update";
	
		}else{
			$this->mode = "insert";
			$this->query_action = "insert";
		}
		
		$this->buildPage();
	}
	
	
	
	
	function buildPage(){
							
	
		if($this->mode == "edit"){
			$row_data = $this->db->queryRow("SELECT * FROM $this->table WHERE id = '$this->id'");
		}else{
			$row_data = false;
		}
		
		$q_related = $this->db->query("SELECT * FROM cms_relations WHERE table_parent = '$this->table'");
				
		
		
		if($row_data || $this->mode == "insert"){
			
			$_name_space = '';
			if(strlen($this->name_space) > 1){
				$_name_space = $this->name_space . "_";
			}
							
			$q_cols = $this->db->query("SHOW COLUMNS FROM `$this->table`");
			
			if($this->channel == "related"){
				
				
				
				if($q_relation = $this->db->queryRow("SELECT * FROM cms_relations WHERE table_parent = '" . $_REQUEST['table_parent'] . "' AND table_child = '$this->table'")){
					
					
					for($i=0;$i<count($q_cols);$i++){
						$col = $q_cols[$i];
						
						if($col['Field'] == $q_relation['column_child']){
							array_splice($q_cols,$i,1);
							break;
						}
					}
					
					$q_parent = $this->db->queryRow("SELECT * FROM " . $_REQUEST['table_parent'] . " WHERE id = " . $_REQUEST['id_parent']);					
					Forms::hidden($_name_space . $q_relation['column_child'],$q_parent[$q_relation['column_parent']]);
					Forms::hidden('id_parent',$q_parent[$q_relation['column_parent']],array('omit_id'=>true));
				}
			}
			
			
		
			foreach($q_cols as $col){
		
				$col_ready = false;
				$q_col = false;
				$value = $row_data[$col['Field']];
				$options = array();
				$display_name = preg_replace('[_]',' ',$col['Field']);
				$display_name = strtoupper($display_name{0}) . substr($display_name,1);

				$options['label'] = $display_name;				
				$options['name_space'] = $_name_space;
				$options['db'] = $this->db;
				
				if($col['Field'] == 'id'){
					if($this->query_action == "update"){
						Forms::readonly($_name_space . 'id',$value,$options);
					}
					$col_ready = true;
					
				}
				
				if(!$col_ready){
				
					$q_c = $this->db->query("SELECT * FROM cms_cols WHERE column_name = '$col[Field]' ORDER BY table_name,edit_mode,edit_channel");
					if($q_c){				
						$q_col = Utils::checkArray($q_c,array('table_name'=>$this->table,'edit_mode'=>$this->mode,'edit_channel'=>$this->channel));
						if(!$q_col){
							$q_col = Utils::checkArray($q_c,array('table_name'=>$this->table,'edit_mode'=>$this->mode,'edit_channel'=>''));
						}
						
						if(!$q_col){
							$q_col = Utils::checkArray($q_c,array('table_name'=>$this->table,'edit_mode'=>'','edit_channel'=>$this->channel));
							if(!$q_col){
								$q_col = Utils::checkArray($q_c,array('table_name'=>$this->table,'edit_mode'=>'','edit_channel'=>''));
							}
						}
						
						if(!$q_col){
							$q_col = Utils::checkArray($q_c,array('table_name'=>'*','edit_mode'=>$this->mode,'edit_channel'=>$this->channel));
							if(!$q_col){
								$q_col = Utils::checkArray($q_c,array('table_name'=>'*','edit_mode'=>$this->mode,'edit_channel'=>''));
							}
						}
						
						if(!$q_col){
							$q_col = Utils::checkArray($q_c,array('table_name'=>'*','edit_mode'=>'','edit_channel'=>$this->channel));
							if(!$q_col){
								$q_col = Utils::checkArray($q_c,array('table_name'=>'*','edit_mode'=>'','edit_channel'=>''));
							}
						}
					
					}
				
				}
				
				
												
				if($q_col){
					
					if($q_col['default_value'] != '' && $this->mode == "insert"){
						$value = $q_col['default_value'];
					}
					
					if(strlen($q_col['help'])>1){
						$options['tip']	= $q_col['help'];
					}
	
					if($q_col['display_name'] != ''){
						$display_name = $q_col['display_name'];
					}
					
					$options['label'] = $display_name;
					
												
					$module = $q_col['edit_module'];
					
					if(strlen($q_col['edit_config']) > 1){
						$config = Utils::parseConfig($q_col['edit_config']);
						$options = array_merge($options,$config);
					}
					
					if($q_col['validate'] != ''){
						$options = array_merge($options,Utils::parseConfig($q_col['validate']));
					}
										
					if($module != ""){
						
						switch ($module) {
							
							case "module":
							
							
							break;
							
							case "plugin":
								$options['table'] = $this->table;
								$options['mode'] = $this->mode;
								$options['row_data'] = $row_data;
								$options['id'] = $this->id;
								$options['col_name'] = $col['Field'];
								
								$this->cms->pluginColumnEdit($_name_space . $col['Field'],$value, $options);
								$col_ready = true;
							break;
							
							
							case "disabled":
								$col_ready = true;
							break;
							
							
							default :
								$options['name_space'] = $_name_space;
								$options['label'] = $display_name;
								Forms::$module($_name_space . $col['Field'],$value, $options);
								$col_ready = true;
							break;
							
						}
						
																	
					}
					
				}
		
				if(!$col_ready){
					//otherwise do the default behavior
					$col_type = strtolower($col['Type']);
					
					switch(true){
						
						case ($col_type == 'datetime') :
							Forms::selectDateTime($_name_space . $col['Field'],$value, $options );
						break;
						
						case ($col_type == 'date') :
							Forms::selectDate($_name_space . $col['Field'],$value, $options );
						break;
						
						case ($col_type == 'time') :
							Forms::selectTime($_name_space . $col['Field'],$value, $options );
						break;
												
						case (substr($col_type,0,4) == "text") :
							Forms::textarea($_name_space . $col['Field'],$value, $options );
						break;

						default :
							Forms::text($_name_space . $col['Field'],$value, $options );
						break;
					}
				}
				
				
				
			}
			
		}
		
	}

}

?>