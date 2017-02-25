<?php
namespace DB\Bundle\AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use DB\Bundle\CommonBundle\Base\BaseEntity;

/**
 * DB\Bundle\AppBundle\Entity\UserFeedback
 *
 * @ORM\Table(name="user_feedback")
 * @ORM\Entity
 */
class UserFeedback extends BaseEntity {
	/**
	 * @var integer feedbackId
	 * @ORM\Column(name="feedbackId", type="integer", length=10)
	 * @ORM\Id
	 * @ORM\GeneratedValue(strategy="AUTO")
	 */
	private $feedbackId;
	
	/**
	 * @var integer accountId
	 * @ORM\Column(name="accountId", type="integer", length=10)
	 */
	private $accountId;
	
	/**
	 * @var integer userId
	 * @ORM\Column(name="userId", type="integer", length=10)
	 */
	private $userId;
	
	/**
	 * @var string subject
	 * @ORM\Column(name="subject", type="string")
	 */
	private $subject;
	
	/**
	 * @var string feedback
	 * @ORM\Column(name="feedback", type="string")
	 */
	private $feedback;
	
	/**
	 * @var string note
	 * @ORM\Column(name="note", type="string")
	 */
	private $note;
	
	/**
	 * @var string comment
	 * @ORM\Column(name="comment", type="string")
	 */
	private $comment;
	 
	 /**
	  * @var datetime creationDate
	  * @ORM\Column(name="creationDate", type="datetime")
	  */
	 private $creationDate;
	 
	 /**
	  * Set feedbackId
	  *
	  * @param integer feedbackId
	  * @return UserFeedback
	  */
	 public function setFeedbackId($feedbackId) {
	 	$this->feedbackId = $feedbackId;
	 	return $this;
	 }
	 
	 /**
	  * Get feedbackId
	  *
	  * @return integer feedbackId
	  */
	 public function getFeedbackId() {
	 	return $this->feedbackId;
	 }
	 
	 /**
	  * Set accountId
	  *
	  * @param integer accountId
	  * @return UserFeedback
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
	  * Set userId
	  *
	  * @param integer userId
	  * @return UserFeedback
	  */
	 public function setUserId($userId) {
	 	$this->userId = $userId;
	 	return $this;
	 }
	 
	 /**
	  * Get userId
	  *
	  * @return integer userId
	  */
	 public function getUserId() {
	 	return $this->userId;
	 }
	 
	 /**
	  * Set subject
	  *
	  * @param string subject
	  * @return UserFeedback
	  */
	 public function setSubject($subject) {
	 	$this->subject = $subject;
	 	return $this;
	 }
	 
	 /**
	  * Get subject
	  *
	  * @return string subject
	  */
	 public function getSubject() {
	 	return $this->subject;
	 }
	 
	 /**
	  * Set feedback
	  *
	  * @param string feedback
	  * @return UserFeedback
	  */
	 public function setFeedback($feedback) {
	 	$this->feedback = $feedback;
	 	return $this;
	 }
	 
	 /**
	  * Get feedback
	  *
	  * @return string feedback
	  */
	 public function getFeedback() {
	 	return $this->feedback;
	 }
	 
	 /**
	  * Set note
	  *
	  * @param string note
	  * @return UserFeedback
	  */
	 public function setNote($note) {
	 	$this->note = $note;
	 	return $this;
	 }
	 
	 /**
	  * Get note
	  *
	  * @return string note
	  */
	 public function getNote() {
	 	return $this->note;
	 }
	 
	 /**
	  * Set comment
	  *
	  * @param string comment
	  * @return UserFeedback
	  */
	 public function setComment($comment) {
	 	$this->comment = $comment;
	 	return $this;
	 }
	 
	 /**
	  * Get comment
	  *
	  * @return string comment
	  */
	 public function getComment() {
	 	return $this->comment;
	 }
	 
	 /**
	  * Set creationDate
	  *
	  * @param datetime creationDate
	  * @return UserFeedback
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
	  * This method return the primary ID for entity,
	  * Primary ID might be composite ID
	  * @return mixed[] - It return array of primary key
	  */
	 public function getPrimaryKey() {
	 	$idParam = array();
	 	if(isset($this->feedbackId)) {
	 		$idParam['feedbackId'] = $this->feedbackId;
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