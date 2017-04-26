<?php
namespace DB\Bundle\AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use DB\Bundle\CommonBundle\Base\BaseEntity;

/**
 * DB\Bundle\AppBundle\Entity\Account
 *
 * @ORM\Table(name="account")
 * @ORM\Entity
 */
class Account extends BaseEntity {
	/**
	 * @var integer accountId
	 * @ORM\Column(name="accountId", type="integer", length=10)
	 * @ORM\Id
	 * @ORM\GeneratedValue(strategy="AUTO")
	 */
	private $accountId;
	
	/**
	 * @var string account
	 * @ORM\Column(name="account", type="string", length=60)
	 */
	private $account;
	
	/**
	 * @var string businessTypeId
	 * @ORM\Column(name="businessTypeId", type="integer", length=10)
	 */
	private $businessTypeId;
	
	/**
	 * @var string categoryId
	 * @ORM\Column(name="categoryId", type="integer", length=10)
	 */
	private $categoryId;
	
	 /**
	  * @var datetime creationDate
	  * @ORM\Column(name="creationDate", type="date")
	  */	 
	 private $creationDate;
	 
	 /**
	  * @var datetime $endDate
	  * @ORM\Column(name="endDate", type="date")
	  */	 
	 private $endDate;
	 
	 /**
	  * @var string apiKey
	  * @ORM\Column(name="apiKey", type="string", length=50)
	  */	 
	 private $apiKey;
	 
	 /**
	  * @var smallint accountStatus
	  * @ORM\Column(name="accountStatus", type="smallint", length=1)
	  */
	 private $accountStatus;
	 
	 /**
	  * @var datetime $lastActionDate
	  * @ORM\Column(name="lastActionDate", type="datetime")
	  */	 
	 private $lastActionDate;
	 
	 /**
	  * @var string btCustomerId
	  * @ORM\Column(name="btCustomerId", type="string", length=100)
	  */	 
	 private $btCustomerId;
	 
	 /**
	  * @var string btCardtoken
	  * @ORM\Column(name="btCardtoken", type="string", length=100)
	  */	 
	 private $btCardtoken;
	 
	 /**
	  * @var string btPaymentMethod
	  * @ORM\Column(name="btPaymentMethod", type="string", length=32)
	  */	 
	 private $btPaymentMethod;
	 
	 /**
	  * @var string btPaypalEmail
	  * @ORM\Column(name="btPaypalEmail", type="string", length=255)
	  */	 
	 private $btPaypalEmail;
	 
	 /**
	  * @var string btCreditCardNo
	  * @ORM\Column(name="btCreditCardNo", type="string", length=50)
	  */	 
	 private $btCreditCardNo;
	 
	 /**
	  * @var string btCardtoken
	  * @ORM\Column(name="btExpirationDate", type="string", length=10)
	  */	 
	 private $btExpirationDate;
	 
	 /**
	  * @var string btCardType
	  * @ORM\Column(name="btCardType", type="string", length=50)
	  */	 
	 private $btCardType;
	 
	 /**
	  * @var string btPlanId
	  * @ORM\Column(name="btPlanId", type="string", length=100)
	  */	 
	 private $btPlanId;
	 
	 /**
	  * @var string btSubscriptionId
	  * @ORM\Column(name="btSubscriptionId", type="string", length=100)
	  */	 
	 private $btSubscriptionId;
	
	/**
	 * Set accountId
	 *
	 * @param integer accountId
	 * @return Account
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
	  * Set account
	  *
	  * @param string account
	  * @return Account
	  */
	 public function setAccount($account) {
	 	$this->account = $account;
	 	return $this;
	 }
	 
	 /**
	  * Get account
	  *
	  * @return string account
	  */
	 public function getAccount() {
	 	return $this->account;
	 }
	 
	 /**
	  * Set businessTypeId
	  *
	  * @param integer businessTypeId
	  * @return Account
	  */
	 public function setBusinessTypeId($businessTypeId) {
	 	$this->businessTypeId = $businessTypeId;
	 	return $this;
	 }
	 
	 /**
	  * Get businessTypeId
	  *
	  * @return integer businessTypeId
	  */
	 public function getBusinessTypeId() {
	 	return $this->businessTypeId;
	 }
	 
	 /**
	  * Set categoryId
	  *
	  * @param integer categoryId
	  * @return Account
	  */
	 public function setCategoryId($categoryId) {
	 	$this->categoryId = $categoryId;
	 	return $this;
	 }
	 
	 /**
	  * Get categoryId
	  *
	  * @return integer categoryId
	  */
	 public function getCategoryId() {
	 	return $this->categoryId;
	 }
	 
	 /**
	  * Set creationDate
	  *
	  * @param string creationDate
	  * @return Account
	  */
	 public function setCreationDate($creationDate) {
	 	$this->creationDate = $creationDate;
	 	return $this;
	 }
	 
	 /**
	  * Get creationDate
	  *
	  * @return string creationDate
	  */
	 public function getCreationDate() {
	 	return $this->creationDate;
	 }
	 
	 /**
	  * Set endDate
	  *
	  * @param string endDate
	  * @return Account
	  */
	 public function setEndDate($endDate) {
	 	$this->endDate = $endDate;
	 	return $this;
	 }
	 
	 /**
	  * Get endDate
	  *
	  * @return string endDate
	  */
	 public function getEndDate() {
	 	return $this->endDate;
	 }
	 
	 /**
	  * Set apiKey
	  *
	  * @param string apiKey
	  * @return Account
	  */
	 public function setApiKey($apiKey) {
	 	$this->apiKey = $apiKey;
	 	return $this;
	 }
	 
