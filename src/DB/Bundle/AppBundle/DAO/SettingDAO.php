<?php
namespace DB\Bundle\AppBundle\DAO;

use DB\Bundle\CommonBundle\Base\BaseDAO;
use DB\Bundle\AppBundle\Entity\Setting;

/**
 * Class For Setting DAO, This class is responsible for manage database 
 * operation for account table/entity
 *
 * @namespace DB\Bundle\AppBundle\DAO
 *
 * @author Dipak Patil
 */
class SettingDAO extends BaseDAO {
	const SETTING_KEY_NOTIFICATION_EMAIL 							= 'NOTIFICATION_EMAIL';
	const SETTING_KEY_NOTIFICATION_ENABLE_NOTIFICATION 				= 'NOTIFICATION_ENABLE_NOTIFICATION';
	const SETTING_KEY_NOTIFICATION_EMAIL_ENABLE_SIGNUP_NOTIFICATION = 'NOTIFICATION_EMAIL_ENABLE_SIGNUP_NOTIFICATION';
	const SETTING_KEY_NOTIFICATION_EMAIL_ENABLE_SIGNUP_COMPLETE_NOTIFICATION = 'NOTIFICATION_EMAIL_ENABLE_SIGNUP_COMPLETE_NOTIFICATION';
	const SETTING_KEY_NOTIFICATION_EMAIL_ENABLE_ACCOUNT_CANCEL 		= 'NOTIFICATION_EMAIL_ENABLE_ACCOUNT_CANCEL';
	
	const SETTING_KEY_SCRAP_HOUZZ_CURRENT_PAGE 						= 'SCRAP_HOUZZ_CURRENT_PAGE';
	const SETTING_KEY_SCRAP_HOUZZ_URL 								= 'SCRAP_HOUZZ_URL';
	/**
	 * Always need doctrim object to initilise EmailNotification dao object
	 * @param $_dm - Doctrime object
	 */
	function __construct($_dm) {
		parent :: __construct($_dm);
	}
	
	/**
	 * This function add new setting
	 * @param array $settingDetail
	 */
	public function addSetting($settingDetail = array()) {
		if(empty($settingDetail)) {
			return false;
		}
		
		//Set defautl data
		if(empty($settingDetail['label'])) {
			$settingDetail['label'] = '';
		}
		
		if(empty($settingDetail['settingKey'])) {
			$settingDetail['settingKey'] = '';
		}
		
		if(empty($settingDetail['settingValue'])) {
			$settingDetail['settingValue'] = '';
		}
		
		$setting = new Setting();
		
		$setting->setLabel($settingDetail['label']);
		
		$setting->setSettingKey($settingDetail['settingKey']);
		$setting->setSettingValue($settingDetail['settingValue']);
		
		$setting = $this->save($setting);
		
		$newDetail = false;
		if(is_object($setting)) {
			$newDetail = $setting->toArray();
		}
		return $newDetail;
	}
	
	/**
	 * This function edit settig detail
	 * @param array $settingDetail
	 */
	public function editSetting($settingDetail = array()) {
		if(empty($settingDetail)) {
			return false;
		}
		
		$record = array();
		if(isset($settingDetail['label'])) {
			$record['label'] = $settingDetail['label'];
		}
		
		if(isset($settingDetail['settingKey'])) {
			$record['settingKey'] = $settingDetail['settingKey'];
		}

		if(isset($settingDetail['settingValue'])) {
			$record['settingValue'] = $settingDetail['settingValue'];
		}
		
		$updatedDetail = array();
		if(!empty($record)) {
			$setting = new Setting();
			$setting->setId($settingDetail['id']);
			$setting = $this->update($setting, $record);
			
			if(is_object($setting)) {
				$updatedDetail = $setting->toArray();
			}
		}
		
		return $updatedDetail;
	}
	
	/**
	 * This function set the setting value by key
	 * @param string $settingKey
	 * @param string $settingValue
	 */
	public function updateSetting($settingKey, $settingValue) {
		if(empty($settingKey)) {
			return array();
		}
		
		$settingDetail = $this->findSingleDetailBy(new Setting(), array('settingKey'=>$settingKey));
		if(empty($settingDetail)) {
			return array();
		}
		
		$settingDetail['settingValue'] = $settingValue;
		return $this->editSetting($settingDetail);
	}
	
