<?php
namespace DB\Bundle\AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use DB\Bundle\CommonBundle\Base\BaseEntity;

/**
 * DB\Bundle\AppBundle\Entity\Transaction
 *
 * @ORM\Table(name="transaction")
 * @ORM\Entity
 */
class Transaction extends BaseEntity {
	/**
	 * @var integer transactionId
	 * @ORM\Column(name="transactionId", type="integer", length=10)
	 * @ORM\Id
	 * @ORM\GeneratedValue(strategy="AUTO")
	 */
	private $transactionId;
	
	/**
	 * @var string accountId
	 * @ORM\Column(name="accountId", type="integer", length=10)
	 */
	private $accountId;
	
	/**
	 * @var string btTransactionId
	 * @ORM\Column(name="btTransactionId", type="string", length=100)
	 */
	private $btTransactionId;
	
	/**
	 * @var string billingPeriod
	 * @ORM\Column(name="billingPeriod", type="string", length=50)
	 */
	private $billingPeriod;
	
	/**
	 * @var decimal amount
	 * @ORM\Column(name="amount", type="decimal")
	 */
	private $amount;
	
	/**
	 * @var string transactionStatus
	 * @ORM\Column(name="transactionStatus", type="string", length=45)
	 */
	private $transactionStatus;
	
	 /**
	  * @var datetime creationDate
	  * @ORM\Column(name="creationDate", type="datetime")
	  */	 
	 private $creationDate;

	 /**
	  * Set transactionId
	  *
	  * @param integer transactionId
	  * @return Transaction
	  */
	 public function setTtransactionId($transactionId) {
	 	$this->transactionId = $transactionId;
	 	return $this;
	 }
	 
	 /**
	  * Get transactionId
	  *
	  * @return integer transactionId
	  */
	 public function getTransactionId() {
	 	return $this->transactionId;
	 }
	
	/**
	 * Set accountId
	 *
	 * @param integer accountId
	 * @return Transaction
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
	  * Set btTransactionId
	  *
	  * @param string btTransactionId
	  * @return Transaction
	  */
	 public function setBtTransactionId($btTransactionId) {
	 	$this->btTransactionId = $btTransactionId;
	 	return $this;
	 }
	 
	 /**
	  * Get btTransactionId
	  *
	  * @return string btTransactionId
	  */
	 public function getBtTransactionId() {
	 	return $this->btTransactionId;
	 }
	 
	 /**
	  * Set billingPeriod
	  *
	  * @param string billingPeriod
	  * @return Transaction
	  */
	 public function setBillingPeriod($billingPeriod) {
	 	$this->billingPeriod = $billingPeriod;
	 	return $this;
	 }
	 
	 /**
	  * Get billingPeriod
	  *
	  * @return string billingPeriod
	  */
	 public function getBillingPeriod() {
	 	return $this->billingPeriod;
	 }
	 
	 /**
	  * Set amount
	  *
	  * @param decimal amount
	  * @return Transaction
	  */
	 public function setAmount($amount) {
	 	$this->amount = $amount;
	 	return $this;
	 }
	 
	 /**
	  * Get amount
	  *
	  * @return decimal amount
	  */
	 public function getAmount() {
	 	return $this->amount;
	 }
	 
	 /**
	  * Set transactionStatus
	  *
	  * @param string transactionStatus
	  * @return Transaction
	  */
	 public function setTransactionStatus($transactionStatus) {
	 	$this->transactionStatus = $transactionStatus;
	 	return $this;
	 }
	 
	 /**
	  * Get accountStatus
	  *
	  * @return string accountStatus
	  */
	 public function getTransactionStatus() {
	 	return $this->transactionStatus;
	 }
	 	 
	 /**
	  * Set creationDate
	  *
	  * @param string creationDate
	  * @return Transaction
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
	 * This method return the primary ID for entity,
	 * Primary ID might be composite ID
	 * @return mixed[] - It return array of primary key
	 */
	public function getPrimaryKey() {
		$idParam = array();
		if(isset($this->transactionId)) {
			$idParam['transactionId'] = $this->transactionId;
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