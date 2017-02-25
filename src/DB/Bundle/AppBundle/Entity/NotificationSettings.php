<?php
namespace DB\Bundle\AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use DB\Bundle\CommonBundle\Base\BaseEntity;

/**
 * DB\Bundle\AppBundle\Entity\NotificationSettings
 *
 * @ORM\Table(name="notification_settings")
 * @ORM\Entity
 */
class NotificationSettings extends BaseEntity {
	const NOTIFY_TYPE_EMAIL = 1;
	const NOTIFY_TYPE_MESSENGER = 2;
	const NOTIFY_TYPE_SUGGESTED = 3;
	
	/**
	 * @var integer notificationSettingsId
	 * @ORM\Column(name="notificationSettingsId", type="integer", length=10)
	 * @ORM\Id
	 * @ORM\GeneratedValue(strategy="AUTO")
	 */
	private $notificationSettingsId;
	
	/**
	 * @var integer accountId
	 * @ORM\Column(name="accountId", type="integer", length=10)
	 */
	private $accountId;
	
	/**
	 * @var string receivers
	 * @ORM\Column(name="receivers", type="string")
	 */
	private $receivers;
	
	/**
	 * @var string period
	 * @ORM\Column(name="period", type="string", length=100)
	 */
	private $period;

	/**
	 * @var string notifyType
	 * @ORM\Column(name="notifyType", type="smallint", length=1)
	 */
	private $notifyType;
	
	 /**
	  * @var datetime lastUpdate
	  * @ORM\Column(name="lastUpdate", type="datetime")
	  */	 
	 private $lastUpdate;
	
	/**
	 * Set notificationSettingsId
	 *
	 * @param integer notificationSettingsId
	 * @return NotificationSettings
	 */
	public function setNotificationSettingsId($notificationSettingsId) {
		$this->notificationSettingsId = $notificationSettingsId;
		return $this;
	}
	
	/**
	 * Get notificationSettingsId
	 *
	 * @return integer notificationSettingsId
	 */
	public function getNotificationSettingsId() {
		return $this->notificationSettingsId;
	}
	
	/**
	 * Set accountId
	 *
	 * @param integer accountId
	 * @return Advertiser
	 */
	public function setAccountId($accountId) {
		$this->accountId = $accountId;
		return $this;
	}
	
	/**
	 * Get accountId
	 *
	 * @return integer accountId
	 */
	public function getAccountId() {
		return $this->accountId;
	}
	 
	 /**
	  * Set receivers
	  *
	  * @param string receivers
	  * @return NotificationSettings
	  */
	 public function setReceivers($receivers) {
	 	$this->receivers = $receivers;
	 	return $this;
	 }
	 
	 /**
	  * Get receivers
	  *
	  * @return string receivers
	  */
	 public function getReceivers() {
	 	return $this->receivers;
	 }
	 
	 /**
	  * Set period
	  *
	  * @param string period
	  * @return EmailNotification
	  */
	 public function setPeriod($period) {
	 	$this->period = $period;
	 	return $this;
	 }
	 
	 /**
	  * Get period
	  *
	  * @return string period
	  */
	 public function getPeriod() {
	 	return $this->period;
	 }
	 
	 /**
	  * Set notifyType
	  *
	  * @param string notifyType
	  * @return NotificationSettings
	  */
	 public function setNotifyType($notifyType) {
	 	$this->notifyType = $notifyType;
	 	return $this;
	 }
	 
	 /**
	  * Get notifyType
	  *
	  * @return integer notifyType
	  */
	 public function getNotifyType() {
	 	return $this->notifyType;
	 }
	 
	 /**
	  * Set lastUpdate
	  *
	  * @param string lastUpdate
	  * @return NotificationSettings
	  */
	 public function setLastUpdate($lastUpdate) {
	 	$this->lastUpdate = $lastUpdate;
	 	return $this;
	 }
	 
	 /**
	  * Get lastUpdate
	  *
	  * @return \DateTime lastUpdate
	  */
	 public function getLastUpdate() {
	 	return $this->lastUpdate;
	 }
	
	/**
	 * This method return the primary ID for entity,
	 * Primary ID might be composite ID
	 * @return mixed[] - It return array of primary key
	 */
	public function getPrimaryKey() {
		$idParam = array();
		if(isset($this->articleNotifyHistoryd)) {
			$idParam['notificationSettingsId'] = $this->notificationSettingsId;
		}
		return $idParam;
	}

	/**
	 * This function return all fields as a properties
	 *
	 * @return mixed[]
	 */
	public function getProperty() {
		return get_object_vars($this);
	}
}
?>