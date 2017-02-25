<?php

namespace DB\Bundle\AppBundle\Common;

use DB\Bundle\CommonBundle\Util\DBUtil;
class PrimoAPIConfig {
	const PRIMO_API_KEY = 'da420657-42c1-4cdd-95c5-5e44d8048cd5';
	
	const SPIKE_DEFAULT_HOUR = '8';
	
	const SPIKE_DEFAULT_SORT_BY = 'highest_velocity';
	
	const SPIKE_DEFAULT_INCLUDE_COUNTRY = 'us';
	
	//Constant for social massenger
	//This is for dipak health page access toke, demo app
	//const FACEBOOK_MESSANGER_ACCESS_TOKEN = 'EAALr05SlQygBACbWA2i8lVzIICJ2ys4LrkeXZAsLVrA6d4jZBmgmIPw8JK1jEwNbFGlRYQ3oJAKUV3ZAmuaHsrYW3f5QF80itlaoiUZBin8rCS8dZCEQ1peQCmsqnmYBi9n8aXiZCpKcDkLD48ZCJfrUZBrpyR5iUHMulrbTn0q3dgZDZD';
	const FACEBOOK_MESSANGER_ACCESS_TOKEN = 'CAAYGB79IhbgBAFJ96qAirVRVN46lWeKCCtdrpDEOkbTVylu4PsD1zShjQfGfpWddDPURP2YKUnX0IAEqymXEOXWj68tP2ahOZAottM6ROHZBdSDGeG3j3Xj13ytE6lmiX5z46MhswuhkL2tmjdxL6cp4aL5fYuVMXa7BJF0Mi2B6YCHfJx7ynlmMky4ZAgZD';
	
	/**
	 * Thid function validate the request
	 * @param array $request
	 */
	public static function validateRequest($request) {
		$error = array('status'=>true);
		if(!isset($request) || empty($request)) {
			$error['status'] = false;
			$error['message'] = 'Invalid request';
			return $error;
		}
		
		//cehck for API key
		if(empty($request['apiKey']) || $request['apiKey'] != self::PRIMO_API_KEY) {
			$error['status'] = false;
			$error['message'] = 'Invalid API key';
			return $error;
		}
		
		return $error;
	}
	
	/**
	 * Ths function validate the search api request
	 * @param array $request
	 */
	public static function validateSearchRequest($request) {
		$error = array('status'=>true);
		
		if(!isset($request) || empty($request)) {
			$error['status'] = false;
			$error['message'] = 'Invalid request';
			return $error;
		}
		
		//cehck for API key
		if(empty($request['apiKey']) || $request['apiKey'] != self::PRIMO_API_KEY) {
			$error['status'] = false;
			$error['message'] = 'Invalid API key';
			return $error;
		}
		
		//cehck for API key
		if(empty($request['keywords'])) {
			$error['status'] = false;
			$error['message'] = 'No keywords foud';
			return $error;
		}
		
		return $error;
	}
	
	/**
	 * This function return default publisher for exlcude
	 */
	public static function defaultExcludePublisher() {
		$excludePublisher = 'washingtonexaminer.com,breitbart.com,foxnews.com,ajc.com,video.foxnews.com,insider.foxnews.com,redstate.com';
		return DBUtil::explode(',', $excludePublisher);
	}
	
	public function getFbTemplates($template = '') {
		
	}
}