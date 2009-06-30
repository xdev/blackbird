<div class="bb_dash">
	<div class="titlebar">
		<h2>All Edits by User</h2>
	</div>
	<?php if($percents != '' && $labels != ''): ?>
	<div class="content" style="background: #FFFFFF url('http://chart.apis.google.com/chart?cht=p&amp;chs=420x200&amp;chd=t:<?= $percents ?>&amp;chl=<?= $labels ?>') 50% 50% no-repeat;"></div>
	<?php else: ?>
	<div class="content">
		<p class="message">There are no edits yet…</p>
	</div>
	<?php endif ?>
</div>
