<?php
namespace DB\Bundle\AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use DB\Bundle\CommonBundle\Base\BaseEntity;

/**
 * DB\Bundle\AppBundle\Entity\SocialProfile
 *
 * @ORM\Table(name="social_profile")
 * @ORM\Entity
 */
class SocialProfile extends BaseEntity {
	const PROFILE_TYPE_FACEBOOK = 'Facebook';
	const PROFILE_TYPE_TWITTER = 'Twitter';
	
	/**
	 * @var integer socialProfileId
	 * @ORM\Column(name="socialProfileId", type="integer", length=10)
	 * @ORM\Id
	 * @ORM\GeneratedValue(strategy="AUTO")
	 */
	private $socialProfileId;
	
	/**
	 * @var integer accountId
	 * @ORM\Column(name="accountId", type="integer", length=10)
	 */
	private $accountId;
	
	/**
	 * @var string profileType
	 * @ORM\Column(name="profileType", type="string", length=45)
	 */
	private $profileType;
	
	/**
	 * @var string socialId
	 * @ORM\Column(name="socialId", type="string", length=100)
	 */
	private $socialId;
	
	/**
	 * @var string accessToken
	 * @ORM\Column(name="accessToken", type="string")
	 */
	private $accessToken;
	
	/**
	 * @var string oauthToken
	 * @ORM\Column(name="oauthToken", type="string")
	 */
	private $oauthToken;
	
	/**
	 * @var string oauthTokenSecret
	 * @ORM\Column(name="oauthTokenSecret", type="string")
	 */
	private $oauthTokenSecret;
	
	 /**
	  * @var string name
	  * @ORM\Column(name="name", type="string", length=255)
	  */	 
	 private $name;
	
	/**
	 * @var string picture
	 * @ORM\Column(name="picture", type="string")
	 */
	private $picture;
	
	 /**
	  * @var string category
	  * @ORM\Column(name="category", type="string", length=100)
	  */	 
	 private $category;
	 
	 /**
	  * @var datetime creationDate
	  * @ORM\Column(name="creationDate", type="datetime")
	  */	 
	 private $creationDate;
	
	/**
	 * Set socialProfileId
	 *
	 * @param integer socialProfileId
	 * @return SocialProfile
	 */
	public function setSocialProfileId($socialProfileId) {
		$this->socialProfileId = $socialProfileId;
		return $this;
	}
	
	/**
	 * Get socialProfileId
	 *
	 * @return integer socialProfileId
	 */
	public function getSocialProfileId() {
		return $this->socialProfileId;
	}
	
	/**
	 * Set accountId
	 *
	 * @param integer accountId
	 * @return SocialProfile
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
	  * Set profileType
	  *
	  * @param string profileType
	  * @return SocialProfile
	  */
	 public function setProfileType($profileType) {
	 	$this->profileType = $profileType;
	 	return $this;
	 }
	 
	 /**
	  * Get profileType
	  *
	  * @return string profileType
	  */
	 public function getProfileType() {
	 	return $this->profileType;
	 }
	 
	 /**
	  * Set socialId
	  *
	  * @param string socialId
	  * @return SocialProfile
	  */
	 public function setSocialId($socialId) {
	 	$this->socialId = $socialId;
	 	return $this;
	 }
	 
	 /**
	  * Get socialId
	  *
	  * @return string socialId
	  */
	 public function getSocialId() {
	 	return $this->socialId;
	 }
	 
	 /**
	  * Set accessToken
	  *
	  * @param string accessToken
	  * @return SocialProfile
	  */
	 public function setAccessToken($accessToken) {
	 	$this->accessToken = $accessToken;
	 	return $this;
	 }
	 
	 /**
	  * Get accessToken
	  *
	  * @return string accessToken
	  */
	 public function getAccessToken() {
	 	return $this->accessToken;
	 }
	 
	 /**
	  * Set oauthToken
	  *
	  * @param string oauthToken
	  * @return SocialProfile
	  */
	 public function setOauthToken($oauthToken) {
	 	$this->oauthToken = $oauthToken;
	 	return $this;
	 }
	 
	 /**
	  * Get oauthToken
	  *
	  * @return string oauthToken
	  */
	 public function getOauthToken() {
	 	return $this->oauthToken;
	 }
	 
	 /**
	  * Set oauthTokenSecret
	  *
	  * @param string oauthTokenSecret
	  * @return SocialProfile
	  */
	 public function setOauthTokenSecret($oauthTokenSecret) {
	 	$this->oauthTokenSecret = $oauthTokenSecret;
	 	return $this;
	 }
	 
	 /**
	  * Get oauthTokenSecret
	  *
	  * @return string oauthTokenSecret
	  */
	 public function getOauthTokenSecret() {
	 	return $this->oauthTokenSecret;
	 }
	 
	 /**
	  * Set name
	  *
	  * @param string name
	  * @return SocialProfile
	  */
	 public function setName($name) {
	 	$this->name = $name;
	 	return $this;
	 }
	 
	 /**
	  * Get name
	  *
	  * @return string name
	  */
	 public function getName() {
	 	return $this->name;
	 }
	 
	 /**
	  * Set picture
	  *
	  * @param string picture
	  * @return SocialProfile
	  */
	 public function setPicture($picture) {
	 	$this->picture = $picture;
	 	return $this;
	 }
	 
	 /**
	  * Get picture
	  *
	  * @return string picture
	  */
	 public function getPicture() {
	 	return $this->picture;
	 }
	 
	 /**
	  * Set category
	  *
	  * @param string category
	  * @return SocialProfile
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
	  * Set creationDate
	  *
	  * @param string creationDate
	  * @return SocialProfile
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
		if(isset($this->socialProfileId)) {
			$idParam['socialProfileId'] = $this->socialProfileId;
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