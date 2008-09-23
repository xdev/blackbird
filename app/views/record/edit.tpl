<?php

//main divs

//tab nav

//main tab

//surrounding form markup
//dump contents
//closing form markup
//target iform

//related tabs
//iterate array
	//dump contents
	//make iform?

//other stuff

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

<div class="bb_module">
<div class="section main_projects" id="section_main">
<form id="form_main" name="form_main" enctype="multipart/form-data" action="<?= BASE ?>record/process" method="post" target="form_target_main" onsubmit="Element.show('ajax');" >
<?= $main ?>
</form>
<iframe id="form_target_main" name="form_target_main" class="related_iframe"></iframe>
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

				var sendVars = new Object();				
				sendVars.table = "%s";
				
				var tA = $("section_" + "%s").select(".table");
				var obj = tA[0];
				var myAjax = new Ajax.Updater(
					obj,
					"%s", 
					{
						method		: "post", 
						parameters	: formatPost(sendVars),
						evalScript	: true
					}
				);

			</script>
		
		</div>',
		$relation['table_child'],
		$relation['name_space'],
		BASE . 'table/datagrid'
		);
		
		print '</div>';
	
	}
	
}

?>
</div>