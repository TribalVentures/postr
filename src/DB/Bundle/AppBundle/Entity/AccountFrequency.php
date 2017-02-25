<?php
namespace DB\Bundle\AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use DB\Bundle\CommonBundle\Base\BaseEntity;

/**
 * DB\Bundle\AppBundle\Entity\AccountFrequency
 *
 * @ORM\Table(name="account_frequency")
 * @ORM\Entity
 */
class AccountFrequency extends BaseEntity {
	const ACCOUNT_FREQUENCY_CATEGORY_AUTOPILOT 		= 'Autopilot';
	const ACCOUNT_FREQUENCY_CATEGORY_MANUAL_POST 	= 'Manual Posts';
	
	/**
	 * @var integer accountFrequencyId
	 * @ORM\Column(name="accountFrequencyId", type="integer", length=10)
	 * @ORM\Id
	 * @ORM\GeneratedValue(strategy="AUTO")
	 */
	private $accountFrequencyId;
	
	/**
	 * @var string accountId
	 * @ORM\Column(name="accountId", type="integer", length=10)
	 */
	private $accountId;
	
	/**
	 * @var string category
	 * @ORM\Column(name="category", type="string", length=50)
	 */
	private $category;
	
	/**
	 * @var string frequency
	 * @ORM\Column(name="frequency", type="string", length=10)
	 */
	private $frequency;
	
	/**
	 * @var string timezone
	 * @ORM\Column(name="timezone", type="string", length=100)
	 */
	private $timezone;
	
	/**
	 * Set accountFrequencyId
	 *
	 * @param integer accountFrequencyId
	 * @return AccountFrequency
	 */
	public function setAccountFrequencyId($accountFrequencyId) {
		$this->accountFrequencyId = $accountFrequencyId;
		return $this;
	}
	
	/**
	 * Get accountFrequencyId
	 *
	 * @return integer accountFrequencyId
	 */
	public function getAccountFrequencyId() {
		return $this->accountFrequencyId;
	}
	
	/**
	 * Set accountId
	 *
	 * @param integer accountId
	 * @return AccountFrequency
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
	  * Set category
	  *
	  * @param string category
	  * @return AccountFrequency
	  */
	 public function setCategory($category) {
	 	$this->category = $category;
	 	return $this;
	 }
	 
	 /**
	  * Get category
	  *
	  * @return string category
	  */
	 public function getCategory() {
	 	return $this->category;
	 }
	 
	 /**
	  * Set frequency
	  *
	  * @param string frequency
	  * @return AccountFrequency
	  */
	 public function setFrequency($frequency) {
	 	$this->frequency = $frequency;
	 	return $this;
	 }
	 
	 /**
	  * Get frequency
	  *
	  * @return string frequency
	  */
	 public function getFrequency() {
	 	return $this->frequency;
	 }
	 
	 /**
	  * Set timezone
	  *
	  * @param string timezone
	  * @return AccountFrequency
	  */
	 public function setTimezone($timezone) {
	 	$this->timezone = $timezone;
	 	return $this;
	 }
	 
	 /**
	  * Get timezone
	  *
	  * @return string timezone
	  */
	 public function getTimezone() {
	 	return $this->timezone;
	 }
	
	/**
	 * This method return the primary ID for entity,
	 * Primary ID might be composite ID
	 * @return mixed[] - It return array of primary key
	 */
	public function getPrimaryKey() {
		$idParam = array();
		if(isset($this->accountFrequencyId)) {
			$idParam['accountFrequencyId'] = $this->accountFrequencyId;
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