<?php

printf('
<div class="section" id="section_%s">
<div class="table">
<script type="text/javascript">
	<!-- <![CDATA[ 
	var data_grid_%s= new dataGrid(
		{
			table: "%s",
			name_space: "%s",
			cms_root: "%s"
		}
	);
	// ]]> -->
</script>
</div>
</div>',
$name_space,
$name_space,
$this->route['table'],
$name_space,
BASE);

?>
