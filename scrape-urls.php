<?php
/**
 * Scrapin' Pinterest
 * @author Dennis Yang <sinned@gmail.com> 
 *
 */
	require "scraperest-config.php";
	require "lib/ez_sql.php";
	require "lib/pins.lib.php";

	$url = "http://pinterest.com/";
	//$url = "http://localhost/scraperest/pin-test.html";
	//$url = "http://localhost/scraperest/dom-test.html";
	
	$html = loadContentFromUrl($url);
	//echo $html . "\n\n";
	
	// find all of the urls of the form /all?category=SOMETHING
	
	$pattern = '/<a href="(\/all\/.*?)"/';
	if (preg_match_all($pattern, $html, $matches)) {
		foreach ($matches[1] as $url) {
			$sql = "INSERT INTO pinterest_urls (url) VALUES ('http://pinterest.com" . $db->escape($url) . "')";
			if ($db->query($sql)) {
				echo "INSERTED! " . $url . "\n";				
			}
		}
	}
	
	$pattern = '/<a href="(\/popular\/.*?)"/';
	if (preg_match_all($pattern, $html, $matches)) {
		foreach ($matches[1] as $url) {
			$sql = "INSERT INTO pinterest_urls (url) VALUES ('http://pinterest.com" . $db->escape($url) . "')";
			if ($db->query($sql)) {
				echo "INSERTED! " . $url . "\n";				
			}
		}
	}	
	
	$pattern = '/<a href="(\/gifts\/.*?)"/';
	if (preg_match_all($pattern, $html, $matches)) {
		foreach ($matches[1] as $url) {
			$sql = "INSERT INTO pinterest_urls (url) VALUES ('http://pinterest.com" . $db->escape($url) . "')";
			if ($db->query($sql)) {
				echo "INSERTED! " . $url . "\n";				
			}
		}
	}		
	
?>