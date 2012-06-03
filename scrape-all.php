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

	echo "\n---- BEGIN PINTEREST SCRAPE " . date("F j, Y, g:i a") . " ---\n";

	$urls = $db->get_col("SELECT url FROM pinterest_urls");
	//$urls = array ('http://localhost/scraperest/pin-test-cars.html');
	//$urls = array ('http://pinterest.com/popular/');
	//$urls = array ('http://pinterest.com/all/?category=art&lazy=1&page=3');
	//$urls = array ('http://localhost/scraperest/pin-test-gifts.html');
	//$urls = array ('http://localhost/scraperest/pin-test-arts.html');	
		
	
	foreach ($urls as $url) {
		//echo "Scraping $url \n";
		echo "\n";

		for ($page = 1; $page <=10; $page++) {
			
			$new_pins_on_this_page = false;
			
			// add page number
			if (strpos($url, "?")) {
				$pagedurl = $url . "&lazy=1&page=" . $page;
			} else {
				$pagedurl = $url . "?lazy=1&page=" . $page;
			}
			echo "Scraping $pagedurl \n";
			
			$html = loadContentFromUrl($pagedurl);
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
		
			echo " FOUND " . ($divs->length) . " PINS!! \n";
		
			foreach ($divs as $div) {
				$pin = new PinterestPin();
				$pin->from_url = $url;
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

				if (isset($xpath->query("//p[@class='stats colorless']//span[@class='LikesCount']")->item(0)->nodeValue)) { 
					$likesstring = trim($xpath->query("//p[@class='stats colorless']//span[@class='LikesCount']")->item(0)->nodeValue);
					$pin->likes_count = substr($likesstring, 0, strrpos($likesstring," like"));
				}	

				if (isset($xpath->query("//strong[@class='price']")->item(0)->nodeValue)) { 
					$pricestring = trim($xpath->query("//strong[@class='price']")->item(0)->nodeValue);
					//echo "MATCHING: $pricestring YAY";
					$pricestring = preg_replace('/[,]/', '', $pricestring); // remove comma
					preg_match('/(.)([0-9]*\.[0-9]*)/u', $pricestring, $matches);
					//print_r($matches);
					//echo "YAH:" . $matches[2] . "MOO";
					$pin->price = $matches[2];
					$pin->currency = $matches[1];
					//echo $pin->currency . $pin->price;
				}	

				if (isset($xpath->query("//p[@class='stats colorless']//span[@class='RepinsCount']")->item(0)->nodeValue)) { 
					$repinsstring = trim($xpath->query("//p[@class='stats colorless']//span[@class='RepinsCount']")->item(0)->nodeValue);
					$pin->repins_count = substr($repinsstring, 0, strrpos($repinsstring," repin"));
					//echo $repinsstring . " " . $pin->repin_count . "\n";
				}
			
				if (isset($xpath->query("//p[@class='stats colorless']//span[@class='CommentsCount']")->item(0)->nodeValue)) { 
					$commentsstring = trim($xpath->query("//p[@class='stats colorless']//span[@class='CommentsCount']")->item(0)->nodeValue);
					$pin->comments_count = substr($commentsstring, 0, strrpos($commentsstring," comment"));
					//echo $commentsstring . " " . $pin->comment_count . "\n";				
				}							
				//echo $xpath->query("//p[@class='stats colorless']//span[@class='RepinsCount']")->item(0)->nodeValue;		
				//echo $xpath->query("//p[@class='stats colorless']//span[@class='CommentsCount']")->item(0)->nodeValue;		
							
				//$pin->_print();		
				if ($pin->_save()) {
					$new_pins_on_this_page = true;
				} 
			} // end-foreach:divs
			
			// stop the loop if there's no more pins.
			if (!$new_pins_on_this_page) {
				$page = 11;
			}
			
		} // end-for: pages
	} // end-foreach:urls

	echo "\n---- END PINTEREST SCRAPE " . date("F j, Y, g:i a") . " ---\n";
	
?>
