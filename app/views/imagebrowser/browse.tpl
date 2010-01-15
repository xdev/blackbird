<?php 
//move to model sucka
$images = AdaptorMysql::query("SELECT * FROM $table WHERE $config[col_parent] = $id ORDER BY `$config[col_order]`");
?>

<div class="bb_toolbar related">
	<h1>Browsing <?= Utils::singulizer(_ControllerFront::getTableName($table)) ?></h1>
	<div class="bb_toolbar_actions">
		<input type="button" value="+ Add Record" onclick="blackbird.addNewRecord('<?= $table ?>','<?= $name_space ?>');" />
		<span class="total"><?= (($images) ? count($images) : '0') ?> Images</span>
	</div>
</div>

<div class="container">
	<ul id="<?= $name_space ?>_image_set" class="	image_browser">
		<?php if($images): ?>
		<?php foreach($images as $img): ?>
			<?= $this->fetchView('/imagebrowser/_image',array(
					'table'=>$table,
					'id'=>$img['id'],
					'name_space'=>$name_space,
					'config'=>$config)
			) ?>			
		<?php endforeach ?>
		<?php endif ?>
	</ul>
</div>

<script type="text/javascript">
	<!-- <![CDATA[ 
	$(document).observe('dom:loaded',function(){
		//need to pass in serialized config object here
		<?php 
		$tA = _ControllerFront::getRoute();
		$config['name_space'] = $name_space;
		$config['table'] = $table;
		$config['base'] = BASE;
		$config['table_parent'] = $tA['table']; 
		$config['id_parent'] = $tA['id'];
		?>
				
		ImageBrowser_<?= $name_space ?> = new ImageBrowser(<?= json_encode($config) ?>);
		blackbird.broadcaster.addListener(ImageBrowser_<?= $name_space ?>);
	});
	// ]]> -->
</script>