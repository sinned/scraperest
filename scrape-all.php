<?php
/**
 * Scrapin' Pinterest
 * @author Dennis Yang <sinned@gmail.com> 
 *
 */
	require "scraperest-config.php";
	require "lib/ez_sql.php";
	require "lib/pins.lib.php";

	$urls = $db->get_col("SELECT url FROM pinterest_urls");
	
	foreach ($urls as $url) {
		echo "Scraping $url \n";

		//$url = "http://www.pinterest.com/all/";
		//$url = "http://pinterest.com/all/?category=architecture";
		//$url = "http://localhost/scraperest/pin-test.html";
		//$url = "http://localhost/scraperest/dom-test.html";
	
		$html = loadContentFromUrl($url);
		//echo $html . "\n\n";
	
		// let's use DOMDocument since all of the pins are in the class "pin"
	
		$dom = new DOMDocument();
		$dom->validateOnParse = true;
		$dom->loadHTML($html);
	
		//var_dump($dom);
	
		//echo $dom->saveHTML();

		$xp = new DOMXpath($dom);
		//$divs = $xp->query('//*[contains(@class, \'pin\')]'); // too aggressive 
		$divs = $xp->query("//*[@class='pin']"); // grabs each div with class 'pin' and tucks it into a node
		
		foreach ($divs as $div) {
			$pin = new PinterestPin();
			$pin->data_id = $div->getAttribute('data-id');

			// from here, we run another xPath query for each of the attributes of the pin
			$newDom = new DOMDocument();
			$newDom->appendChild($newDom->importNode($div, true));	
			$xpath = new DOMXPath ($newDom);
		
			$pin->url = $xpath->query("//a[contains(@class, 'PinImage')]")->item(0)->getAttribute('href');
			$pin->img_src = $xpath->query("//a[contains(@class, 'PinImage')]/img")->item(0)->getAttribute('src');
			$pin->description = $xpath->query("//p[@class='description']")->item(0)->nodeValue;

			$pin->user_url = $xpath->query("//div[@class='convo attribution clearfix']/a")->item(0)->getAttribute('href');	
			$pin->user_img_src = $xpath->query("//a[@class='ImgLink']/img")->item(0)->getAttribute('src');					
			$pin->user_fullname = $xpath->query("//div[@class='convo attribution clearfix']/a")->item(0)->getAttribute('title');
		
			$pin->pinboard_name = $xpath->query("//div[@class='convo attribution clearfix']//a")->item(2)->nodeValue;
			$pin->pinboard_url = $xpath->query("//div[@class='convo attribution clearfix']//a")->item(2)->getAttribute('href');		
							
			//$pin->_print();		
			$pin->_save();
			//echo get_inner_html($div);
		} // end-foreach:divs
	} // end-foreach:urls
	
?>