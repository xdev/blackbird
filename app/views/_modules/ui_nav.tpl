<ul id="bb_main_nav_tables">
	<?php foreach($tableA as $section): ?>
	<li>
		<span><?= $section['name'] ?></span>
		<ul>
			<?php foreach($section['tables'] as $table): ?>
				<li><a class="<?= (isset($this->route[3]) && $table == $this->route[3] ? 'selected' : '') ?>" href="<?= BASE ?>table/browse/<?= $table ?>"><?= _ControllerFront::getTableName($table) ?></a></li>
			<?php endforeach ?>
		</ul>
	</li>
	<?php endforeach ?>
</ul>
<ul id="bb_main_nav_actions">
	<li id="bb_nav_action_expand">Expand</li>
	<li id="bb_nav_action_collapse">Collapse</li>
</ul>