<?php

class TableController extends _Controller
{
	
	public function Index()
	{
		
	}
	
	//same as index
	public function Browse()
	{
		
		//view datagrid					
		$this->view(array('data'=>array('name_space'=>'main')));
		
	}
	
	public function Datagrid()
	{
		$this->layout_view = null;
		$table = $_POST['table'];
		
		//$data = array();
		//$data['table'] = $table;
		$tA = $this->model->getData($table);
		//$data['rowData'] = $tA['rowData'];
		//$data['headerData'] = $tA['headerData'];
		
					
		$this->view(array('data'=>$tA));
	}
	
}