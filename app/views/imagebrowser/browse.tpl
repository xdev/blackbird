<?php 
//move to model sucka
$images = AdaptorMysql::query("SELECT * FROM $table WHERE $config[col_parent] = $id ORDER BY position");
?>

<div class="bb_toolbar related">
	<h1>Browse <?= $table ?></h1>
	<div class="bb_toolbar_actions">
		<input type="button" value="+ New Record" onclick="blackbird.addNewRecord('<?= $table ?>','<?= $name_space ?>');" />
		<span class="total"><?= (count($images)) ?> Images</span>
	</div>
</div>

<ul id="<?= $name_space ?>_image_set" class="container image_browser">
<?php foreach($images as $img): ?>
	<?= $this->fetchView('/imagebrowser/_image',array(
			'table'=>$table,
			'id'=>$img['id'],
			'name_space'=>$name_space,
			'config'=>$config)
	) ?>			
<?php endforeach ?>
</ul><div style="clear:both"></div>

<script type="text/javascript">
	<!-- <![CDATA[ 
	document.observe('dom:loaded',function(){
		ImageBrowser_<?= $name_space ?> = new ImageBrowser({name_space:"<?= $name_space ?>",table:"<?= $table ?>",base:"<?= BASE ?>"});
		blackbird.broadcaster.addListener(ImageBrowser_<?= $name_space ?>);
	});
	// ]]> -->
</script>