<?php
namespace DB\Bundle\AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use DB\Bundle\CommonBundle\Base\BaseEntity;

/**
 * Btit\Bundle\AppBundle\Entity\EmailHistory
 * @ORM\Table(name="email_history")
 * @ORM\Entity
 * @author patildipakr
 *
 */
class EmailHistory extends BaseEntity {
	/**
	 * @var integer emailHistoryId
	 * @ORM\Column(name="emailHistoryId", type="bigint", length=15)
	 * @ORM\Id
	 * @ORM\GeneratedValue(strategy="AUTO")
	 */
	private $emailHistoryId;
	
	/**
	 * @var string accountId
	 * @ORM\Column(name="accountId", type="integer", length=10)
	 */
	private $accountId;
	
	/**
	 * @var datetime creationDate
	 * @ORM\Column(name="creationDate", type="datetime")
	 */
	private $creationDate;
	
	/**
	 * Set emailHistoryId
	 *
	 * @param integer emailHistoryId
	 * @return EmailHistory
	 */
	public function setEmailHistoryId($emailHistoryId) {
		$this->emailHistoryId = $emailHistoryId;
		return $this;
	}
	/**
	 * Get emailHistoryId
	 *
	 * @return integer emailHistoryId
	 */
	public function getEmailHistoryId() {
		return $this->emailHistoryId;
	}
	
	/**
	 * Set accountId
	 *
	 * @param integer accountId
	 * @return EmailHistory
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
	 * Set creationDate
	 *
	 * @param datetime creationDate
	 * @return EmailHistory
	 */
	public function setCreationDate($creationDate) {
		$this->creationDate = $creationDate;
		return $this;
	}
	
	/**
	 * Get creationDate
	 *
	 * @return datetime creationDate
	 */
	public function getCreationDate() {
		return $this->creationDate;
	}
	
	/**
	 *This method return the primary ID for entity,
	 * Primary ID might be composite ID
	 * @return mixed[] - It return array of primary key
	 */
	public function getPrimaryKey() {
		$idParam = array();
		if(isset($this->emailHistoryId)) {
			$idParam['emailHistoryId'] = $this->emailHistoryId;
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