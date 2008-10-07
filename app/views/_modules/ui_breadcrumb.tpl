<?php if(_ControllerFront::$session->logged === true): ?>
<ol id="bb_breadcrumb">
	<li><a href="<?= BASE ?>">Dashboard</a></li>
	<?php if($this->route['controller'] == 'table'): ?>
	<li><?= Utils::titleCase(str_replace('_',' ',$tablename)) ?></li>	
	<?php endif ?>
	<?php if($this->route['controller'] == 'record'): ?>
	<li><a href="<?= BASE ?>table/browse/<?= $table ?>"><?= Utils::titleCase(str_replace('_',' ',$tablename)) ?></a></li>
		<?php if($this->route['action'] == 'edit'): ?>
		<li>Edit Record</li>
		<?php else: ?>
		<li>Add Record</li>
		<?php endif ?>
	<?php endif ?>
</ol>
<?php endif ?>