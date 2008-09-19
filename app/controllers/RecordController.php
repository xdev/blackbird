<?php

class RecordController extends _Controller
{
		
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
		
		//just the main record data
		$this->model->getData(array('query_action'=>$this->query_action));
		$main = $this->ColumnLoop();
		
		$this->view(array('data'=>array('main'=>$main)));
	}
	
	public function Edit()
	{	
		require_once LIB . 'Bobolink' . DS . 'forms' . DS . 'Forms.class.php';
		$this->query_action = 'update';
		
		//set id
		$this->id = $this->route['id'];
		$this->table = $this->route['table'];
		
		//main record data
		$this->model->getData(array('query_action'=>$this->query_action));
		$main = $this->ColumnLoop();
		//all related data
		
		$this->view(array('data'=>array('main'=>$main,'related'=>'Testing Related Data Here')));
	}
	
	private function ColumnLoop()
	{
		//the master loopage		
		//do a few things different if we're editing vs inserting a new record.. however not much
		//use output buffering to feed this to the view... this is a unique controller driven situation
		//since it's almost entirely logic based and presentation uses markup snippets from the Forms library
		
		ob_start();
		
		$_name_space = '';
		
		$recordData = $this->model->data;
		
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
			if($column['config']){
				
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
					//$config = $this->cms->parseConfig($q_col['edit_config']);
					$options = array_merge($options,$config);
				}

				if($q_col['validate'] != ''){
					//$options = array_merge($options,$this->cms->parseConfig($q_col['validate']));
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

							//$this->cms->pluginColumnEdit($_name_space . $column['name'],$value, $options);
							$col_ready = true;
						break;

						case "position":

							//build a selectDefault but with special options ehh
							$options['col_display'] = $column['name'];
							$options['col_value'] = $column['name'];

							//factor in the contraint if set
							if(isset($config['col_constraint'])){
								$options['select_sql'] = "SELECT * FROM `$this->table` WHERE $config[col_constraint] = '".$row_data[$config['col_constraint']]."' ORDER BY $col[Field]";
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
	}
	
	public function pluginColumnEdit()
	{
		
	}
}