<?php
if($controller == 'record'){
print '	
<div class="bb_toolbar main">
	<h1>';
	printf(($mode == 'edit') ? 'Edit Record' : 'New Record');
print '</h1>';

	if($mode == 'edit'){
		printf('
		<dl class="bb_record_meta">
			<dt>Id</dt>
			<dd>%s</dd>
			%s
			%s
		</dl>',
		$id,
		(isset($created)) ? '<dt>Created</dt><dd>'.$created.'</dd>' : '',
		(isset($modified)) ? '<dt>Modified</dt><dd>'.$modified.'</dd>' : ''
		);
	}
	
	print '
	<div class="bb_record_actions">
		<!-- set up change handler to set main_active to this value upon change.. ehh -->
		<select onchange="CMS.setActive(this,\'main\');">
			<option value="1" selected="selected">Active</option>
			<option value="0" >Inactive</option>
		</select>
		<input type="button" value="Save" onclick="$(\'form_main\').submit(); return false;" />
		<!-- CMS.loopBack(\'main\') -->
		<input type="button" value="Close" onclick="window.location=\'' . BASE . 'table/browse/' . $table . '\'; return false;" />
	</div>
</div>';
}
if($controller == 'table'){
	print '
<div class="bb_toolbar main">
	<h1>' . $table . '</h1>
	
	<div class="bb_record_actions">
		<input type="button" value="+ New Record" onclick="window.location=\''. BASE . 'record/add/' . $table . '\'" />
	</div>
</div>';
}