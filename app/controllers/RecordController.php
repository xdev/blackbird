<?php

class RecordController extends _Controller
{
	
	/*
	Needs a context switch between rendering of page, or rendering of snippets for remote access
	*/
		
	public function Index()
	{
		
	}
		
	public function Add()
	{
		require_once LIB . 'Bobolink' . DS . 'forms' . DS . 'Forms.class.php';
		$this->query_action = 'insert';
		
		//prepare id 
		$this->id = '';//get it from somewhere in db
		$this->table = $this->route['table'];
		$this->mode = 'insert';
		
		//just the main record data
		$this->model->getData(array('query_action'=>$this->query_action,'table'=>$this->table,'id'=>$this->id,'channel'=>'main'));
		$main = $this->_buildForm();
		
		$this->view(array('data'=>array('main'=>$main)));
	}
	
	public function Edit()
	{	
		$this->query_action = 'update';
		
		//set id
		$this->id = $this->route['id'];
		$this->table = $this->route['table'];
		$this->mode = 'edit';
		
		//main record data
		$this->model->getData(array('query_action'=>$this->query_action,'table'=>$this->table,'id'=>$this->id,'channel'=>'main'));
		$main = $this->_buildForm();
		//all related data
		if($related = $this->model->getRelated()){
		
			for($i=0;$i<count($related);$i++){
				$relation = $related[$i];
				$relation['name_space'] = 'related_' . $relation['table_child'];
				if($relation['label'] == ''){
					$relation['label'] = $relation['table_child'];
				}
				$related[$i] = $relation;
			}
		
		}
		
		$this->view(array('data'=>array('main'=>$main,'related'=>$related,'mode'=>$this->query_action,'name_space'=>'main','table'=>$this->table,'id'=>$this->id)));
	}
	
	public function Editrelated()
	{
		$this->query_action = 'update';
		
		//set id
		$this->id = $_POST['id'];
		$this->table = $_POST['table'];
		$this->mode = 'edit';
		
		//main record data
		$this->model->getData(array('query_action'=>$this->query_action,'table'=>$this->table,'id'=>$this->id,'channel'=>'related'));
		$main = $this->_buildForm();
		$this->view(array('data'=>array('main'=>$main,'mode'=>$this->query_action)));
		
		$this->layout_view = null;
		
	}
	
	private function _buildRelations()
	{
		
		//loop around and scoop up placeholder stuff we need for stuffs, mostly setting variables ehh? Do everything else via javascript?
		
		
	}
	
