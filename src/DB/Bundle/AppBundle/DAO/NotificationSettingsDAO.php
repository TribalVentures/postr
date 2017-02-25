<?php
namespace DB\Bundle\AppBundle\DAO;

use DB\Bundle\CommonBundle\Base\BaseDAO;
use DB\Bundle\AppBundle\Entity\NotificationSettings;

/**
 * Class For EmailNotification DAO, This class is responsible for manage database 
 * operation for account table/entity
 *
 * @namespace DB\Bundle\AppBundle\DAO
 *
 * @author Dipak Patil
 */
class NotificationSettingsDAO extends BaseDAO { 
	/**
	 * Always need doctrim object to initilise EmailNotification dao object
	 * @param $_dm - Doctrime object
	 */
	function __construct($_dm) {
		parent :: __construct($_dm);
	}
	
	/**
	 * This function add new email notification
	 * @param array $emailNotificationDetail
	 */
	public function addEmailNotification($emailNotificationDetail = array()) {
		if(empty($emailNotificationDetail)) {
			return false;
		}
		
		//Set defautl data
		if(empty($emailNotificationDetail['receivers'])) {
			$emailNotificationDetail['receivers'] = '';
		}
		
		if(empty($emailNotificationDetail['period'])) {
			$emailNotificationDetail['period'] = '8:00';
		}
		
		if(empty($emailNotificationDetail['notifyType'])) {
			$emailNotificationDetail['notifyType'] = NotificationSettings::NOTIFY_TYPE_EMAIL;
		}
		
		if(empty($emailNotificationDetail['lastUpdate'])) {
			$emailNotificationDetail['lastUpdate'] = new \DateTime();
		}
		
		$notificationSettings = new NotificationSettings();
		
		$notificationSettings->setAccountId($emailNotificationDetail['accountId']);
		$notificationSettings->setReceivers($emailNotificationDetail['receivers']);
		
		$notificationSettings->setPeriod($emailNotificationDetail['period']);
		
		$notificationSettings->setNotifyType($emailNotificationDetail['notifyType']);
		
		$notificationSettings->setLastUpdate($emailNotificationDetail['lastUpdate']);
		
		$notificationSettings = $this->save($notificationSettings);
		
		$newDetail = false;
		if(is_object($notificationSettings)) {
			$newDetail = $notificationSettings->toArray();
		}
		return $newDetail;
	}
	
	/**
	 * This function edit advertiser detail
	 * @param array $emailNotificationDetail
	 */
	public function editEmailNotification($emailNotificationDetail = array()) {
		if(empty($emailNotificationDetail)) {
			return false;
		}
		
		$record = array();
		if(!empty($emailNotificationDetail['receivers'])) {
			$record['receivers'] = $emailNotificationDetail['receivers'];
		}
		
		if(!empty($emailNotificationDetail['period'])) {
			$record['period'] = $emailNotificationDetail['period'];
		}

		if(!empty($emailNotificationDetail['notifyType'])) {
			$record['notifyType'] = $emailNotificationDetail['notifyType'];
		}
		
		if(!empty($emailNotificationDetail['lastUpdate'])) {
			$record['lastUpdate'] = $emailNotificationDetail['lastUpdate'];
		}
		
		$updatedDetail = array();
		if(!empty($record)) {
			$notificationSettings = new NotificationSettings();
			$notificationSettings->setNotificationSettingsId($emailNotificationDetail['notificationSettingsId']);
			
			$emailNotification = $this->update($notificationSettings, $record);
			
			if(is_object($emailNotification)) {
				$updatedDetail = $emailNotification->toArray();
			}
		}
		
		return $updatedDetail;
	}
	
	/**
	 * This function add notification if not exist otherwise update the notification
	 * @param unknown $accountId
	 * @param unknown $notifyType
	 */
	public function manageNotificationSettings($param) {
		if(empty($param['accountId'])) {
			return array();
		}
		
		if(empty($param['notifyType'])) {
			$param['notifyType'] = NotificationSettings::NOTIFY_TYPE_EMAIL;
		}
		
		$emailNotificationDetail = $this->findSingleDetailBy(new NotificationSettings(), array('accountId'=>$param['accountId'], 'notifyType'=>$param['notifyType']));
		if(empty($emailNotificationDetail)) {
			$emailNotificationDetail = $this->addEmailNotification($param);
		}
		
		return $emailNotificationDetail;
	}
	
	/**
	 * This function return all email notification detail list
	 * @param string $period
	 */
	public function getEmailNotificationByPeriod($period) {
		$em = $this->getDoctrine()->getManager();
	
		//$from for entity name (table name)
		$from = "DB\\Bundle\\AppBundle\Entity\\EmailNotification emailNotification ";
	
		$whereCondition = "";
		if(!empty($period)) {
			if(!empty($whereCondition)) {
				$whereCondition .= ' AND ';
			}
			
			$whereCondition .= "emailNotification.period LIKE '%" . $period . "%' ";
		}
	
		$sql = "SELECT emailNotification.emailNotificationId, emailNotification.accountId, emailNotification.receivers, emailNotification.businessType, emailNotification.category, " .
				"emailNotification.period, emailNotification.layout, emailNotification.emailNotificationStatus, emailNotification.lastUpdate, emailNotification.nextSchedule " .
		"FROM " . $from;
	
		if(!empty($whereCondition)) {
			$sql .= " WHERE " . $whereCondition . " ";
		}
	
		$sql .= "ORDER BY emailNotification.emailNotificationId DESC ";
	
		//echo $sql;
	
		$query = $em->createQuery($sql);
		$result = $query->getResult();
	
		return $result;
	}
	
	/**
	 * This function returl all email notfication by category
	 * @param string $category
	 */
	public function getEmailNotificationByCategory($category) {
		if(empty($category)) {
			return array();
		}
		
		$em = $this->getDoctrine()->getManager();
		
		//$from for entity name (table name)
		$from = "DB\\Bundle\\AppBundle\Entity\\EmailNotification emailNotification ";
		
		$whereCondition = "";
		if(!empty($category)) {
			if(!empty($whereCondition)) {
				$whereCondition .= ' AND ';
			}
				
			$whereCondition .= "emailNotification.category LIKE '%" . $category . "%' ";
		}
		
		$sql = "SELECT emailNotification.emailNotificationId, emailNotification.accountId, emailNotification.receivers, emailNotification.businessType, emailNotification.category, " .
				"emailNotification.period, emailNotification.layout, emailNotification.emailNotificationStatus, emailNotification.lastUpdate, emailNotification.nextSchedule " .
				"FROM " . $from;
		
		if(!empty($whereCondition)) {
			$sql .= " WHERE " . $whereCondition . " ";
		}
		
		$sql .= "ORDER BY emailNotification.emailNotificationId DESC ";
		
		//echo $sql;
		
		$query = $em->createQuery($sql);
		$result = $query->getResult();
		
		return $result;
	}
}
?>