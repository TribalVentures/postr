<?php
namespace DB\Bundle\AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use DB\Bundle\CommonBundle\Base\BaseEntity;

/**
 * DB\Bundle\AppBundle\Entity\SocialPostMetric
 *
 * @ORM\Table(name="social_post_metric")
 * @ORM\Entity
 */
class SocialPostMetric extends BaseEntity {
	/**
	 * @var integer socialPostMetricId
	 * @ORM\Column(name="socialPostMetricId", type="integer", length=10)
	 * @ORM\Id
	 * @ORM\GeneratedValue(strategy="AUTO")
	 */
	private $socialPostMetricId;
	
	/**
	 * @var integer accountId
	 * @ORM\Column(name="accountId", type="integer", length=10)
	 */
	private $accountId;
	
	/**
	 * @var integer socialPostId
	 * @ORM\Column(name="socialPostId", type="integer", length=10)
	 */
	private $socialPostId;
	
	/**
	 * @var integer social
	 * @ORM\Column(name="social", type="integer", length=10)
	 */
	private $social;
	
	/**
	 * @var integer socialIncrease
	 * @ORM\Column(name="socialIncrease", type="integer", length=10)
	 */
	private $socialIncrease;
	
	/**
	 * @var integer fbSocial
	 * @ORM\Column(name="fbSocial", type="integer", length=10)
	 */
	private $fbSocial;
	
	/**
	 * @var integer fbLike
	 * @ORM\Column(name="fbLike", type="integer", length=10)
	 */
	private $fbLike;
	
	/**
	 * @var integer fbShare
	 * @ORM\Column(name="fbShare", type="integer", length=10)
	 */
	private $fbShare;
	
	/**
	 * @var integer fbComment
	 * @ORM\Column(name="fbComment", type="integer", length=10)
	 */
	private $fbComment;
	
	/**
	 * @var integer twLike
	 * @ORM\Column(name="twLike", type="integer", length=10)
	 */
	private $twLike;
	 
	 /**
	  * @var datetime creationDate
	  * @ORM\Column(name="creationDate", type="date")
	  */	 
	 private $creationDate;
	
	/**
	 * Set socialPostMetricId
	 *
	 * @param integer socialPostMetricId
	 * @return SocialPostMetric
	 */
	public function setSocialPostMetricId($socialPostMetricId) {
		$this->socialPostMetricId = $socialPostMetricId;
		return $this;
	}
	
	/**
	 * Get socialPostMetricId
	 *
	 * @return integer socialPostMetricId
	 */
	public function getSocialPostMetricId() {
		return $this->socialPostMetricId;
	}
	
	/**
	 * Set accountId
	 *
	 * @param integer accountId
	 * @return SocialPostMetric
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
	  * Set socialPostId
	  *
	  * @param integer socialPostId
	  * @return SocialPostMetric
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
	  * Set social
	  *
	  * @param integer social
	  * @return SocialPostMetric
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
	  * Set socialIncrease
	  *
	  * @param integer socialIncrease
	  * @return SocialPostMetric
	  */
	 public function setSocialIncrease($socialIncrease) {
	 	$this->socialIncrease = $socialIncrease;
	 	return $this;
	 }
	 
	 /**
	  * Get socialIncrease
	  *
	  * @return integer socialIncrease
	  */
	 public function getSocialIncrease() {
	 	return $this->socialIncrease;
	 }
	 
	 /**
	  * Set fbSocial
	  *
	  * @param integer fbSocial
	  * @return SocialPostMetric
	  */
	 public function setFbSocial($fbSocial) {
	 	$this->fbSocial = $fbSocial;
	 	return $this;
	 }
	 
	 /**
	  * Get fbSocial
	  *
	  * @return integer fbSocial
	  */
	 public function getFbSocial() {
	 	return $this->fbSocial;
	 }
	 
	 /**
	  * Set fbLike
	  *
	  * @param integer fbLike
	  * @return SocialPostMetric
	  */
	 public function setFbLike($fbLike) {
	 	$this->fbLike = $fbLike;
	 	return $this;
	 }
	 
	 /**
	  * Get fbLike
	  *
	  * @return integer fbLike
	  */
	 public function getFbLike() {
	 	return $this->fbLike;
	 }
	 
	 /**
	  * Set fbShare
	  *
	  * @param integer fbShare
	  * @return SocialPostMetric
	  */
	 public function setFbShare($fbShare) {
	 	$this->fbShare = $fbShare;
	 	return $this;
	 }
	 
	 /**
	  * Get fbShare
	  *
	  * @return integer fbShare
	  */
	 public function getFbShare() {
	 	return $this->fbShare;
	 }
	
	/**
	 * Set fbComment
	 *
	 * @param integer fbComment
	 * @return SocialPostMetric
	 */
	public function setFbComment($fbComment) {
		$this->fbComment = $fbComment;
		return $this;
	}
	
	/**
	 * Get fbComment
	 *
	 * @return integer fbComment
	 */
	public function getFbComment() {
		return $this->fbComment;
	}
	
	/**
	 * Set twLike
	 *
	 * @param integer twLike
	 * @return SocialPostMetric
	 */
	public function setTwLike($twLike) {
		$this->twLike = $twLike;
		return $this;
	}
	
	/**
	 * Get twLike
	 *
	 * @return integer twLike
	 */
	public function getTwLike() {
		return $this->twLike;
	}
	 
	 /**
	  * Set creationDate
	  *
	  * @param \DateTime creationDate
	  * @return SocialPostMetric
	  */
	 public function setCreationDate($creationDate) {
	 	$this->creationDate = $creationDate;
	 	return $this;
	 }
	 
	 /**
	  * Get creationDate
	  *
	  * @return \DateTime creationDate
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
		if(isset($this->socialPostMetricId)) {
			$idParam['socialPostMetricId'] = $this->socialPostMetricId;
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