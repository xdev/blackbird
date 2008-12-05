<?= $this->fetchView('/_layouts/_header.tpl',$this->layout_data) ?>

	<body id="body">
		
		<div id="bb_session_nav"></div>

		<div id="bb_main">
			<div class="bb_toolbar"></div>			
		</div>
		
		<div id="lightbox"><div class="wrapper"><div class="dialog"><?= $content['main'] ?></div></div></div>		
		
	</body>

</html>
