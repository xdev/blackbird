<div id="bb_toolbar">
	<h1>Edit Record</h1>
	<dl id="bb_record_meta">
		<dt>Id</dt>
		<dd>4</dd>
		<dt>Created</dt>
		<dd>2008-04-25 10:16:48</dd>
		<dt>Modified</dt>
		<dd>2008-04-25 10:16:48</dd>
	</dl>

	<div id="bb_record_actions">
		<!-- set up change handler to set main_active to this value upon change.. ehh -->
		<select onchange="CMS.setActive(this,'main');">
			<option value="1" selected="selected">Active</option>
			<option value="0" >Inactive</option>
		</select>
		<input type="button" value="Save" onclick="CMS.loopBack('main'); return false;" />
		<input type="button" value="Close" onclick="window.location=''; return false;" />
	</div>
</div>

<!--
<div id="bb_toolbar">
	<h1>Product features</h1>
	
	<div id="bb_record_actions">
		<input type="button" value="+ New Record" onclick="window.location='/cms/add/product_features'" />
	</div>
</div>
-->