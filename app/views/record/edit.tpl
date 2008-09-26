<?php

print $this->fetchView('/_modules/ui_toolbar',array(
	'controller'=>'record',
	'mode'=>'edit',
	'table'=>$this->table,
	'id'=>$id,
	'active'=>$active,
	'name_space'=>$name_space,
	'type'=>'main'));

if(is_array($related)){

	$r = '<ul id="bb_main_sections">';
	$r .= '<li><a id="bb_toggle_main" class="active" href="#main">Main</a></li>';
	foreach ($related as $relation) {
		$r .= sprintf(
			'<li><a id="bb_toggle_%s" class="%s" href="#%s">%s</a></li>',
			$relation['name_space'],
			$mode == "insert" ? 'dim' : '',
			$relation['name_space'],
			$relation['label']
		);
	}
	$r .= '</ul>';

	print $r;

}

?>
<div id="bb_module_edit">

<div class="bb_module bb_module_edit">
<div class="section <?= $name_space ?>_<?= $table ?>" id="section_<?= $name_space ?>">
<form id="form_<?= $name_space ?>" name="form_<?= $name_space ?>" enctype="multipart/form-data" action="<?= BASE ?>record/process" method="post" target="form_target_<?= $name_space ?>" onsubmit="Element.show('ajax');" >
<?= $main ?>
</form>
<iframe id="form_target_<?= $name_space ?>" name="form_target_<?= $name_space ?>" class="related_iframe"></iframe>
</div>

<?php
if(is_array($related)){
	foreach($related as $relation){
		print '<div class="section" id="section_'.$relation['name_space'].'" style="display:none;">';
		//holder for edit
		print '<div class="edit_form" style="display:none;"><div class="detail"></div></div>';
		
		printf('
		<div class="table">
		<script type="text/javascript">
			<!-- <![CDATA[ 
			var data_grid_%s= new dataGrid(
				{
					mode: "related",
					table: "%s",
					table_parent: "%s",
					id_parent: "%s",
					name_space: "%s",
					cms_root: "%s",
					%s
				}
			);
			// ]]> -->
		</script>
		</div>',
		$relation['name_space'],
		$relation['table_child'],
		$relation['table_parent'],
		$this->id,
		$relation['name_space'],
		BASE,
		(isset($relation['sql_where'])) ? sprintf('sql_where: "%s"',$relation['sql_where']) : ''
		);
		
		/*
		CMS.broadcaster.addListener(data_grid_%s.listener);
		CMS.addCallback(\'' . $name_space . '\',data_grid_'.$name_space.',"getUpdate");
		*/
				
		print '</div>';
	
	}
	
}

?>
</div>
</div>