<?php
namespace DB\Bundle\AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use DB\Bundle\CommonBundle\Base\BaseEntity;

/**
 * DB\Bundle\AppBundle\Entity\SocialPost
 *
 * @ORM\Table(name="social_post")
 * @ORM\Entity
 */
class SocialPost extends BaseEntity {
	/**
	 * @var integer socialPostId
	 * @ORM\Column(name="socialPostId", type="integer", length=10)
	 * @ORM\Id
	 * @ORM\GeneratedValue(strategy="AUTO")
	 */
	private $socialPostId;
	
	/**
	 * @var integer accountId
	 * @ORM\Column(name="accountId", type="integer", length=10)
	 */
	private $accountId;
	
	/**
	 * @var string message
	 * @ORM\Column(name="message", type="string")
	 */
	private $message;
	
	/**
	 * @var string link
	 * @ORM\Column(name="link", type="string")
	 */
	private $link;
	
	/**
	 * @var string facebookPostId
	 * @ORM\Column(name="facebookPostId", type="string", length=100)
	 */
	private $facebookPostId;
	
	 /**
	  * @var string twitterPostId
	  * @ORM\Column(name="twitterPostId", type="string", length=100)
	  */	 
	 private $twitterPostId;
	
	/**
	 * @var integer postStatus
	 * @ORM\Column(name="postStatus", type="smallint", length=1)
	 */
	private $postStatus;
	
	/**
	 * @var integer validStatus
	 * @ORM\Column(name="validStatus", type="smallint", length=1)
	 */
	private $validStatus;
	
	/**
	 * @var integer social
	 * @ORM\Column(name="social", type="integer", length=10)
	 */
	private $social;
	
	 /**
	  * @var string trendingArticleId
	  * @ORM\Column(name="trendingArticleId", type="integer", length=10)
	  */	 
	 private $trendingArticleId;
	
	 /**
	  * @var string articleNotifyHistoryId
	  * @ORM\Column(name="articleNotifyHistoryId", type="integer", length=10)
	  */	 
	 private $articleNotifyHistoryId;
	 
	 /**
	  * @var datetime creationDate
	  * @ORM\Column(name="creationDate", type="datetime")
	  */	 
	 private $creationDate;
	
	 /**
	  * @var datetime lastUpdate
	  * @ORM\Column(name="lastUpdate", type="datetime")
	  */	 
	 private $lastUpdate;
	
	/**
	 * Set socialPostId
	 *
	 * @param integer socialPostId
	 * @return SocialPost
	 */
	public function setSocialPostId($socialPostId) {
		$this->socialPostId = $socialPostId;
		return $this;
	}
	
	/**
	 * Get socialPostId
	 *
	 * @return integer socialPostId
	 */
	public function getSocialPostId() {
		return $this->socialPostId;
	}
	
	/**
	 * Set accountId
	 *
	 * @param integer accountId
	 * @return SocialPost
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
	  * Set message
	  *
	  * @param string message
	  * @return SocialPost
	  */
	 public function setMessage($message) {
	 	$this->message = $message;
	 	return $this;
	 }
	 
	 /**
	  * Get message
	  *
	  * @return string message
	  */
	 public function getMessage() {
	 	return $this->message;
	 }
	 
	 /**
	  * Set link
	  *
	  * @param string link
	  * @return SocialPost
	  */
	 public function setLink($link) {
	 	$this->link = $link;
	 	return $this;
	 }
	 
	 /**
	  * Get link
	  *
	  * @return string link
	  */
	 public function getLink() {
	 	return $this->link;
	 }
	 
	 /**
	  * Set facebookPostId
	  *
	  * @param string facebookPostId
	  * @return SocialPost
	  */
	 public function setFacebookPostId($facebookPostId) {
	 	$this->facebookPostId = $facebookPostId;
	 	return $this;
	 }
	 
	 /**
	  * Get facebookPostId
	  *
	  * @return string facebookPostId
	  */
	 public function getFacebookPostId() {
	 	return $this->facebookPostId;
	 }
	 
	 /**
	  * Set twitterPostId
	  *
	  * @param string twitterPostId
	  * @return SocialPost
	  */
	 public function settWitterPostId($twitterPostId) {
	 	$this->twitterPostId = $twitterPostId;
	 	return $this;
	 }
	 
	 /**
	  * Get twitterPostId
	  *
	  * @return string twitterPostId
	  */
	 public function getTwitterPostId() {
	 	return $this->twitterPostId;
	 }
	
	/**
	 * Set postStatus
	 *
	 * @param integer postStatus
	 * @return SocialPost
	 */
	public function setPostStatus($postStatus) {
		$this->postStatus = $postStatus;
		return $this;
	}
	
	/**
	 * Get postStatus
	 *
	 * @return integer postStatus
	 */
	public function getPostStatus() {
		return $this->postStatus;
	}
	
	/**
	 * Set validStatus
	 *
	 * @param integer validStatus
	 * @return SocialPost
	 */
	public function setValidStatus($validStatus) {
		$this->validStatus = $validStatus;
		return $this;
	}
	
	/**
	 * Get validStatus
	 *
	 * @return integer validStatus
	 */
	public function getValidStatus() {
		return $this->validStatus;
	}
	
	/**
	 * Set social
	 *
	 * @param integer social
	 * @return SocialPost
	 */
	public function setSocial($social) {
		$this->social = $social;
		return $this;
	}
	
	/**
	 * Get social
	 *
	 * @return integer social
	 */
	public function getSocial() {
		return $this->social;
	}
	 
	 /**
	  * Set trendingArticleId
	  *
	  * @param integer trendingArticleId
	  * @return SocialPost
	  */
	 public function setTrendingArticleId($trendingArticleId) {
	 	$this->trendingArticleId = $trendingArticleId;
	 	return $this;
	 }
	 
	 /**
	  * Get trendingArticleId
	  *
	  * @return integer trendingArticleId
	  */
	 public function getTrendingArticleId() {
	 	return $this->trendingArticleId;
	 }
	 
	 /**
	  * Set articleNotifyHistoryId
	  *
	  * @param integer articleNotifyHistoryId
	  * @return SocialPost
	  */
	 public function setArticleNotifyHistoryId($articleNotifyHistoryId) {
	 	$this->articleNotifyHistoryId = $articleNotifyHistoryId;
	 	return $this;
	 }
	 
	 /**
	  * Get articleNotifyHistoryId
	  *
	  * @return integer articleNotifyHistoryId
	  */
	 public function getArticleNotifyHistoryId() {
	 	return $this->articleNotifyHistoryId;
	 }
	 
	 /**
	  * Set creationDate
	  *
	  * @param string creationDate
	  * @return SocialPost
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
	  * Set lastUpdate
	  *
	  * @param string lastUpdate
	  * @return SocialPost
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
		if(isset($this->socialPostId)) {
			$idParam['socialPostId'] = $this->socialPostId;
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