<?php

class ImagebrowserController extends _Controller
{
	public function Sort()
	{
		$this->layout_view = null;
		
		$table = $_POST['table'];
		$idSet = explode(",",$_POST['id_set']);
		
		$q_related = AdaptorMysql::queryRow("SELECT * FROM ".BLACKBIRD_TABLE_PREFIX."relations WHERE table_parent = '$_POST[table_parent]' AND table_child = '$table'");
		$config = _ControllerFront::parseConfig($q_related['config']);
				
		for($i=0;$i<count($idSet);$i++){
			AdaptorMysql::update($table,array(array('field'=>$config['col_order'],'value'=>($i+1)) ),"id",$idSet[$i]);
		}
		
		//send signal back somewhere, why not
		if(isset($config['post_save'])){
			$file = CUSTOM . DS . 'plugins' . DS . 'image_browser.php';
			if(file_exists($file) && include_once$file){
				@eval($config['post_save']);
			}
		}
		
	}
	
	public function Deleteimage()
	{
		$this->layout_view = null;	
		
		$table = $_POST['table'];
		$id = $_POST['id'];
		
		//physically remove file
		
		//delete thumbs?		
		
		$q_related = AdaptorMysql::queryRow("SELECT * FROM ".BLACKBIRD_TABLE_PREFIX."relations WHERE table_parent = '$_POST[table_parent]' AND table_child = '$table'");
		$config = _ControllerFront::parseConfig($q_related['config']);
		
		//pull in record model
		$this->loadModel('Record');
		$m = new RecordModel();
		
		//TODO: improve this flow, store complete record to be deleted, delete, then send signal for processing
		//send signal back somewhere, why not
		if(isset($config['post_delete'])){
			$file = CUSTOM . DS . 'plugins' . DS . 'image_browser.php';
			if(file_exists($file) && include_once$file){
				@eval($config['post_delete']);
			}
		}
		
		//delete record
		$m->processDelete($table,explode(",",$id));
		
			
	}
	
	public function Getimage()
	{
		$this->layout_view = null;
		
		$id = $_POST['id'];
		$table = $_POST['table'];
		$table_parent = $_POST['table_parent'];
		$name_space = $_POST['name_space'];
		
		$q_relation = AdaptorMysql::queryRow("SELECT * FROM " . BLACKBIRD_TABLE_PREFIX . "relations WHERE table_parent = '$table_parent' AND table_child = '$table'");
		$config = _ControllerFront::parseConfig($q_relation['config']);
		
		$this->view(array('view'=>'_image','data'=>array(
			'id'=>$id,
			'table'=>$table,
			'config'=>$config,
			'name_space'=>$name_space)));
	}
	
}