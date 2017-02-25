<?php
namespace DB\Bundle\AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use DB\Bundle\CommonBundle\Base\BaseEntity;

/**
 * DB\Bundle\AppBundle\Entity\AccountFrequency
 *
 * @ORM\Table(name="account_param")
 * @ORM\Entity
 */
class AccountParam extends BaseEntity {
	/**
	 * @var integer id
	 * @ORM\Column(name="id", type="bigint", length=10)
	 * @ORM\Id
	 * @ORM\GeneratedValue(strategy="AUTO")
	 */
	private $id;
	
	/**
	 * @var string accountId
	 * @ORM\Column(name="accountId", type="integer", length=10)
	 */
	private $accountId;
	
	/**
	 * @var string discountCode
	 * @ORM\Column(name="discountCode", type="string", length=100)
	 */
	private $discountCode;
	
	/**
	 * @var string sid
	 * @ORM\Column(name="sid", type="string", length=100)
	 */
	private $sid;
	
	/**
	 * @var datetime lastUpdate
	 * @ORM\Column(name="lastUpdate", type="datetime")
	 */
	private $lastUpdate;
	
	/**
	 * Set id
	 *
	 * @param integer id
	 * @return AccountParam
	 */
	public function setId($id) {
		$this->id = $id;
		return $this;
	}
	
	/**
	 * Get id
	 *
	 * @return integer id
	 */
	public function getId() {
		return $this->id;
	}
	
	/**
	 * Set accountId
	 *
	 * @param integer accountId
	 * @return AccountParam
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
	  * Set discountCode
	  *
	  * @param string discountCode
	  * @return AccountParam
	  */
	 public function setDiscountCode($discountCode) {
	 	$this->discountCode = $discountCode;
	 	return $this;
	 }
	 
	 /**
	  * Get discountCode
	  *
	  * @return string discountCode
	  */
	 public function getDiscountCode() {
	 	return $this->discountCode;
	 }
	 
	 /**
	  * Set sid
	  *
	  * @param string sid
	  * @return AccountParam
	  */
	 public function setSid($sid) {
	 	$this->sid = $sid;
	 	return $this;
	 }
	 
	 /**
	  * Get sid
	  *
	  * @return string sid
	  */
	 public function getSid() {
	 	return $this->sid;
	 }
	
	/**
	 * Set lastUpdate
	 *
	 * @param datetime lastUpdate
	 * @return AccountParam
	 */
	public function setLastUpdate($lastUpdate) {
		$this->lastUpdate = $lastUpdate;
		return $this;
	}
	
	/**
	 * Get lastUpdate
	 *
	 * @return datetime lastUpdate
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
		if(isset($this->id)) {
			$idParam['id'] = $this->id;
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