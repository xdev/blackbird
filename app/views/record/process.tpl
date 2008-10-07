<script type="text/javascript">
	parent.blackbird.broadcaster.broadcastMessage("onRemoteComplete",
		{
			name_space : "<?= $name_space ?>",
			mode : "<?= $mode ?>",
			query_action : "<?= $query_action ?>",
			channel : "<?= $channel ?>",
			id : "<?= $id ?>",
			table : "<?= $table ?>"
		}
	);
</script>