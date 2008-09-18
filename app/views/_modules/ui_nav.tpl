<ul id="bb_main_nav_tables">
	<?php	
	foreach($tableA as $section){
		print '
		<li>
			<span>'.$section['name'].'</span>
			<ul>';
		
		foreach($section['tables'] as $table){
			print '<li><a href="/blackbird/browse/'.$table.'">'.$table.'</a></li>';
		}
		print '
		</ul>
		</li>';
	}
	?>
</ul>
<ul id="bb_main_nav_actions">
	<li id="bb_nav_action_expand">Expand</li>
	<li id="bb_nav_action_collapse">Collapse</li>
</ul>