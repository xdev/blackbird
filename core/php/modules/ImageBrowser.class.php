<?php

class ImageBrowser
{
		
	private $_data;	
	
	public function __construct($cms)
	{
		if(!isset($this->config['col_order'])){
			$this->config['col_order'] = 'position';
		}
		$this->db = $cms->db;
	}
	
	public function __set($name,$value)
	{
		$this->_data[$name] = $value;
	}
	
	public function __get($name)
	{
		if(isset($this->_data[$name])){
			return $this->_data[$name];
		}else{
			return false;
		}		
	}
	
		
	function sort_order()
	{		
		$idSet = explode(",",$_POST['id_set']);
		
		for($i=0;$i<count($idSet);$i++){
			$this->db->update($this->table,array(array('field'=>$this->config['col_order'],'value'=>($i+1)) ),"id",$idSet[$i]);
		}
		
		print 'order updated';
	}
	
	function _getImgDetail()
	{		
		$id = Utils::setVar('id');
		$this->getImgDetail($id);
	}
	
	function getImgDetail($id)
	{	
		$controller = 'ImageBrowser_' . $this->name_space;
		$img = $this->db->queryRow("SELECT * FROM `" . $this->table . "` WHERE id = '$id'");		
		($img['active'] == 0) ? $class = ' inactive' : $class = '';
		print '
		<li class="img_module'.$class.'" id="' . $this->name_space . '_img_' . $img['id'] . '" >
			<div class="img">
			<img src="'. SERVER . $this->config['folder'] . $this->config['file_prefix'] . $img['id'] .'.jpg?nc=' . rand(1000) . '" class="handle" alt="img" />
			</div>';
			
			if(isset($this->config['col_label'])){
				if(isset($img[$this->config['col_label']])){
					print '<span>' . $img[$this->config['col_label']] .'</span>';
				}
			
			}
			
			print '
			<a href="#" onclick="' . $controller . '.deleteImg('.$img['id'].')" class="icon delete" >Delete</a>
			<a href="#" onclick="' . $controller . '.editImg(' .  $img['id'] . ');" class="icon edit" >Edit</a>
		</li>';	
		
	}
	
	
	function get_updated_img()
	{
		$this->process_ajax();
	}
	
	
		

	function build()
	{	
	
		print '
		<div class="edit_form" style="display:none;">
			<div class="detail"></div>
		</div>';
		
		$controller = 'ImageBrowser_' . $this->name_space;
		
		print '<script type="text/javascript">
		var ' . $controller . ' = new ImageBrowser({name_space : \''. $this->name_space .'\',table : \'' . $this->table . '\',cms_root : \'' . CMS_ROOT . '\'});
		</script>';
		
		$p_id = $this->cms->id;
		$q = $this->db->query("SELECT * FROM " . $this->table . " WHERE parent_id = '$p_id' ORDER BY position");
		if($q){
			$rT = count($q);
		}else{
			$rT = 0;
		}	
		print'
		<div class="actions">
			<div class="left">
				<a class="icon new" href="#" onclick="CMS.addNewRecord(\'' . $this->table . '\',\'' . $this->name_space . '\')" ></a>
			</div>
			<div class="right">
				' . $rT . ' Image(s)
			</div>
			<div style="clear:both;"></div>
		</div>';
			
		
		
		
		print '<ul id="'.$this->name_space . '_image_set" class="image_browser">';
		if($q){
			foreach($q as $img){
				$this->getImgDetail($img['id']);			
			}
		}
		
		print '</ul><div style="clear:both"></div>';
		
		
		
		//add onUpdate to process the new order of items
		
		print '
		<script type="text/javascript">
			Sortable.create(
				"' . $this->name_space . '_image_set",
				{
					overlap:"horizontal",
					constraint:false,
					handle:"handle",
				
					onUpdate:function(){
						' . $controller . '.onOrderChange();
					}
					
				}
			);			
			CMS.addCallback(\'' . $this->name_space . '\','. $controller . ',"onRemoteComplete");
		</script>';
	
	
	}
	

}
?>