	/**
	 * This function return setting detail by setting key
	 * @param string $settingKey
	 * @return mixed|NULL[]
	 */
	public function getSettingByKey($settingKey) {
		return $this->findSingleDetailBy(new Setting(), array('settingKey'=>$settingKey));
	}
	
	/**
	 * This function return all setting list by setting key, If no any 
	 * key provide then that will return all settings.
	 * @param array $settingKeyList
	 */
	public function getSettingList($settingKeyList = array()) {
		$em = $this->getDoctrine()->getManager();
		
		//$from for entity name (table name)
		$from = "DB\\Bundle\\AppBundle\Entity\\Setting setting ";
		
		$whereCondition = '';
		if(!empty($settingKeyList)) {
			foreach($settingKeyList as $settingKey) {
				if(!empty($whereCondition)) {
					$whereCondition .= ", ";
				}
				$whereCondition .= "'" . $settingKey . "' ";
			}
			
			$whereCondition = 'setting.settingKey IN (' . $whereCondition . ')';
		}
		
		$sql = "SELECT setting.id, setting.label, setting.settingKey, setting.settingValue " .
				"FROM " . $from;
		
		if(!empty($whereCondition)) {
			$sql .= " WHERE " . $whereCondition . " ";
		}
		
		$sql .= "ORDER BY setting.id ASC ";
		
		//echo $sql;
		
		$query = $em->createQuery($sql);
		$result = $query->getResult();
		
		return $result;
	}
	
	/**
	 * This function return all setting map by setting keys, If no any key
	 * provides then return map of all settings
	 * @param array $settingKeyList
	 */
	public function getSettingMap($settingKeyList = array()) {
		$settingList = $this->getSettingList($settingKeyList);
		$settingMap = array();
		
		if(!empty($settingList)) {
			foreach($settingList as $settingDetail) {
				$settingMap[$settingDetail['settingKey']] = $settingDetail;
			}
		}
		
		return $settingMap;
	}
	
	/**
	 * This function return the email notification related setting
	 */
	public function getEnailNotificationSettingMap() {
		$settingKeyList = array();
		$settingKeyList[] = SettingDAO::SETTING_KEY_NOTIFICATION_EMAIL;
		$settingKeyList[] = SettingDAO::SETTING_KEY_NOTIFICATION_ENABLE_NOTIFICATION;
		$settingKeyList[] = SettingDAO::SETTING_KEY_NOTIFICATION_EMAIL_ENABLE_SIGNUP_NOTIFICATION;
		$settingKeyList[] = SettingDAO::SETTING_KEY_NOTIFICATION_EMAIL_ENABLE_SIGNUP_COMPLETE_NOTIFICATION;
		$settingKeyList[] = SettingDAO::SETTING_KEY_NOTIFICATION_EMAIL_ENABLE_ACCOUNT_CANCEL;
		
		return $this->getSettingMap($settingKeyList);
	}
	
	/**
	 * This function update the notification setting in DB
	 * @param array $settingDetailList
	 */
	public function updateNotification($settingDetailList = array()) {
		if(empty($settingDetailList)) {
			return array();
		}
		
		$settingKeyList = array();
		$settingKeyList[] = SettingDAO::SETTING_KEY_NOTIFICATION_EMAIL;
		$settingKeyList[] = SettingDAO::SETTING_KEY_NOTIFICATION_ENABLE_NOTIFICATION;
		$settingKeyList[] = SettingDAO::SETTING_KEY_NOTIFICATION_EMAIL_ENABLE_SIGNUP_NOTIFICATION;
		$settingKeyList[] = SettingDAO::SETTING_KEY_NOTIFICATION_EMAIL_ENABLE_SIGNUP_COMPLETE_NOTIFICATION;
		$settingKeyList[] = SettingDAO::SETTING_KEY_NOTIFICATION_EMAIL_ENABLE_ACCOUNT_CANCEL;
		
		foreach($settingKeyList as $settingKey) {
			if(isset($settingDetailList[$settingKey])) {
				//Set value to zero
				$settingDetail = $settingDetailList[$settingKey];
				$this->updateSetting($settingKey, $settingDetail);
			} else {
				$this->updateSetting($settingKey, '0');
			}
		}
	}
}
?>