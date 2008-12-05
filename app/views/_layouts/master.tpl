<?= $this->fetchView('/_layouts/_header.tpl',$this->layout_data) ?>

	<body class="logged" id="body">
		
		<div id="bb_navigation">
			<?= $content['ui_nav'] ?>
		</div>		

		<div id="bb_session_nav">
			<?= $content['ui_breadcrumb'] ?>
			<?= $content['ui_session'] ?>
		</div>

		<div id="bb_main">
			<?= $content['main'] ?>
		</div>

	</body>

</html>
