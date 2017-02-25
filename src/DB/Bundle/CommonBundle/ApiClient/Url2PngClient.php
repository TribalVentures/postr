<?php
namespace DB\Bundle\CommonBundle\ApiClient;

use SendGrid\Email;

/**
* This class use send grid apis tocommunicate with there system to send emails
*/
class Url2PngClient {
	
	/**
	 * This function send email and return response
	 * @param array $emailDetail
	 */
	# See also
	# codeigniter - https://github.com/gkimpson/url2png-codeigniter
	# wordpress - https://wordpress.org/plugins/url2png-screenshots/
	# drupal - https://www.drupal.org/project/url2png

	private static function url2png_v6($apiKey, $secretKey, $url, $args) {
  		# urlencode request target
  		$options['url'] = urlencode($url);

  		$options += $args;

  		# create the query string based on the options 
  		$_parts = array();
  		foreach($options as $key => $value) { 
  			$_parts[] = "$key=$value"; 
  		}
  		# create a token from the ENTIRE query string
  		$query_string = implode("&", $_parts);
  		$TOKEN = md5($query_string . $secretKey);

  		return "https://api.url2png.com/v6/$apiKey/$TOKEN/png/?$query_string";

	}
	
	/**
	 * This function return the iamge URL of web url
	 * @param string $apiKey
	 * @param string $secretKey
	 * @param strings $url
	 */
	public static function getUrlImage($apiKey, $secretKey, $url) {
		$options= array();
		$options['force']     = 'timestamp';      # [false,always,timestamp] Default: false
		$options['fullpage']  = 'false';      # [true,false] Default: false
		$options['thumbnail_max_width'] = '540';      # scaled image width in pixels; Default no-scaling.
		$options['viewport']  = "540x310";  # Max 5000x5000; Default 1280x1024
		
		return self::url2png_v6($apiKey, $secretKey, $url, $options);
	}
}
?>