<?php
namespace DB\Bundle\CommonBundle\Util;
use DB\Bundle\CommonBundle\Base\Paging;

class DBUtil {
	/**
	 * This fucntion is used to get unique key for uploading image
	 *
	 * @return $uniqueKey
	 */
	public static function getUniqueKey($len = 3) {
		$base = 'ABCDEFGHKLMNOPQRSTWXYZabcdefghjkmnpqrstwxyz123456789';
		$max = strlen($base)-1;
		$uniqueKey = '';
		while (strlen($uniqueKey)<$len+1) {
			$uniqueKey.=$base{mt_rand(0,$max)};
		}

		$uniqueKey = date("YmdHisu") . $uniqueKey;

		return $uniqueKey;
	}
	
	/**
	 * This function will return the excrepted password by salt
	 * @param unknown $password
	 * @return boolean|string
	 */
	public static function getPassword($password) {
		if(!isset($password)) {
			return false;
		}
		
		$options = array(
				'cost' => 11,
				'salt' => "P@ST_TREND!ING_ART!CLE"
		);
		
		return password_hash($password, PASSWORD_BCRYPT, $options);
	}

	/**
	 * This function show the pagging details on the page
	 * @param $currentPage - Holds the values of current page
	 * @param $count - Holds the count of table record
	 * @return $paggingDetails - It returns the paging detail
	 */
	public static function getPaggingDetails($currentPage = 1, $count = 0, $resultsPerPage = '') {
		//Calculate count first
		$paging = new Paging();
		$paging->TotalResults = $count;

		if($currentPage > $paging->TotalResults) {
			$currentPage = 1;
		}
		if(empty($resultsPerPage)) {
			$resultsPerPage = 4;
		}
		$paging->ResultsPerPage = $resultsPerPage;
		$paging->LinksPerPage = 10;
		$paging->PageVarName = "page";
		$paging->CurrentPage = $currentPage;

		$paggingDetails = $paging->InfoArray();

		return $paggingDetails;
	}
	
	/**
	 * This function return date in string format
	 */
	public static function getDate($dateType) {
		$date = '';
		if($dateType == 'now') {
			$date = date("Y-m-d H:i:s");
		} else if($dateType == 'tomorrow') {
			$date = date("Y-m-d H:i:s", strtotime('+1 day', strtotime(date("Y-m-d-H-i-s-u"))));
		} else {
			$date = '';
		}
		return $date;
	}
	
	/**
	 * This function formate date into given format, Default formate is myswl date
	 * @param \DateTime $datetime
	 * @param string $format
	 */
	public static function format(\DateTime $datetime, $format = 'Y-m-d H:i:s') {
		$date = '';
		if(isset($datetime) && is_object($datetime)) {
			$date = $datetime->format($format);
		}

		return $date;
	}

	/**
	 * This function return all list of timezone
	 * @return mixed
	 */
	public static function getTimeZoneList() {
		$zones = timezone_identifiers_list();
		$locations = array();
		foreach ($zones as $zone) {
			$zone = explode('/', $zone); // 0 => Continent, 1 => City
		
			// Only use "friendly" continent names
			if ($zone[0] == 'Africa' || $zone[0] == 'America' || $zone[0] == 'Antarctica' || $zone[0] == 'Arctic' || $zone[0] == 'Asia' || $zone[0] == 'Atlantic' || $zone[0] == 'Australia' || $zone[0] == 'Europe' || $zone[0] == 'Indian' || $zone[0] == 'Pacific') {
				if (isset($zone[1]) != '') {
					$locations[$zone[0]][$zone[0]. '/' . $zone[1]] = str_replace('_', ' ', $zone[1]); // Creates array(DateTimeZone => 'Friendly name')
				}
			}
		}
		
		return $locations;
	}
	
	/**
	 * This function splits string into array by delimiter and Also this function trim all elements
	 * @param string $delimiter
	 * @param string $string
	 */
	public static function explode($delimiter, $string) {
		if(empty($string)) {
			return array();
		}
		$splitList = explode($delimiter, $string);
		if(!empty($splitList)) {
			for($index = 0; $index < count($splitList); $index ++) {
				$splitList[$index] = trim($splitList[$index]);
			}
		}
		return $splitList;
	}
	
	/** This function only keep numbers in string and remove all alpha and charecters
	 * @param string $string
	 */
	public static function keepNumeric($string) {
		return preg_replace("/[^0-9]/", "", $string);
	}
	
	/**
	 * This function read JSON data form file
	 * @param string $fileName
	 * @param array $default
	 * @return mixed|unknown
	 */
	public static function readJson($fileName, $default = array()) {
		$content = file_get_contents($fileName);
		if(!empty($content)) {
			try {
				$content = json_decode($content, true);
				return $content;
			} catch (Exception $e) {
				return $default;
			}
		} else {
			return $default;
		}
	}
	
	/**
	 * This function encrypt data with respect to key
	 * @param string $data
	 * @param string $key
	 * @return string
	 */
	public static function encrypt($data, $key = 'Welcome123') {
		return base64_encode($data . $key);
	}


	/**
	 * This function decrypt data with respect to key
	 * @param string $data
	 * @param string $key
	 * @return string
	 */
	public static function decrypt($data, $key = 'Welcome123') {
		$data = base64_decode($data);
		$data = str_replace($key, '', $data);
		return $data;
	}
	
	/**
	 * This fucntion convert the string into charector array
	 * @param string $tring
	 * @return array
	 */
	public static function strToArray($tring) {
		$strArray = array();
		$strlen = strlen( $tring );
		for( $i = 0; $i <= $strlen; $i++ ) {
			$strArray[] = substr( $tring, $i, 1 );
		}
		
		return $strArray;
	}
	
	/**
	 * This function return the domain name form URL
	 * @param string $url
	 */
	public static function getDomain($url) {
		$pieces = parse_url($url);
		$domain = isset($pieces['host']) ? $pieces['host'] : '';
		$regs = array();
		if(preg_match('/(?P<domain>[a-z0-9][a-z0-9\-]{1,63}\.[a-z\.]{2,6})$/i', $domain, $regs)) {
			return $regs['domain'];
		}
		return false;
	}
}
?>