	private function _buildForm()
	{
		require_once LIB . 'Bobolink' . DS . 'forms' . DS . 'Forms.class.php';
		//the master loopage		
		//do a few things different if we're editing vs inserting a new record.. however not much
		//use output buffering to feed this to the view... this is a unique controller driven situation
		//since it's almost entirely logic based and presentation uses markup snippets from the Forms library
		
		ob_start();
		
		$_name_space = 'main_';
		
		
		Forms::hidden($_name_space . 'table',$this->table,null);
		Forms::hidden($_name_space . 'query_action',$this->query_action,null);
		
		$recordData = $this->model->data;
		$row_data = $recordData;
		
		//loop items
		foreach($recordData as $column){
			$options = array();
			$col_type = strtolower($column['type']);
			$value = $column['value'];

			$col_ready = false;
			$display_name = ucfirst(preg_replace('[_]',' ',$column['name']));

			$options['label'] = $display_name;
			$options['name_space'] = $_name_space;
			$options['db'] = AdaptorMysql::getInstance();

			//built in stuffs
			if (
				$column['name'] == 'id' ||
				$column['name'] == 'active' ||
				$column['name'] == 'created' ||
				$column['name'] == 'modified'
			) {
				if($this->query_action == "update"){
					Forms::hidden($_name_space . $column['name'],$value,$options);
				}
				$col_ready = true;
			}

			//plugins
			
			//col_config needs to be created for each column in the model = FAIL			
			if($column['config'] && !$col_ready){
				
				$q_col = $column['config'];
				
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
					$config = _ControllerFront::parseConfig($q_col['edit_config']);
					$options = array_merge($options,$config);
				}

				if($q_col['validate'] != ''){
					$options = array_merge($options,_ControllerFront::parseConfig($q_col['validate']));
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
							$options['col_name'] = $column['name'];
							
							$file = APP . DS . 'plugins' . DS . 'record_column_edit.php';
							
							if(file_exists($file) && @include_once($file)){
								plugin__record_column_edit($_name_space . $column['name'],$value, $options);
							}
							
							//$this->cms->pluginColumnEdit($_name_space . $column['name'],$value, $options);
							$col_ready = true;
						break;

						case "position":

							//build a selectDefault but with special options ehh
							$options['col_display'] = $column['name'];
							$options['col_value'] = $column['name'];

							//factor in the contraint if set
							if(isset($config['col_constraint'])){
								$options['select_sql'] = "SELECT * FROM `$this->table` WHERE $config[col_constraint] = '".$row_data[$config['col_constraint']]."' ORDER BY $column[name]";
							}else{
								$options['select_sql'] = "SELECT * FROM `$this->table` ORDER BY $column[name]";
							}

							$options['table'] = $this->table;
							$options['col_name'] = $column['name'];
							$options['id'] = $this->id;
							$options['name_space'] = $_name_space;
							$options['label'] = $display_name;

							$options['allow_null'] = false;

							Forms::selectDefault($_name_space . $column['name'],$value, $options);
							$col_ready = true;
						break;


						case "disabled":
							$col_ready = true;
						break;


						default :
							$options['table'] = $this->table;
							$options['col_name'] = $column['name'];
							$options['id'] = $this->id;
							$options['name_space'] = $_name_space;
							$options['label'] = $display_name;
							Forms::$module($_name_space . $column['name'],$value, $options);
							$col_ready = true;
						break;

					}


				}

			}

