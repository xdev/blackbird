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
		$this->query_action = 'insert';
		
		//prepare id 
		$this->id = '';//get it from somewhere in db
		$this->table = $this->route['table'];
		$this->mode = 'add';
		
		$this->name_space = 'main';
		$this->channel = 'main';
		
		//just the main record data
		$this->model->getData(array('query_action'=>$this->query_action,'table'=>$this->table,'id'=>$this->id,'channel'=>'main'));
		$main = $this->_buildForm();
		
		//get active state
		
		$this->view(array('data'=>array(
			'main'=>$main,
			'id'=>$this->id,
			'table'=>$this->table,
			'mode'=>$this->mode,
			'name_space'=>$this->name_space,
			'permission_insert'=>_ControllerFront::$session->getPermissions('insert',$this->table)
			)));
	}
	
	public function Edit()
	{	
		$this->query_action = 'update';
		
		//set id
		$this->id = $this->route['id'];
		$this->table = $this->route['table'];
		$this->mode = 'edit';
		$this->channel = 'main';
		
		$this->name_space = 'main';
		
		//main record data
		$this->model->getData(array('query_action'=>$this->query_action,'table'=>$this->table,'id'=>$this->id,'channel'=>$this->channel));
		$main = $this->_buildForm();
		//all related data
		if($related = $this->model->getRelated($this->table)){
		
			for($i=0;$i<count($related);$i++){
				$relation = $related[$i];
				$relation['name_space'] = 'related_' . $relation['table_child'];
				if($relation['label'] == ''){
					$relation['label'] = $relation['table_child'];
				}
				$relation['permission_insert'] = _ControllerFront::$session->getPermissions('insert',$relation['table_child']);
				$related[$i] = $relation;
			}
		
		}
		
		$this->view(array('data'=>array(
			'main'=>$main,
			'related'=>$related,
			'mode'=>$this->mode,
			'name_space'=>$this->name_space,
			'table'=>$this->table,
			'id'=>$this->id,
			'permission_delete'=>_ControllerFront::$session->getPermissions('delete',$this->table),
			'permission_update'=>_ControllerFront::$session->getPermissions('update',$this->table)
			)));
	}
	
	public function Editrelated()
	{
		
		$this->table = $_POST['table'];
		$this->mode = $_POST['mode'];
		$this->query_action = $_POST['query_action'];
		if($this->mode == 'edit'){
			$this->id = $_POST['id'];
		}else{
			$this->id = '';
		}
		
		$this->channel = 'related';
		$this->name_space = $_POST['name_space'];
		
		//main record data
		$this->model->getData(array('query_action'=>$this->query_action,'table'=>$this->table,'id'=>$this->id,'channel'=>$this->channel));
		$main = $this->_buildForm();
		
		if($this->model->data['active']['value'] != ''){
			$this->active = $this->model->data['active']['value'];
		}else{
			$this->active = 1;
		}
							
		$this->view(array('data'=>array(
			'main'=>$main,
			'mode'=>$this->mode,
			'name_space'=>$_POST['name_space'],
			'id'=>$this->id,
			'active'=>$this->active,
			'permission_delete'=>_ControllerFront::$session->getPermissions('delete',$this->table),
			'permission_update'=>_ControllerFront::$session->getPermissions('update',$this->table),
			'permission_insert'=>_ControllerFront::$session->getPermissions('insert',$this->table)			
			)));
		
		$this->layout_view = null;
		
	}
	
	public function Delete()
	{
		//take table and id
		$table = $_POST['table'];
		$id = $_POST['id'];
		$this->model->processDelete($table,explode(",",$id));
		
		$this->layout_view = null;
		
		/* needs to handle errors
		$this->view(array('data'=>array(
			'table'=>$table,
			'id'=>$id)));
		*/
	}
		
	
	private function _buildRelations()
	{
		
		//loop around and scoop up placeholder stuff we need for stuffs, mostly setting variables ehh? Do everything else via javascript?
		
		
	}
	
	private function _buildForm()
	{
		//the master loopage		
		//do a few things different if we're editing vs inserting a new record.. however not much
		//use output buffering to feed this to the view... this is a unique controller driven situation
		//since it's almost entirely logic based and presentation uses markup snippets from the Forms library
		
		ob_start();
		
		$_name_space = $this->name_space . '_';
		
		//processing instructions
		Forms::hidden('name_space',$this->name_space,array('omit_id'=>true));
		Forms::hidden('mode',$this->mode,array('omit_id'=>true));
		Forms::hidden('channel',$this->channel,array('omit_id'=>true));
		Forms::hidden('table',$this->table,array('omit_id'=>true));
		Forms::hidden('query_action',$this->query_action,array('omit_id'=>true));
		
		$recordData = $this->model->data;
		$row_data = $recordData;
		
		//CMS_RELATIONS fields
		if($this->channel == "related"){
			$sql = "SELECT * FROM ". BLACKBIRD_TABLE_PREFIX ."relations WHERE table_parent = '" . $_POST['table_parent'] . "' AND table_child = '$this->table'";
			if($q_relation = AdaptorMysql::queryRow($sql)){
				$i=0;
				foreach($recordData as $column){
					if($column['name'] == $q_relation['column_child']){
						array_splice($recordData,$i,1);
						break;
					}
					$i++;
				}
				$q_parent = AdaptorMysql::queryRow("SELECT * FROM " . $_POST['table_parent'] . " WHERE id = " . $_POST['id_parent']);					
				Forms::hidden($_name_space . $q_relation['column_child'],$q_parent[$q_relation['column_parent']]);
				Forms::hidden('id_parent',$q_parent[$q_relation['column_parent']],array('omit_id'=>true));
			}
		}
		
		//loop items
		foreach($recordData as $column){
			$options = array();
			$col_type = strtolower($column['type']);
			$value = $column['value'];

			$col_ready = false;
			$display_name = Utils::titleCase(str_replace('_',' ',$column['name']));

			$options['label'] = $display_name;
			$options['name_space'] = $_name_space;
			$options['db'] = AdaptorMysql::getInstance();

			//built in stuffs
			if (
				$column['name'] == 'id'
			) {
				if($this->query_action == "update" || $column['name'] == 'active'){
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
							
							_ControllerFront::pluginColumnEdit($_name_space . $column['name'],$value, $options);
							$col_ready = true;
						break;

						case "position":

							//build a selectDefault but with special options ehh
							$options['col_display'] = $column['name'];
							$options['col_value'] = $column['name'];
							
							//do it manual style, to accomodate constraint change or late entries
							
							//do a relative selection of position, based upon existing list.. oh!
							
							//factor in the contraint if set
							if(isset($config['col_constraint'])){
								$options['select_sql'] = "SELECT * FROM `$this->table` WHERE $config[col_constraint] = '".$row_data[$config['col_constraint']]['value']."' ORDER BY $column[name]";
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
						
						case "slug":
							
							$source = isset($config['col_source']) ? $_name_space . $config['col_source'] : null;
							
							Forms::text($_name_space . $column['name'],$value,$options);
							print '
								<script type="text/javascript">
									Event.observe(window,\'load\', function(){createSlug(\''.$_name_space . $column['name'].'\',\''.$source.'\')}, true);
								</script>
							';
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
							
							//add database datasource for countries and states
							if($module == 'selectState'){
								$options['datasource'] = BLACKBIRD_TABLE_PREFIX . 'states';
							}
							if ($module == 'selectCountry'){
								$options['datasource'] = BLACKBIRD_TABLE_PREFIX . 'countries';
							}
							
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
		$this->layout_view = null;
		
		
		//server side validation
		$this->_name_space = $_POST['name_space'] . '_';
		$this->mode = $_POST['mode'];
		$this->table = $_POST['table'];
		$this->query_action = $_POST['query_action'];
		$this->channel = $_POST['channel'];
		
		$this->key = AdaptorMysql::getPrimaryKey($this->table);
		
		if($this->query_action == 'update'){		
			$this->id = $_POST[$this->_name_space . $this->key];
		}else{
			$this->id = '';
		}
		
		$this->db = AdaptorMysql::getInstance();
		
		$q_cols = $this->db->query("SHOW COLUMNS FROM $this->table",MYSQL_BOTH);
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
						$options['db'] = AdaptorMysql::getInstance();
						
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
							
							$t = _ControllerFront::pluginColumnProcess($this->_name_space . $col['Field'],$value, $options);
							
							if(isset($t['error'])){
								$this->errorData[] = array('field'=>$col['Field'],'error'=>$t['error']);	
							}else{
								if(is_array($t)){
									$row_data[] = $t;
								}
							}
						}
						
						if($module == 'file'){
														
							
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
							
							_ControllerFront::sortPosition($this->table,"SELECT id FROM `$this->table` $where ORDER BY $col[Field]",$this->id,$value,$col['Field']);
						}
						if($this->query_action == "insert"){
							//check for constraints from config
							if(isset($config)){
								$where = "WHERE `".$config['col_constraint']."` = '".$_REQUEST[$this->_name_space . $config['col_constraint']]."' ";
							}
							$q_pos = $this->db->queryRow("SELECT max($col[Field]) AS position FROM `$this->table` $where");
							$row_data[] = array("field"=>$col['Field'],"value"=>($q_pos['position'] + 1));
						}
						$col_ready = true;
					break;
					
					case $module == 'slug':
						
						function checkSlug($slug,$options)
						{
							if ($slug != '*' && $q = $options['db']->query("
								SELECT ".$options['col_name']."
								FROM ".$options['table']."
								WHERE id != '".$options['id']."'
									AND ".$options['col_name']." = '".$slug."'".$options['where']."
							")) {
								if (is_numeric($i = substr($slug,strrpos($slug,'_')+1))) $slug = substr($slug,0,strrpos($slug,'_')+1).($i+1);
								else $slug .= '_1';
								return checkSlug($slug,$options);
							} else {
								return $slug;
							}
						}
						
						if(strlen($q_col['process_config']) > 1){
							$config = _ControllerFront::parseConfig($q_col['process_config']);
						}else if(isset($config)){
							unset($config);
						}
						
						$value = $_REQUEST[$this->_name_space . $col['Field']];
						if ($this->query_action == 'insert') {
							$this->id = mysql_insert_id();
							//$q_pos = $this->db->queryRow("SELECT max($col[Field]) FROM `$this->table` $where");
							//$this->id = $q_pos[0] + 1;
						}
						//check for constraints from config
						$where = "";
						if(isset($config['col_constraint'])){
							//try to find in row_data
							foreach($row_data as $temprow){
								if($temprow['field'] == $config['col_constraint']){
									$where = " AND `".$temprow['field']."` = '".$temprow['value']."' ";
								}
							}
						}
						$value = checkSlug($value,array(
							'col_name' => $col['Field'],
							'table' => $this->table,
							'id' => $this->id,
							'where' => $where,
							'db' => $this->db
						));
						/*if ($value != '*' && $q = $this->db->query("
							SELECT ".$col['Field']."
							FROM ".$this->table."
							WHERE id != '".$this->id."'
								AND ".$col['Field']." = '".$value."'".$where."
						")) {
							if (is_numeric($i = substr($value,strrpos($value,'_')+1))) $value = substr($value,0,strrpos($value,'_')+1).($i+1);
							else $value = $value.'_1';
						}*/
						$row_data[] = array("field"=>$col['Field'],"value"=>$value);
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
						
		if(strlen($q_table['process_module']) > 3){
			//$this->cms->pluginTableProcess($this->table,$this->id,$this->query_action);
		}else{
				
			if(count($this->errorData) == 0){
				
				if($this->query_action == "insert"){
					$sql = $this->db->insert($this->table,$row_data);					
					$this->id = mysql_insert_id();
				}
				
				if($this->query_action == "update"){
					$key = AdaptorMysql::getPrimaryKey($this->table);
					$sql = $this->db->update($this->table,$row_data,$key,$this->id);
				}
						
				$row_data = array();
				$row_data[] = array('field'=>'table_name','value'=>$this->table);
				$row_data[] = array('field'=>'record_id','value'=>$this->id);
				$row_data[] = array('field'=>'action','value'=>$this->query_action);
				$row_data[] = array('field'=>'user_id','value'=>_ControllerFront::$session->u_id);
				$row_data[] = array('field'=>'sql','value'=>$sql);
				$row_data[] = array('field'=>'session_id','value'=>session_id());
				
				$this->db->insert(BLACKBIRD_TABLE_PREFIX.'history',$row_data);
				
				$this->view(array('data'=>array(
					'mode'=>$this->mode,
					'query_action'=>$this->query_action,
					'channel'=>$this->channel,
					'name_space'=>$_POST['name_space'],
					'table'=>$this->table,
					'id'=>$this->id)));
				
			}else{
				
				//$GLOBALS['errors'] = $this->errorData;
				$this->view(array('view'=>'/_errors/remote','data'=>array(
					'mode'=>$this->mode,
					'query_action'=>$this->query_action,
					'channel'=>$this->channel,
					'name_space'=>$_POST['name_space'],
					'table'=>$this->table,
					'id'=>$this->id,
					'errors'=>$this->errorData)));
			
			}
		
		}
				
		
		//if we have warnings, feed them back	
		
		
		
		
	}
	
}