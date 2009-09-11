<?php
//Move to model & controller
$img = AdaptorMysql::queryRow("SELECT * FROM `" . $table . "` WHERE id = '$id'");		
//($img['active'] == 0) ? $class = ' inactive' : $class = '';
$class = '';
$controller = 'ImageBrowser_' . $name_space;

$fkid = null;
if(isset($config['fk_table'])){
	$q_image = AdaptorMysql::queryRow("SELECT * FROM `$config[fk_table]` WHERE `$config[fk_pk]` = '".$img[$config['fk_col']]."'");
	$label = $q_image['image'];
	$fkid = $q_image['id'];
}else{
	
	/*
		<?php if(isset($img[$config['col_label']])): ?>
			<span><?= $img[$config['col_label']]?></span>
		<?php endif ?>	
	*/
}

?>


<li class="img_module<?= $class ?>" id="<?= $name_space ?>_img_<?= $img['id'] ?>" <?= isset($fkid) ? 'rel="fkid_'.$fkid.'"' : '' ?>>
	<div class="handle">
		<img src="<?= SITE_URL ?>photos/<?= substr($q_image['sha1'],0,16) ?>_w140_h140.jpg" alt="img" />
	</div>
	
	<span><?= $label ?></span>

	<a href="#" onclick="<?= $controller ?>.editImg(<?=  $img['id'] ?>); return false;" class="icon edit" >Edit</a>
	<a href="#" onclick="<?= $controller ?>.deleteImg(<?= $img['id'] ?>); return false;" class="icon delete" >Delete</a>
</li>