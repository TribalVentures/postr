<?php
namespace DB\Bundle\AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use DB\Bundle\CommonBundle\Base\BaseEntity;

/**
 * DB\Bundle\AppBundle\Entity\AccountCategory
 *
 * @ORM\Table(name="account_category")
 * @ORM\Entity
 */
class AccountCategory extends BaseEntity {
	/**
	 * @var integer accountCategotyId
	 * @ORM\Column(name="accountCategotyId", type="integer", length=10)
	 * @ORM\Id
	 * @ORM\GeneratedValue(strategy="AUTO")
	 */
	private $accountCategotyId;
	
	/**
	 * @var string accountId
	 * @ORM\Column(name="accountId", type="integer", length=10)
	 */
	private $accountId;
	
	/**
	 * @var string categoryId
	 * @ORM\Column(name="categoryId", type="integer", length=10)
	 */
	private $categoryId;
	
	/**
	 * Set accountCategotyId
	 *
	 * @param integer accountCategotyId
	 * @return AccountCategory
	 */
	public function setAccountCategotyId($accountCategotyId) {
		$this->accountCategotyId = $accountCategotyId;
		return $this;
	}
	
	/**
	 * Get accountCategotyId
	 *
	 * @return integer accountCategotyId
	 */
	public function getAccountCategotyId() {
		return $this->accountCategotyId;
	}
	
	/**
	 * Set accountId
	 *
	 * @param integer accountId
	 * @return AccountCategory
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
	  * Set categoryId
	  *
	  * @param integer categoryId
	  * @return AccountCategory
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
	 * This method return the primary ID for entity,
	 * Primary ID might be composite ID
	 * @return mixed[] - It return array of primary key
	 */
	public function getPrimaryKey() {
		$idParam = array();
		if(isset($this->accountCategotyId)) {
			$idParam['accountCategotyId'] = $this->accountCategotyId;
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