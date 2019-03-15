<?PHP
    // CONFIGURE YOUR DATABASE SETTINGS HERE...
    $dbuser = '';
	$dbpass = '';
	$dbhost = '';
	$dbname = '';

    // SET THE POSSIBLE CHARACTERS YOUR SHORT URL CAN USE...
	$valid_chars = 'abcdefghjkmnpqrstuvwxyz23456789';
    
    // LENGTH OF YOUR SHORT URL SLUG...
    $slug_length = 5;

    // ##############################################
    // ##### NOTHING FURTHER TO CONFIGURE BELOW #####
    // ##############################################

	$s = empty($_SERVER['HTTPS']) ? '' : ($_SERVER['HTTPS'] == 'on') ? 's' : '';
	$base_url = "http$s://" . $_SERVER['HTTP_HOST'] . '/';
	
	$db = mysqli_connect($dbhost, $dbuser, $dbpass, $dbname) or die('database');

	if(isset($_GET['slug'])) {
		$escaped_slug = mysqli_real_escape_string($db, $_GET['slug']) or die('could not escape slug');
		$result = mysqli_query($db, "SELECT url FROM links WHERE slug = '$escaped_slug'");
		if($result === false || mysqli_num_rows($result) == 0) die('slug not found');

		mysqli_query($db, "UPDATE links SET visits = visits + 1, last_visited = NOW() WHERE slug = '$escaped_slug'") or die('could not update visits count');

		$row = mysqli_fetch_row($result);
		$url = $row[0];

		header('Location: ' . $url);

		exit;
	}

	$url = ltrim($_SERVER['REQUEST_URI'], '/');
	if(strlen($url) == 0) die('nothing to do');
	if(preg_match('!^[a-zA-Z0-9]+://.+!', $url) !== 1) die('invalid url');

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
