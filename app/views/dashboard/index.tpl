<div class="bb_toolbar">
	<h1>Dashboard: <?= BLACKBIRD_CLIENT ?></h1>
</div>

<div id="bb_sections">
	<div class="section">
		<div id="dashboard" class="container">
			<?= $this->fetchView('_users',array('data'=>$users)) ?>
			<?= $this->fetchView('_chart_user_edits',array('percents'=>$chart_users['percents'],'labels'=>$chart_users['labels'])) ?>
			<?= $this->fetchView('_activity',array('data'=>$activity)) ?>
			<?= $this->fetchView('_chart_edits',array('percents'=>$chart_edits['percents'],'labels'=>$chart_edits['labels'])) ?>
			<?= $this->fetchView('_tables',array('data'=>$tables)) ?>
			<?= $this->fetchView('_chart_tables',array('percents'=>$chart_tables['percents'],'labels'=>$chart_tables['labels'])) ?>
		
			<script type="text/javascript">
				/*
				onrelease, send to a cookie that remembers the state and position of stuffs
				*/
				Sortable.create(
					$('dashboard'),
					{
						overlap		: "horizontal",
						tag			: 'div',
						constraint	: false,
						handle		: "titlebar"
				    }
				);
			</script>
			
		</div>
	</div>
</div>