			//defaults	
			if(!$col_ready){

				switch(true){		

					case ($col_type == 'datetime') :
						Forms::selectDateTime($_name_space . $column['name'],$value, $options );
					break;

					case ($col_type == 'date') :
						Forms::selectDate($_name_space . $column['name'],$value, $options );
					break;

					case ($col_type == 'time') :
						Forms::selectTime($_name_space . $column['name'],$value, $options );
					break;

					case (substr($col_type,0,4) == "text") :
						Forms::textarea($_name_space . $column['name'],$value, $options );
					break;

					default :
						Forms::text($_name_space . $column['name'],$value, $options );
					break;
				}
			
			}	

		}
		
		
		$r = ob_get_contents();
		ob_end_clean();
		
		return $r;
		
	}
	
	public function Process()
	{
		//server side validation
		$this->_name_space = 'main_';
		$this->table = $_POST[$this->_name_space.'table'];
		$this->query_action = $_POST[$this->_name_space.'query_action'];
		
		if($this->query_action == 'update'){		
			$this->id = $_POST[$this->_name_space.'id'];
		}else{
			$this->id = '';
		}
		
		$this->db = AdaptorMysql::getInstance();
		
		$q_cols = $this->db->query("SHOW COLUMNS FROM $this->table");
		$row_data = array();
				
				
				
		//set up error handler here
		$this->errorData = array();		
				
		foreach($q_cols as $col){
			
			
			$col_type = strtolower($col['Type']);
			$col_ready = false;
			
			$q_c = array();
			//get all the base config
			$tA = Utils::checkArray(_ControllerFront::$config['cols'],array('column_name'=>$col['Field']),true);
			if(is_array($tA)){
				$q_c = $tA;
			}
			
			//get anything from the blackbird_cols
			if($q_sql = $this->db->query("SELECT * FROM ".BLACKBIRD_TABLE_PREFIX."cols WHERE column_name = '$col[Field]' AND process_module != '' ORDER BY table_name,process_mode")){
				$q_c = array_merge($q_c,$q_sql);
			}					
						
			$q_col = Utils::checkArray($q_c,array('table_name'=>$this->table,'process_mode'=>$this->query_action));
			if(!$q_col){
				$q_col = Utils::checkArray($q_c,array('table_name'=>$this->table,'process_mode'=>''));
			}
			
			if(!$q_col){
				$q_col = Utils::checkArray($q_c,array('table_name'=>'*','process_mode'=>$this->query_action));
				if(!$q_col){
					$q_col = Utils::checkArray($q_c,array('table_name'=>'*','process_mode'=>''));
				}
			}
						
			if($q_col){
			
				$module = $q_col['process_module'];
								
				switch(true){
			
					case $module == 'plugin' || $module == 'file':
						$options = array();
						$options['mode'] = $this->query_action;
						$options['name_space'] = $this->_name_space;
						
						if($this->query_action == "update"){
							$options['id'] = $this->id;
						}
						if($this->query_action == "insert"){
							$options['id'] = $this->db->getInsertId($this->table);
						}
						$options['col_name'] = $col['Field'];
						$options['table'] = $this->table;
						
						if(isset($_REQUEST[$this->_name_space . $col['Field']])){
							$value = $_REQUEST[$this->_name_space . $col['Field']];
						}else{
							$value = '';
						}
						
						if(strlen($q_col['process_config']) > 1){
							$options = array_merge($options,_ControllerFront::parseConfig($q_col['process_config']));
						}
												
						if($module == 'plugin'){
							//$t = $this->cms->pluginColumnProcess($this->_name_space . $col['Field'],$value,$options);

							if(isset($t['error'])){
								$this->errorData[] = array('field'=>$col['Field'],'error'=>$t['error']);	
							}else{
								if(is_array($t)){
									$row_data[] = $t;
								}
							}
						}
						
						if($module == 'file'){
							$options['db'] = AdaptorMysql::getInstance();
							$name = $this->_name_space . $col['Field'];
							$upload = true;
							
							if(isset($options['file_validator']) && is_uploaded_file($_FILES[$name]['tmp_name'])){
								$t = Utils::validateFile($_FILES[$name],$options['file_validator']);
								if($t === true){

								}else if(is_array($t)){
									$r = '<ul>';
									foreach($t as $row){
										$r .= '<li>'.$row.'</li>';
									}
									$r .= '</ul>';
									$this->errorData[] = array('field'=>$col['Field'],'error'=>$r);
									$upload = false;
								}

							}

							//if so.. do upload
							if($upload === true){
								if($value = Utils::uploadFile($name,$value,$options)) {
									
									$row_data[] = array(
										'field'=>$options['col_name'],
										'value'=>$value
									);
									
									if(isset($options['thumbnails'])){
										foreach($options['thumbnails'] as $thumb){
											$src = WEB_ROOT . 'files/'.$options['table'].'/'.$options['col_name'].'/'.$value;
											$targ = WEB_ROOT . 'files/'.$options['table'].'/'.$thumb['output_directory'].'/image_'.$options['id'].'.jpg';
											Utils::createThumb($src,$targ,$thumb['height'],$thumb['width'],array('quality'=>$thumb['quality'],'mode'=>$thumb['mode']));
										}
									}

								}elseif (isset($_POST[$name.'_delete']) && $_POST[$name.'_delete']) {
									$row_data[] = array(
										'field'=>$options['col_name'],
										'value'=>''
									);
								}
							}
						}
						
						$col_ready = true;
					break;
					
				
					case $module == 'position':
						//if we are a position column
						$where = '';
						
						if(strlen($q_col['process_config']) > 1){
							$config = _ControllerFront::parseConfig($q_col['process_config']);
						}else if(isset($config)){
							unset($config);
						}
						
						$value = $_REQUEST[$this->_name_space . $col['Field']];
						
						if($this->query_action == "update"){
							
							//check for constraints from config
							if(isset($config['col_constraint'])){
								//try to find in row_data
								$foundrow = false;
								foreach($row_data as $temprow){
									if($temprow['field'] == $config['col_constraint']){
										$foundrow = true;
										$where = "WHERE `".$config['col_constraint']."` = '".$temprow['value']."' ";
									}
								}
								if(!$foundrow){
									//check for the $_REQUEST
									$where = "WHERE `".$config['col_constraint']."` = '".$_REQUEST[$this->_name_space . $config['col_constraint']]."' ";
								}
							}
							
							_ControllerFront::sortPosition($this->table,"SELECT id FROM `$this->table` $where ORDER BY $col[Field]",$this->id,$value);
						}
						if($this->query_action == "insert"){
							//check for constraints from config
							if(isset($config)){
								$where = "WHERE `".$config['col_constraint']."` = '".$_REQUEST[$this->_name_space . $config['col_constraint']]."' ";
							}
							
							$q_pos = $this->db->queryRow("SELECT max($col[Field]) FROM `$this->table` $where");
							$row_data[] = array("field"=>$col['Field'],"value"=>($q_pos[0] + 1));
						}
						$col_ready = true;
					break;
					
					case $module == 'timestamp':
						$row_data[] = array("field"=>$col['Field'],"value"=>(($col['Field'] == 'created' && $_REQUEST[$this->_name_space . $col['Field']]) ? $_REQUEST[$this->_name_space . $col['Field']] : Utils::now()));
						$col_ready = true;
					break;
					
				}
				
			}
			
			
			if(!$col_ready){
				//if we are a timestamp
				if($col_type == "datetime"){
					$row_data[] = array("field"=>$col['Field'],"value"=>Utils::assembleDateTime($col['Field'],$this->_name_space));
					$col_ready = true;
				}
			
				if($col_type == "date"){
					$row_data[] = array("field"=>$col['Field'],"value"=>Utils::assembleDate($col['Field'],$this->_name_space));
					$col_ready = true;
				}
			
				if($col_type == "time"){
					$row_data[] = array("field"=>$col['Field'],"value"=>Utils::assembleTime($col['Field'],$this->_name_space));
					$col_ready = true;
				}
			}
			
			
			if(!$col_ready){
				//if we are a generic column
				if(isset($_REQUEST[$this->_name_space . $col['Field']])){
					$row_data[] = array("field"=>$col['Field'],"value"=>$_REQUEST[$this->_name_space . $col['Field']]);
				}
			}
				
		}
		
		$q_table = $this->db->queryRow("SELECT * FROM ".BLACKBIRD_TABLE_PREFIX."tables WHERE table_name = '$this->table'");
		
		//die(print_r($row_data));
		
		if(strlen($q_table['process_module']) > 3){
			//$this->cms->pluginTableProcess($this->table,$this->id,$this->query_action);
		}else{
				
			if(count($this->errorData) == 0){
				
				if($this->query_action == "insert"){
					$sql = $this->db->insert($this->table,$row_data);
					$this->id = mysql_insert_id();
				}
				
				if($this->query_action == "update"){
					$sql = $this->db->update($this->table,$row_data,"id",$this->id);
				}
						
				$row_data = array();
				$row_data[] = array('field'=>'table_name','value'=>$this->table);
				$row_data[] = array('field'=>'record_id','value'=>$this->id);
				$row_data[] = array('field'=>'action','value'=>$this->query_action);
				//$row_data[] = array('field'=>'user_id','value'=>$this->cms->session->u_id);
				$row_data[] = array('field'=>'sql','value'=>$sql);
				$row_data[] = array('field'=>'session_id','value'=>session_id());
				
				$this->db->insert(BLACKBIRD_TABLE_PREFIX.'history',$row_data);
				
			}else{
				
				$GLOBALS['errors'] = $this->errorData;
			
			}
		
		}
		
		$this->layout_view = null;		
	}
	
	public function pluginColumnEdit()
	{
		
	}
}