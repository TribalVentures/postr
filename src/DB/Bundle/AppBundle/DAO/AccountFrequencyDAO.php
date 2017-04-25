<?php
namespace DB\Bundle\AppBundle\DAO;

use DB\Bundle\CommonBundle\Base\BaseDAO;
use DB\Bundle\AppBundle\Entity\AccountFrequency;
use DB\Bundle\CommonBundle\Util\DBUtil;
/**
 * Class For Account Frequency DAO, This class is responsible for manage database 
 * operation for account_frequency table/entity
 *
 * @namespace DB\Bundle\AppBundle\DAO
 *
 * @author Dipak Patil
 */
class AccountFrequencyDAO extends BaseDAO {
	
	/**
	 * Always need doctrim object to initilise Account frequency dao object
	 * @param $_dm - Doctrime object
	 */
	function __construct($_dm) {
		parent :: __construct($_dm);
	}
	
	/**
	 * This function add account frequency in DB
	 * @param array $accountFrequencyDetail
	 * @return boolean
	 */
	public function addAccountFrequency($accountFrequencyDetail) {
		if(empty($accountFrequencyDetail)) {
			return array();	
		}
		
		if(empty($accountFrequencyDetail['category'])) {
			$accountFrequencyDetail['category'] = AccountFrequency::ACCOUNT_FREQUENCY_CATEGORY_AUTOPILOT;
		}
		
		if(empty($accountFrequencyDetail['frequency'])) {
			$accountFrequencyDetail['frequency'] = '1111111';
		}
		
		if(empty($accountFrequencyDetail['timezone'])) {
			$accountFrequencyDetail['timezone'] = 'America/New_York';
		}
		
		$accountFrequency = new AccountFrequency();
		
		$accountFrequency->setAccountId($accountFrequencyDetail['accountId']);
		$accountFrequency->setCategory($accountFrequencyDetail['category']);
		
		$accountFrequency->setFrequency($accountFrequencyDetail['frequency']);
		
		$accountFrequency->setTimezone($accountFrequencyDetail['timezone']);
		
		$accountFrequency = $this->save($accountFrequency);
		
		$newDetail = false;
		if(is_object($accountFrequency)) {
			$newDetail = $accountFrequency->toArray();
		}
		return $newDetail;
	}
	
	/**
	 * This function manage accunt frequency, If not exist then added in DB otherwise update
	 * @param array $accountFrequencyDetail
	 */
	public function manageAccountFrequency($accountFrequencyDetail) {
		if(empty($accountFrequencyDetail)) {
			return array();
		}
		
		//Check if exist
		$detail = array();
		if(!empty($accountFrequencyDetail['accountId'])) {
			$existAccountFrequencyDetail = $this->findSingleDetailBy(new AccountFrequency(), array('accountId'=>$accountFrequencyDetail['accountId']));
		
			if(!empty($existAccountFrequencyDetail)) {
				$record = array();
				
				if(!empty($accountFrequencyDetail['category'])) {
					$record['category'] = $accountFrequencyDetail['category'];
				}
				
				if(!empty($accountFrequencyDetail['frequency'])) {
					$record['frequency'] = $accountFrequencyDetail['frequency'];
				}
				
				if(!empty($accountFrequencyDetail['timezone'])) {
					$record['timezone'] = $accountFrequencyDetail['timezone'];
				} else {
					$record['timezone'] = 'America/New_York';
				}
				
				if(!empty($record)) {
					$accountFrequency = new AccountFrequency();
					$accountFrequency->setAccountFrequencyId($existAccountFrequencyDetail['accountFrequencyId']);
					
					$detail = $this->update($accountFrequency, $record);
				}
			} else {
				$detail = $this->addAccountFrequency($accountFrequencyDetail);
			}
			
			//manage email notofication
			$notificationSettingsDAO = new NotificationSettingsDAO($this->getDoctrine());
			$notificationSettingsDAO->manageNotificationSettings(array('accountId'=>$accountFrequencyDetail['accountId']));
			
			return $detail;
		}
	}
	
	/**
	 * This function return the account frequency detail
	 * @param integer $accountId
	 */
	public function getAccountFrequencyDetail($accountId, $option = array()) {
		$param = array('accountId'=>$accountId);
		if(!empty($option['category'])) {
			$param['category'] = $option['category'];
		}
		
		$accountFrequencyDetail = $this->findSingleDetailBy(new AccountFrequency(), $param);
		if(!empty($accountFrequencyDetail)) {
			//Divide the day values
			$accountFrequencyDetail['frequencyList'] = DBUtil::strToArray($accountFrequencyDetail['frequency']);
		}
		
		return $accountFrequencyDetail;
	}
	
