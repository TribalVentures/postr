<?php

namespace DB\Bundle\AppBundle\Common;

use DB\Bundle\CommonBundle\Util\DBUtil;
class Config {
	static $CONFIG = array();
	
	const SECURE_CONFIG_PATH = '/opt/tribal/src/postreach/docs/config.json';
	//const SECURE_CONFIG_PATH = '/home/patildipakr/work/workspace/postreach/docs/config.json';
	
	//Default variable
	const DEFAULT_FROM_NAME = 'Interior Postr';

	//Paging variable
	const RECORDS_PER_PAGE	= 10;
	const OLD_RECORD_NO_OF_DAY = '5';
	
	const UPLOAD_PATH_LOGO = 'uploads/logo';
	
	const NO_OF_DAY = '1';
	const NO_OF_HOURS = '3';
	
	const POSTREACH_FACEBOOK_PROFILE_CATEGORY = 'Profile';
	
	const URL2PNG_APIKEY = 'P095BBCB7C7CA01';
	const URL2PNG_SECRET = 'S_5FCC608ABEB9B';
	
	const LOG_DEBUG = 'DEGUB';
	const LOG_INFO = 'INFO';
	const LOG_ERROR = 'ERROR';
	
	const COOKIE_KEY_SID = '__ap_sid';
	const COOKIE_KEY_DISCOUNT_COUPON = '__ap_dc';
	
	/**
	 * This function return all picklist types
	 */
	public static function getPicklistType() {
		$list = array();
		
		$list[] = array('value'=>'category', 'label'=>'Category');
		
		return $list;
	}
	
	/**
	 * This function return all picklist types
	 */
	public static function getSpkeSortByList() {
		$spikSortByString = 'fb_total_engagement, fb_likes, fb_shares, fb_comments, fb_total, twitter, linkedin, fb_tw_and_li, nw_score, nw_max_score, created_at';
		$spikeSortbyList = explode(',', $spikSortByString);
		if(!empty($spikeSortbyList)) {
			for($index = 0; $index < count($spikeSortbyList); $index ++) {
				$spikeSortbyList[$index] = trim($spikeSortbyList[$index]);
			}
		}
		
		return $spikeSortbyList;
	}
	
	/**
	 * Thid function return all team emails
	 */
	public static function getTeamEmails() {
		return Config::getSParameter('TEAM_EMAIL', array());
	}
	
	/**
	 * This function return parameter form default JSON 
	 * @param unknown $param
	 * @param string $defaultValue
	 */
	public static function getSParameter($param, $defaultValue = '') {
		return self::getSConfig(self::SECURE_CONFIG_PATH, $param, $defaultValue);
	}
	
	/**
	 * This function return the available supported timezone list
	 */
	public static function getSupportedTimezoneList() {
		$timeZoneString = self::getSParameter('SUPPORTED_TIMEZONE', '');
		if(empty($timeZoneString)) {
			return array();
		}
		
		$tempTimeZoneList = explode('#', $timeZoneString);
		$timeZoneList = array();
		if(!empty($tempTimeZoneList)) {
			for($index = 0; $index < count($tempTimeZoneList); $index ++) {
				$tempTimeZoneList[$index] = trim($tempTimeZoneList[$index]);
				$area = explode('/', $tempTimeZoneList[$index]);
				if(count($area) > 0) {
					if(!isset($timeZoneList[$area[0]])) {
						$timeZoneList[$area[0]] = array();
					}
					$timeZoneList[$area[0]][$tempTimeZoneList[$index]] = $tempTimeZoneList[$index];
				}
			}
		}
		
		return $timeZoneList;
	}
	
	/**
	 * This function return config form JSON file
	 * @param unknown $param
	 * @param string $default
	 */
	private static function getSConfig($url, $param, $defaultValue = '') {
		$config = array();
		if($url == self::SECURE_CONFIG_PATH && empty(self::$CONFIG)) {
			if(empty(self::$CONFIG)) {
				self::$CONFIG = DBUtil::readJson($url);
			}
			$config = self::$CONFIG;
		} else {
			$config = DBUtil::readJson($url);
		}
		
		if(isset($config[$param])) {
			$defaultValue = $config[$param];
		}
		
		return $defaultValue;
	}
	
	private static function getCurrentTimezone() {
		$currentHour = date('H');
		$currentMinute = date('i');
	}
}
