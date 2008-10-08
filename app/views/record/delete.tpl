<div class="titlebar">
	<p id="closeLightbox">Close</p>
	<h1>Delete Record</h1>
</div>
<div class="content">
	<p>Really do it.</p>
	<input type="button" value="Delete" onclick="blackbird.destroyRecord('<?= $table ?>','<?= $id ?>');" />
	<input type="button" value="Cancel" onclick="blackbird.closeLightbox();" />
</div>