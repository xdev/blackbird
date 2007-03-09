<?php

class Home
{
	
	private $cms;
	
	function __construct($cms)
	{
		$this->cms = $cms;
		$this->buildPage();
	}
	
	function modTables()
	{
		$tables = $this->cms->session->getTables('navigation');
		print '
		<div class="module">
			<h3>Database Tables</h3>
			<table class="data_grid">
				<thead>
					<tr>
					<th>Table</th>
					<th># of Records</th>
					<th>Last Updated</th>
					</tr>
				</thead>
				<tbody class="records">';					
		$rc = 0;
		foreach($tables as $key => $row){
			($rc%2==0) ? $class='' : $class='class="odd"';
			print "<tr $class>";
			
			$click = 'onclick="window.location = \'' . CMS_ROOT . 'browse/' . $key . '\';"';
			
			print "<td $click>" . $key . '</td>';
			
			$q_m = Db::query("SELECT id FROM $key");
			
			print "<td $click>" . count($q_m) . '</td>';
			
			$q_last = Db::queryRow("SELECT * FROM cms_history WHERE table_name = '$key' ORDER BY modtime LIMIT 1");
			$deets = Db::queryRow("SELECT firstname,lastname,email FROM cms_users WHERE id = '$q_last[user_id]'");
									
			print "<td $click>". $deets['lastname'] . ', ' . $deets['firstname'] . ' ' . $q_last['modtime'] . '</td>';
			print '</tr>';
			
			$rc++;
			
		}
		
		print '</tbody>
		</table>
		</div>';
	
	}
	
	function modSessions()
	{
		print '<div class="module">
		<h3>Recent Sessions</h3>
		<table class="data_grid">
			<thead>
				<tr>
				<th>User</th>
				<th>Start</th>
				<th>End</th>
				<th>Duration</th>
				<th>Content Edits</th>
				</tr>
			</thead>
			<tbody class="records">';
		
		$q = Db::query("SELECT * FROM cms_sessions ORDER BY start_time DESC LIMIT 5");
		$rc = 0;
		
		foreach($q as $row){
			$classA = Array();
			($rc%2==0) ? '' : $classA[] = 'odd';
			$click = 'onclick="window.location = \'' . CMS_ROOT . 'browse/cms_history/?sort_col=id%20DESC&amp;sort_max=10000&amp;filter_session_id=' . $row['session_id'] . '\';"';
			if($q_edits = Db::query("SELECT * FROM cms_history WHERE session_id = '$row[session_id]'")){
				$e = count($q_edits);
			}else{
				$e = '';
				$click = '';
				$classA[] = 'locked';
				
			}
			
			$class = 'class="' . join(" ",$classA) . '"';
			
			print "<tr $class>";
			
			
			
			$deets = Db::queryRow("SELECT firstname,lastname,email FROM cms_users WHERE id = '$row[user_id]'");
			print "<td $click>" . $deets['lastname'] . ', ' . $deets['firstname'] . '</td>';
			print "<td $click>" . $row['start_time'] . '</td>';
			
			if($row['end_time'] == '0000-00-00 00:00:00'){
				$et = '~';
				$diff = Utils::getTimeDifference($row['start_time'],Utils::now());
			}else{
				$et = $row['end_time'];
				$diff = Utils::getTimeDifference($row['start_time'],$row['end_time']);
			}
			
			print "<td $click>" . $et . '</td>';
			print "<td $click>" . $diff['hours'] . ':' . $diff['minutes'] . '</td>';
			
			
			
			print "<td $click>" . $e . '</td>';			
			print '</tr>';
			$rc++;
		
		}
				
		
		print '
			</tbody>
		</table>
		</div>';
	}
	
	function modEdits()
	{
		print '
		<div class="module">
			<h3>Recent Content Edits</h3>
			<table class="data_grid">
				<thead>
					<tr>
					<th>Table</th>
					<th>Id</th>
					<th>Action</th>
					<th>User</th>
					<th>Time</th>
					</tr>
				</thead>
				<tbody class="records">';
				
		$q = Db::query("SELECT * FROM cms_history ORDER BY modtime DESC LIMIT 5");
		$rc = 0;
		foreach($q as $row){
			($rc%2==0) ? $class='' : $class='class="odd"';
			$click = 'onclick="window.location = \'' . CMS_ROOT . 'edit/cms_history/' . $row['id'] . '\';"';
			$deets = Db::queryRow("SELECT firstname,lastname,email FROM cms_users WHERE id = '$row[user_id]'");
			print "<tr $class>";
			print "<td $click>" . $row['table_name'] . '</td>';
			print "<td $click>" . $row['record_id'] . '</td>';
			print "<td $click>" . $row['action'] . '</td>';
			print "<td $click>" . $deets['lastname'] . ', ' . $deets['firstname'] . '</td>';
			print "<td $click>" . $row['modtime'] . '</td>';			
			print '</tr>';
			
			$rc++;
		}
		
		print '</tbody>
		</table>
		</div>';
	
	}
	
	function modRss()
	{
		if(defined('CMS_NEWS_FEED')){
		if(CMS_NEWS_FEED != ''){
		print '
		<div class="module">
			<h3>Client News</h3>';
			
		require_once LIB.'rss/magpierss-0.72/rss_fetch.inc';

		$rss = fetch_rss(CMS_NEWS_FEED);
		
		//echo "Site: ", $rss->channel['title'], "<br />";
		
		foreach ($rss->items as $item ) {
			print '<div class="rssitem">';
						
			if(isset($item['link'])){
				print "<h4><a href=\"$item[link]\">$item[title]</a></h4>";
			}else{
				print "<h4>$item[title]</h4>";
			}
			
			print "<p>$item[description]</p></div>";
		}	
		
		print '</div>';
		}
		}
		
	}
	
	function modDocs()
	{
	
		print '		
		<div class="module">
			<h3>Documentation</h3>
			<p>This is a section that has quick links to view embedded CMS documentation. Good for training new members.</p>
		</div>';
	
	}
	
	function buildPage()
	{
		
		$this->cms->label = "Home";
		$this->cms->buildHeader();
		$this->cms->buildHome($this);
		$this->cms->buildFooter();
	
	}




}


?>