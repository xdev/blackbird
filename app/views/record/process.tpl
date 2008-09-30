<script type="text/javascript">
	parent.blackbird.broadcaster.broadcastMessage("onRemoteComplete",
		{
			name_space : "<?= $name_space ?>",
			mode : "<?= $mode ?>",
			channel : "<?= $channel ?>",
			id : "<?= $id ?>"
		}
	);
</script>