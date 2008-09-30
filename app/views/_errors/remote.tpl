<script type="text/javascript">
	parent.blackbird.broadcaster.broadcastMessage("onRemoteErrors",
		{ 
			
			name_space	: "<?= $name_space ?>",
			mode		: "<?= $mode ?>",
			id			: "<?= $id ?>",
			errors		: [
			
			<?php						
			for($i=0;$i<count($errors);$i++){
				$error = $errors[$i];
				print "['$error[field]','$error[error]']";
				if($i < count($errors) - 1){
					print ',';
				}
			}
			?>
									
			]		
		}
	)
</script>