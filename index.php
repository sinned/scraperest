<?php
	require "scraperest-config.php";
	require "lib/ez_sql.php";
	require "lib/pins.lib.php";
	
	// get the pins
	$sql = "SELECT data_id, url, img_src, description, user_url, user_img_src, user_fullname, pinboard_url, pinboard_name, likes_count, comments_count, repins_count, from_url, last_updated FROM pins ORDER BY last_updated DESC LIMIT 500";
	$pins = $db->get_results($sql);
	
	// get total pincount
	$sql = "SELECT count(*) FROM pins";
	$pincount = $db->get_var($sql);

?><!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>scraperest.</title>
<link href="http://twishlist.com/styles/reset-fonts-grids.css" rel="styleSheet" type="text/css" />
<link href="http://twishlist.com/styles/twishlist.css" rel="styleSheet" type="text/css" />

<body>
	<div>
	<h1>Scraperest (<?php echo number_format($pincount); ?>)</h1>
<?php
	foreach ($pins as $pin) {
		echo "<a href='http://pinterest.com" . $pin->url . "' title='" . substr($pin->description,0,50) . " " . $pin->last_updated . "' ><img height='100' src='" . $pin->img_src . "' alt ='" . substr($pin->description,0,50) . "' /></a> ";
		//echo substr($pin->description,0,50) . " by <a href='http://pinterest.com" . $pin->user_url . "'>" . $pin->user_fullname . "</a> onto <a href='http://www.pinterest.com" . $pin->pinboard_url . "'>" . $pin->pinboard_name . "</a>.\n";
	}
?>	
</div>
</body>