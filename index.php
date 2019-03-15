<?PHP
    // CONFIGURE YOUR DATABASE SETTINGS HERE...
	$dbhost = '';
    $dbuser = '';
	$dbpass = '';
	$dbname = '';

    // SET THE POSSIBLE CHARACTERS YOUR SHORT URL CAN USE...
	$valid_chars = 'abcdefghjkmnpqrstuvwxyz23456789';
    
    // LENGTH OF YOUR SHORT URL SLUG...
    $slug_length = 5;
	
	// A PASSWORD TO ACCESS LINK ACTIVITY STATISTICS (LEAVE BLANK TO ALLOW ANYONE TO ACCESS)
	$pw_stats = '';
	
	// A PASSWORD TO ALLOW CREATING NEW LINKS (LEAVE BLANK TO ALLOW ANYONE TO ACCESS)
	$pw_create = '';

    // ##############################################
    // ##### NOTHING FURTHER TO CONFIGURE BELOW #####
    // ##############################################

	$s = empty($_SERVER['HTTPS']) ? '' : ($_SERVER['HTTPS'] == 'on') ? 's' : '';
	$base_url = "http$s://" . $_SERVER['HTTP_HOST'] . '/';
	
	$db = mysqli_connect($dbhost, $dbuser, $dbpass, $dbname) or die('database');

	if($_SERVER['REQUEST_URI'] == '/') { doNothing(); exit; }
	if($pw_stats == '' && preg_match('!^/stats(/?.*)$!', $_SERVER['REQUEST_URI']) == 1) { showStats(); exit; }
	if($pw_stats != '' && preg_match('!^/stats/' . preg_quote($pw_stats, '!') . '/?$!', $_SERVER['REQUEST_URI']) == 1) { showStats(); exit; }
	if(preg_match("!^/[$valid_chars]+/?$!", $_SERVER['REQUEST_URI']) == 1) { processSlug(); exit; }
	if($pw_stats == '' && preg_match("!^/[$valid_chars]+/stats/?$!", $_SERVER['REQUEST_URI']) == 1) { slugStats(); exit; }
	if($pw_stats != '' && preg_match("!^/[$valid_chars]+/stats/" . preg_quote($pw_stats, '!') . "/?$!", $_SERVER['REQUEST_URI']) == 1) { slugStats(); exit; }
	if($pw_create == '' && preg_match("!^/[a-z0-9]+://.+$!", $_SERVER['REQUEST_URI']) == 1) { createLink(); exit; }
	if($pw_create != '' && preg_match("!^/" . preg_quote($pw_create, '!') . "/[a-z0-9]+://.+$!", $_SERVER['REQUEST_URI']) == 1) { createLink(); exit; }

	exit;

	function doNothing() {
		die('nothing to do');
	}
	
	function processSlug() {
		global $db;

		$slug = trim($_SERVER['REQUEST_URI'], '/');

		$escaped_slug = mysqli_real_escape_string($db, $slug);
		$result = mysqli_query($db, "SELECT url FROM links WHERE slug = '$escaped_slug'");
		if($result === false || mysqli_num_rows($result) == 0) die('slug not found');

		mysqli_query($db, "UPDATE links SET visits = visits + 1, last_visited = NOW() WHERE slug = '$escaped_slug'") or die('could not update visits count');

		$escaped_referrer = mysqli_real_escape_string($db, $_SERVER['HTTP_REFERER']);
		mysqli_query($db, "INSERT INTO visits (slug, visit_date, referrer) VALUES ('$escaped_slug', NOW(), '$escaped_referrer')");

		$row = mysqli_fetch_row($result);
		$url = $row[0];

		header('Location: ' . $url);
	}
	
	function slugStats() {
		global $db, $pw_stats, $valid_chars;

		$slug = trim($_SERVER['REQUEST_URI'], '/');
		$slug = preg_replace("!^([$valid_chars]+)/stats.*$!", '$1', $slug);

		$escaped_slug = mysqli_real_escape_string($db, $slug);
		$result = mysqli_query($db, "SELECT * FROM links WHERE slug = '$escaped_slug'");
		if($result === false || mysqli_num_rows($result) == 0) die('slug not found');
		
		$data = array();
		$data['info'] = mysqli_fetch_array($result, MYSQLI_ASSOC);
		$data['visits'] = array();

		$visits = mysqli_query($db, "SELECT * FROM visits WHERE slug = '$escaped_slug'");
		while($visit = mysqli_fetch_array($visits, MYSQLI_ASSOC)) {
			$referrer = $visit['referrer'];
			if(strlen($referrer) > 0) {
				$url_info = parse_url($referrer);
				$scheme = $url_info['scheme'];
				$host = $url_info['host'];
				$domain = $scheme . '://' . $host;
				if(is_null($reffering_domains[$domain])) {
					$reffering_domains[$domain] = 1;
				} else {
					$reffering_domains[$domain] = $reffering_domains[$domain] + 1;
				}
			}
			$data['visits'][] = $visit;
		}
		array_multisort($reffering_domains, SORT_DESC);
		$data['referers'] = $reffering_domains;
		
		echo "<h1>Stats for <a href='" . $data['info']['url'] . "'>" . $data['info']['url'] . "</a></h1>";
		echo "<ul>";
		echo "<li><strong>Created:</strong> " . $data['info']['created'] . "</li>";
		echo "<li><strong>Visits:</strong> " . $data['info']['visits'] . "</li>";
		echo "</ul>";

		echo "<h2>Top Referers</h2>";
		echo "<table><thead><td><strong>Domain</strong></td><td><strong>Visits</strong></td></thead><tbody>";
		foreach($data['referers'] as $domain => $visits) {
			echo "<tr>";
			echo "<td><a href='$domain'>" . $domain . "</a></td>";
			echo "<td>" . $visits . "</td>";
			echo "</tr>";
		}
		echo "</tbody></table>";
		
		echo "<h2>Recent Visits</h2>";
		echo "<table><thead><td><strong>Date</strong></td><td><strong>Referer</strong></td></thead><tbody>";
		foreach($data['visits'] as $visit) {
			echo "<tr>";
			echo "<td>" . $visit['visit_date'] . "</td>";
			echo "<td><a href='{$visit['referrer']}'>{$visit['referrer']}</a></td>";
			echo "</tr>";
		}
		echo "</tbody></table>";
	}
	
	function createLink() {
		global $base_url, $valid_chars, $slug_length, $db, $pw_create;

		$url = ltrim($_SERVER['REQUEST_URI'], '/');
		
		if($pw_create != '') {
			$url = preg_replace('!^'. preg_quote($pw_create, '!') . '/(.+)$!', '$1', $url);
		}

		do {
			$possible_slug = '';
			for($i = 0; $i < $slug_length; $i++) {
				$possible_slug .= $valid_chars[rand(0, strlen($valid_chars) - 1)];
			}
			$result = mysqli_query($db, "SELECT COUNT(*) FROM links WHERE slug = '$possible_slug'") or die('could not generate new slug');
			$row = mysqli_fetch_row($result);
			$count = $row[0];
			if($count == 0) {
				$slug = $possible_slug;
			}
		} while(is_null($slug));

		$escaped_url = mysqli_real_escape_string($db, $url) or die('could not escape url');

		$result = mysqli_query($db, "INSERT INTO links (slug, url, visits, created) VALUES ('$slug', '$escaped_url', 0, NOW())") or die('could not insert new link');
		if($result == false) die('insert query failed');

		echo $base_url . $slug;
	}

	function showStats() {
		global $db, $base_url, $pw_stats;
		
		$data = array();
		
		$result = mysqli_query($db, 'SELECT * FROM links ORDER BY created DESC LIMIT 50');
		while($link = mysqli_fetch_array($result, MYSQLI_ASSOC)) {
			$reffering_domains =array();
			$visits = mysqli_query($db, "SELECT * FROM visits WHERE slug = '{$link['slug']}'");
			while($visit = mysqli_fetch_array($visits, MYSQLI_ASSOC)) {
				$referrer = $visit['referrer'];
				if(strlen($referrer) > 0) {
					$url_info = parse_url($referrer);
					$scheme = $url_info['scheme'];
					$host = $url_info['host'];
					$domain = $scheme . '://' . $host;
					if(is_null($reffering_domains[$domain])) {
						$reffering_domains[$domain] = 1;
					} else {
						$reffering_domains[$domain] = $reffering_domains[$domain] + 1;
					}
				}
			}
			array_multisort($reffering_domains, SORT_DESC);
			if(count($reffering_domains) > 0) {
				$key = key($reffering_domains);
				$link['top_referer'] = array('domain' => $key, 'visits' => $reffering_domains[$key]);
			}
			$data[] = $link;
		}
		
		echo "<h1>Recent Clicks</h1>";
		echo "<table><thead><td><strong>Created</strong></td><td><strong>Slug</strong></td><td><strong>URL</strong></td><td><strong>Visits</strong></td><td><strong>Last Visit</strong></td><td><strong>Top Referer</strong></td><td></td></thead>";
		echo "<tbody>";
		foreach($data as $link) {
			$short_url = $base_url . $link['slug'];
			echo "<tr>";
			echo "<td>{$link['created']}</td>";
			echo "<td><a href='$short_url'>{$link['slug']}</a></td>";
			echo "<td><a href='{$link['url']}'>{$link['url']}</a></td>";
			echo "<td>{$link['visits']}</td>";
			echo "<td>{$link['last_visited']}</td>";
			if(is_null($link['top_referer'])) {
				echo "<td></td>";
			} else {
				$s = $link['top_referer']['visits'] == 1 ? '' : 's';
				echo "<td><a href='" . $link['top_referer']['domain'] . "'>" . $link['top_referer']['domain'] . "</a> (" . $link['top_referer']['visits'] . " visit$s)</td>";
			}
			echo "<td><a href='/{$link['slug']}/stats/$pw_stats'>View Stats</a></td>";
			echo "</tr>";
		}
		echo "</tbody>";
		echo "</table>";
	}