	 /**
	  * Get apiKey
	  *
	  * @return string apiKey
	  */
	 public function getApiKey() {
	 	return $this->apiKey;
	 }
	 
	 /**
	  * Set accountStatus
	  *
	  * @param smallint accountStatus
	  * @return Account
	  */
	 public function setAccountStatus($accountStatus) {
	 	$this->accountStatus = $accountStatus;
	 	return $this;
	 }
	 
	 /**
	  * Get accountStatus
	  *
	  * @return smallint accountStatus
	  */
	 public function getAccountStatus() {
	 	return $this->accountStatus;
	 }
	 
	 /**
	  * Set lastActionDate
	  *
	  * @param datetime lastActionDate
	  * @return Account
	  */
	 public function setLastActionDate($lastActionDate) {
	 	$this->lastActionDate = $lastActionDate;
	 	return $this;
	 }
	 
	 /**
	  * Get lastActionDate
	  *
	  * @return datetime lastActionDate
	  */
	 public function getLastActionDate() {
	 	return $this->lastActionDate;
	 }
	 
	 /**
	  * Set btCustomerId
	  *
	  * @param string btCustomerId
	  * @return Account
	  */
	 public function setBtCustomerId($btCustomerId) {
	 	$this->btCustomerId = $btCustomerId;
	 	return $this;
	 }
	 
	 /**
	  * Get btCustomerId
	  *
	  * @return string btCustomerId
	  */
	 public function getBtCustomerId() {
	 	return $this->btCustomerId;
	 }
	 
	 /**
	  * Set btCardtoken
	  *
	  * @param string btCardtoken
	  * @return Account
	  */
	 public function setBtCardtoken($btCardtoken) {
	 	$this->btCardtoken = $btCardtoken;
	 	return $this;
	 }
	 
	 /**
	  * Get btCardtoken
	  *
	  * @return string btCardtoken
	  */
	 public function getBtCardtoken() {
	 	return $this->btCardtoken;
	 }
	 
	 /**
	  * Set btPaymentMethod
	  *
	  * @param string btPaymentMethod
	  * @return Account
	  */
	 public function setBtPaymentMethod($btPaymentMethod) {
	 	$this->btPaymentMethod = $btPaymentMethod;
	 	return $this;
	 }
	 
	 /**
	  * Get btPaymentMethod
	  *
	  * @return string btPaymentMethod
	  */
	 public function getBtPaymentMethod() {
	 	return $this->btPaymentMethod;
	 }
	 
	 /**
	  * Set btPaypalEmail
	  *
	  * @param string btPaypalEmail
	  * @return Account
	  */
	 public function setBtPaypalEmail($btPaypalEmail) {
	 	$this->btPaypalEmail = $btPaypalEmail;
	 	return $this;
	 }
	 
	 /**
	  * Get btPaypalEmail
	  *
	  * @return string btPaypalEmail
	  */
	 public function getBtPaypalEmail() {
	 	return $this->btPaypalEmail;
	 }
	 
	 /**
	  * Set btCreditCardNo
	  *
	  * @param string btCreditCardNo
	  * @return Account
	  */
	 public function setBtCreditCardNo($btCreditCardNo) {
	 	$this->btCreditCardNo = $btCreditCardNo;
	 	return $this;
	 }
	 
	 /**
	  * Get btCreditCardNo
	  *
	  * @return string btCreditCardNo
	  */
	 public function getBtCreditCardNo() {
	 	return $this->btCreditCardNo;
	 }
	 
	 /**
	  * Set btExpirationDate
	  *
	  * @param string btExpirationDate
	  * @return Account
	  */
	 public function setBtExpirationDate($btExpirationDate) {
	 	$this->btExpirationDate = $btExpirationDate;
	 	return $this;
	 }
	 
	 /**
	  * Get btExpirationDate
	  *
	  * @return string btExpirationDate
	  */
	 public function getBtExpirationDate() {
	 	return $this->btExpirationDate;
	 }
	 
	 /**
	  * Set btCardType
	  *
	  * @param string btCardType
	  * @return Account
	  */
	 public function setBtCardType($btCardType) {
	 	$this->btCardType = $btCardType;
	 	return $this;
	 }
	 
	 /**
	  * Get btCardType
	  *
	  * @return string btCardType
	  */
	 public function getBtCardType() {
	 	return $this->btCardType;
	 }
	 
	 /**
	  * Set btPlanId
	  *
	  * @param string btPlanId
	  * @return Account
	  */
	 public function setBtPlanId($btPlanId) {
	 	$this->btPlanId = $btPlanId;
	 	return $this;
	 }
	 
	 /**
	  * Get btPlanId
	  *
	  * @return string btPlanId
	  */
	 public function getBtPlanId() {
	 	return $this->btPlanId;
	 }
	 
	 /**
	  * Set btSubscriptionId
	  *
	  * @param string btSubscriptionId
	  * @return Account
	  */
	 public function setBtSubscriptionId($btSubscriptionId) {
	 	$this->btSubscriptionId = $btSubscriptionId;
	 	return $this;
	 }
	 
	 /**
	  * Get btSubscriptionId
	  *
	  * @return string btSubscriptionId
	  */
	 public function getBtSubscriptionId() {
	 	return $this->btSubscriptionId;
	 }
	
	/**
	 * This method return the primary ID for entity,
	 * Primary ID might be composite ID
	 * @return mixed[] - It return array of primary key
	 */
	public function getPrimaryKey() {
		$idParam = array();
		if(isset($this->accountId)) {
			$idParam['accountId'] = $this->accountId;
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