	/**
	 * This function set frequency to week day
	 * @param array $frequencyDetail
	 */
	public function setFrequencyDetail($frequencyDetail) {
		if(empty($frequencyDetail['frequency'])) {
			$frequencyDetail['frequency'] = '000000';
		}
		
		$frequencyList = DBUtil::strToArray($frequencyDetail['frequency']);
		
		if(isset($frequencyList[0])) {
			$frequencyDetail['sunday'] = $frequencyList[0];
		} else {
			$frequencyDetail['sunday'] = 0;
		}
		
		if(isset($frequencyList[1])) {
			$frequencyDetail['monday'] = $frequencyList[1];
		} else {
			$frequencyDetail['monday'] = 0;
		}
		
		if(isset($frequencyList[2])) {
			$frequencyDetail['tuesday'] = $frequencyList[2];
		} else {
			$frequencyDetail['tuesday'] = 0;
		}
		
		if(isset($frequencyList[3])) {
			$frequencyDetail['wednesday'] = $frequencyList[3];
		} else {
			$frequencyDetail['wednesday'] = 0;
		}
		
		if(isset($frequencyList[4])) {
			$frequencyDetail['thursday'] = $frequencyList[4];
		} else {
			$frequencyDetail['thursday'] = 0;
		}
		
		if(isset($frequencyList[5])) {
			$frequencyDetail['friday'] = $frequencyList[5];
		} else {
			$frequencyDetail['friday'] = 0;
		}
		
		if(isset($frequencyList[6])) {
			$frequencyDetail['saturday'] = $frequencyList[6];
		} else {
			$frequencyDetail['saturday'] = 0;
		}
		
		return $frequencyDetail;
	}
	
	/**
	 * This function return frequency string made week day
	 * @param array $frequencyDetail
	 */
	public function getFrequencyDetail($frequencyDetail) {
		$frequency = '';
	
		if(isset($frequencyDetail['sunday']) && $frequencyDetail['sunday'] == '1') {
			$frequency .= '1';
		} else {
			$frequency .= '0';
		}
	
		if(isset($frequencyDetail['monday']) && $frequencyDetail['monday'] == '1') {
			$frequency .= '1';
		} else {
			$frequency .= '0';
		}
		
		if(isset($frequencyDetail['tuesday']) && $frequencyDetail['tuesday'] == '1') {
			$frequency .= '1';
		} else {
			$frequency .= '0';
		}
	
		if(isset($frequencyDetail['wenesday']) && $frequencyDetail['wenesday'] == '1') {
			$frequency .= '1';
		} else {
			$frequency .= '0';
		}
	
		if(isset($frequencyDetail['thursday']) && $frequencyDetail['thursday'] == '1') {
			$frequency .= '1';
		} else {
			$frequency .= '0';
		}
	
		if(isset($frequencyDetail['friday']) && $frequencyDetail['friday'] == '1') {
			$frequency .= '1';
		} else {
			$frequency .= '0';
		}
	
		if(isset($frequencyDetail['saturday']) && $frequencyDetail['saturday'] == '1') {
			$frequency .= '1';
		} else {
			$frequency .= '0';
		}
	
		$frequencyDetail['frequency'] = $frequency;
	
		return $frequencyDetail;
	}
	
	/**
	 * This function returns frequncy in human readable format
	 * @param string $frequency
	 */
	public function getFrequencyReadable($frequency) {
		
		if(empty($frequency)){
			return '';
		}
		
		$frequencyString = '';
		
		$frequencyList = DBUtil::strToArray($frequency);
		
		if(isset($frequencyList[0])) {
			$frequencyString.= 'Su ';
		}
		
		if(isset($frequencyList[1])) {
			$frequencyString.= 'M ';
		}
		
		if(isset($frequencyList[2])) {
			$frequencyString.= 'Tu ';
		}
		
		if(isset($frequencyList[3])) {
			$frequencyString.= 'W ';
		}
		
		if(isset($frequencyList[4])) {
			$frequencyString.= 'Th ';
		}
		
		if(isset($frequencyList[5])) {
			$frequencyString.= 'F ';
		}
		
		if(isset($frequencyList[6])) {
			$frequencyString.= 'Sa ';
		}
		
		// Trim and add commas
		$frequencyString = str_replace(' ', ',', trim($frequencyString));
		
		return $frequencyString;
	}
}
?>