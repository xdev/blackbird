<?php
//Move to model & controller
$img = AdaptorMysql::queryRow("SELECT * FROM `" . $table . "` WHERE id = '$id'");		
($img['active'] == 0) ? $class = ' inactive' : $class = '';
$controller = 'ImageBrowser_' . $name_space;
?>
<li class="img_module<?= $class ?>" id="<?= $name_space ?>_img_<?= $img['id'] ?>" >
	<div class="handle">	
	<?php if(isset($config['col_file'])): ?>
	<img src="<?= $config['folder'] . $img[$config['col_file']] ?>?nc=<?php print rand(0,1000) ?>" alt="img" />
	<?php else: ?>
	<img src="<?= $config['folder'] . $config['file_prefix'] . $img['id'] ?>.jpg?nc=<?php print rand(0,1000) ?>" alt="img" />
	<?php endif ?>
	</div>
	
	<?php if(isset($config['col_label'])): ?>
		<?php if(isset($img[$config['col_label']])): ?>
			<span><?= $img[$config['col_label']]?></span>
		<?php endif ?>	
	<?php endif ?>
	
	<a href="#" onclick="<?= $controller ?>.deleteImg(<?= $img['id'] ?>); return false;" class="icon delete" >Delete</a>
	<a href="#" onclick="<?= $controller ?>.editImg(<?=  $img['id'] ?>); return false;" class="icon edit" >Edit</a>
</li>