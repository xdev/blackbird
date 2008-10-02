<?php

class ImagebrowserController extends _Controller
{
	public function Sort()
	{
		
		$table = $_POST['table'];
		$idSet = explode(",",$_POST['id_set']);
		
		//get relation sucka
		$q_related = AdaptorMysql::queryRow("SELECT * FROM ".BLACKBIRD_TABLE_PREFIX."relations WHERE table_parent = '$_POST[table_parent]' AND table_child = '$table' ORDER BY position");
		$config = _ControllerFront::parseConfig($q_related['config']);
				
		for($i=0;$i<count($idSet);$i++){
			AdaptorMysql::update($table,array(array('field'=>$config['col_order'],'value'=>($i+1)) ),"id",$idSet[$i]);
		}
		
		$this->layout_view = null;
	}
	
	public function Deleteimage()
	{
		//physically remove file
		
		//delete record
		
		//delete thumbs?
	}
	
	
	public function Getimage()
	{
		$this->layout_view = null;
		$this->view('view'=>'_image','data'=>array('id'=>$_POST['id']));
	}
}