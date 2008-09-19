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
		$relation['table_child'],
		$mode == "insert" ? 'dim' : '',
		$relation['table_child'],
		$relation['label']
	);
}
$r .= '</ul>';

print $r;

}

?>

<form id="form_main" name="form_main" enctype="multipart/form-data" action="<?= BASE ?>record/process" method="post" target="form_target_main" onsubmit="Element.show('ajax');" >
<?= $main ?>
</form>
<iframe id="form_target_main" name="form_target_main" class="related_iframe"></iframe>