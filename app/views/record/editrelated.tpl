<div class="bb_toolbar related">
	<h1>Related Edit Toolbar</h1>
	<div class="bb_toolbar_actions
">
		<!-- set up change handler to set main_active to this value upon change.. ehh 
		CMS.setActive(this,'main');	
		-->		
		<select onchange="">
			<option value="1" selected="selected">Active</option>
			<option value="0" >Inactive</option>
		</select>
		<input type="button" value="Save" onclick="$('form_<?= $name_space ?>').submit(); return false;" />
		<!-- CMS.loopBack(\'main\') -->
		<input type="button" value="Close" onclick="blackbird.closeRecord('<?= $name_space ?>'); return false;" />
	</div>
</div>
<form id="form_<?= $name_space ?>" name="form_<?= $name_space ?>" enctype="multipart/form-data" action="<?= BASE ?>record/process" method="post" target="form_target_<?= $name_space ?>" onsubmit="Element.show('ajax');" >
<?= $main ?>
</form>
<iframe id="form_target_<?= $name_space ?>" name="form_target_<?= $name_space ?>" class="related_iframe"></iframe>