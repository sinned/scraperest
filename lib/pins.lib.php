<?php

	class PinterestPin {
		
		var	$data_id = "";
		var $url = "";
		var $img_src = "";
		var $description = "";
		var $currency = "";		
		var $price = 0;
		
		var $user_url = "";
		var $user_img_src = "";
		var $user_fullname = "";
		
		var $likes_count = 0;
		var $comments_count = 0;
		var $repins_count = 0;
		
		var $pinboard_url = "";
		var $pinboard_name = "";
		
		var $from_url = ""; // the url from which this pin was scraped
		
		function _print() {
			echo "PIN: " . $this->data_id . " " . $this->description . 
					" l:" . $this->likes_count . 
					" r:" . $this->repins_count . 
					" c:" . $this->comments_count;
			if ($this->price > 0) {
				echo " " . $this->currency . $this->price;
			}
			echo "\n";
		}


		function _printHtml() {
			echo "<p><a href='http://www.pinterest.com" . $this->url . "'><img src='" . $this->img_src . "' /></a> " . $this->description . " by <a href='http://pinterest.com" . $this->user_url . "'>" . $this->user_fullname . "</a> onto <a href='http://www.pinterest.com" . $this->pinboard_url . "'>" . $this->pinboard_name . "</a>.</p>\n";
		}		
		
		function _save() {
			
			global $db; // grab the ez_sql db connection
		
			
			$sql = "INSERT INTO pins (data_id, url, img_src, description, currency, price, user_url, user_img_src, user_fullname, pinboard_url, pinboard_name, likes_count, comments_count, repins_count, from_url)
					VALUES ('" . $db->escape($this->data_id) . "', 
							'" . $db->escape($this->url) . "',
							'" . $db->escape($this->img_src) . "',							
							'" . $db->escape($this->description) . "',
							'" . $db->escape($this->currency) . "',														
							'" . $db->escape($this->price) . "',							
							'" . $db->escape($this->user_url) . "',
							'" . $db->escape($this->user_img_src) . "',
							'" . $db->escape($this->user_fullname) . "',
							'" . $db->escape($this->pinboard_url) . "',
							'" . $db->escape($this->pinboard_name) . "',
							'" . $db->escape($this->likes_count) . "',
							'" . $db->escape($this->comments_count) . "',
							'" . $db->escape($this->repins_count) . "',
							'" . $db->escape($this->from_url) . "'
							)";
			if ($db->query($sql)) {
				echo " INSERTED ";
				$this->_print();			
				return true;
			} else {
				// insert most likely failed because we have it already...
				//echo "FAILED PIN " . $this->data_id . "\n";
				//echo $sql;
				return false;
			}
			
		}
		
	} // end classPinterestPin
	
	function loadContentFromUrl ($url="", $htuser="", $htpasswd="") {
    
		// load the content with cURL
	
		// create a new cURL resource
	    $ch = curl_init();
	    // set URL and other appropriate options
	    curl_setopt($ch, CURLOPT_URL, $url);
	    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); // get the response as a string from curl_exec(), rather than echoing it
	    curl_setopt($ch, CURLOPT_HEADER, 0);
	
		//curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($ch, CURLOPT_VERBOSE, false);
		$agent= 'curl 7.19.7 (universal-apple-darwin10.0) libcurl/7.19.7 OpenSSL/0.9.8r zlib/1.2.3';
		curl_setopt($ch, CURLOPT_USERAGENT, $agent);

	    if ($htuser && $htpasswd) {
			curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC); 
			curl_setopt($ch, CURLOPT_USERPWD, $htuser . ":" . $htpasswd);	
	    }

	    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1); // turn on redirect to follow any Location in header
	
	    // grab URL and set it to $content
	
	    $content = curl_exec($ch);
	    // close cURL resource, and free up system resources
	    curl_close($ch);
    
	    return $content;

	} // end-function loadContentFromUrl
	
	function get_inner_html( $node ) { 
	    $innerHTML= ''; 
	    $children = $node->childNodes; 
	    foreach ($children as $child) { 
	        $innerHTML .= $child->ownerDocument->saveXML( $child ); 
	    } 

	    return $innerHTML; 
	} 	

?>