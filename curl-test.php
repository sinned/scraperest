<?php
/**
 * Scrapin' Pinterest
 * @author Dennis Yang <http://www.github.com/sinned> 
 *
 */


	error_reporting(E_ERROR);
	
	require "scraperest-config.php";
	require "lib/ez_sql.php";
	require "lib/pins.lib.php";
	
	$url = "http://pinterest.com/popular/'";
	$url = "http://pinterest.com/about/help/";
	
	$html = loadContentFromUrl($url);
	echo $html . "\n\n";
?>