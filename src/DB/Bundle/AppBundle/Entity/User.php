<?php
namespace DB\Bundle\AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use DB\Bundle\CommonBundle\Base\BaseEntity;

/**
 * DB\Bundle\AppBundle\Entity\user
 *
 * @ORM\Table(name="user")
 * @ORM\Entity
 */
class User extends BaseEntity {
	/**
	 * @var integer userId
	 * @ORM\Column(name="userId", type="integer", length=10)
	 * @ORM\Id
	 * @ORM\GeneratedValue(strategy="AUTO")
	 */
	private $userId;
	
	/**
	 * @var integer accountId
	 * @ORM\Column(name="accountId", type="integer", length=10)
	 */
	private $accountId;
	
	/**
	 * @var string firstName
	 * @ORM\Column(name="firstName", type="string", length=50)
	 */
	private $firstName;
	
	/**
	 * @var string lastName
	 * @ORM\Column(name="lastName", type="string", length=50)
	 */
	private $lastName;
	
	/**
	 * @var string email
	 * @ORM\Column(name="email", type="string", length=100)
	 */
	private $email;
	
	/**
	 * @var string password
	 * @ORM\Column(name="password", type="string", length=45)
	 */
	private $password;
	
	/**
	 * @var string profile
	 * @ORM\Column(name="profile", type="string", length=255)
	 */
	private $profile;
	
	/**
	 * @var string fbId
	 * @ORM\Column(name="fbId", type="string", length=100)
	 */
	private $fbId;
	
	/**
	 * @var string senderId
	 * @ORM\Column(name="senderId", type="string", length=100)
	 */
	private $senderId;
	
	/**
	 * @var string twId
	 * @ORM\Column(name="twId", type="string", length=100)
	 */
	private $twId;
	 
	 /**
	  * @var string timezone
	  * @ORM\Column(name="timezone", type="smallint", length=100)
	  */
	 private $timezone;
	 
	 /**
	  * @var smallint userType
	  * @ORM\Column(name="userType", type="smallint", length=1)
	  */
	 private $userType;
	 
	 /**
	  * @var smallint userStatus
	  * @ORM\Column(name="userStatus", type="smallint", length=1)
	  */
	 private $userStatus;
	
	/**
	 * @var string uniqueKey
	 * @ORM\Column(name="uniqueKey", type="string", length=50)
	 */
	private $uniqueKey;
	 
	 /**
	  * @var datetime $lastLoginDate
	  * @ORM\Column(name="lastLoginDate", type="datetime")
	  */
	 private $lastLoginDate;
	
	/**
	 * @var string uniqueToken
	 * @ORM\Column(name="uniqueToken", type="string", length=50)
	 */
	private $uniqueToken;
	 
	 /**
	  * @var datetime tokenValidDate
	  * @ORM\Column(name="tokenValidDate", type="datetime")
	  */
	 private $tokenValidDate;
	 
	 /**
	  * Set userId
	  *
	  * @param integer userId
	  * @return User
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
	  * Set accountId
	  *
	  * @param integer accountId
	  * @return User
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
	  * Set firstName
	  *
	  * @param string firstName
	  * @return User
	  */
	 public function setFirstName($firstName) {
	 	$this->firstName = $firstName;
	 	return $this;
	 }
	 
	 /**
	  * Get firstName
	  *
	  * @return string firstName
	  */
	 public function getFirstName() {
	 	return $this->firstName;
	 }
	 
	 /**
	  * Set lastName
	  *
	  * @param string lastName
	  * @return User
	  */
	 public function setLastName($lastName) {
	 	$this->lastName = $lastName;
	 	return $this;
	 }
	 
	 /**
	  * Get lastName
	  *
	  * @return string lastName
	  */
	 public function getLastName() {
	 	return $this->lastName;
	 }
	 
	 /**
	  * Set email
	  *
	  * @param string email
	  * @return User
	  */
	 public function setEmail($email) {
	 	$this->email = $email;
	 	return $this;
	 }
	 
	 /**
	  * Get email
	  *
	  * @return string email
	  */
	 public function getEmail() {
	 	return $this->email;
	 }
	 
	 /**
	  * Set password
	  *
	  * @param string password
	  * @return User
	  */
	 public function setPassword($password) {
	 	$this->password = $password;
	 	return $this;
	 }
	 
	 /**
	  * Get password
	  *
	  * @return string password
	  */
	 public function getPassword() {
	 	return $this->password;
	 }
	 
	 /**
	  * Set profile
	  *
	  * @param string profile
	  * @return User
	  */
	 public function setProfile($profile) {
	 	$this->profile = $profile;
	 	return $this;
	 }
	 
	 /**
	  * Get profile
	  *
	  * @return string profile
	  */
	 public function getProfile() {
	 	return $this->profile;
	 }
	 
	 /**
	  * Set fbId
	  *
	  * @param string fbId
	  * @return User
	  */
	 public function setFbId($fbId) {
	 	$this->fbId = $fbId;
	 	return $this;
	 }
	 
	 /**
	  * Get fbId
	  *
	  * @return string fbId
	  */
	 public function getFbId() {
	 	return $this->fbId;
	 }
	 
	 /**
	  * Set senderId
	  *
	  * @param string senderId
	  * @return User
	  */
	 public function setSenderId($senderId) {
	 	$this->senderId = $senderId;
	 	return $this;
	 }
	 
	 /**
	  * Get senderId
	  *
	  * @return string senderId
	  */
	 public function getSenderId() {
	 	return $this->senderId;
	 }
	 
	 /**
	  * Set twId
	  *
	  * @param string twId
	  * @return User
	  */
	 public function setTwId($twId) {
	 	$this->twId = $twId;
	 	return $this;
	 }
	 
	 /**
	  * Get twId
	  *
	  * @return string twId
	  */
	 public function getTwId() {
	 	return $this->twId;
	 }
	 
	 /**
	  * Set timezone
	  *
	  * @param string timezone
	  * @return User
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
	  * Set userType
	  *
	  * @param smallint userType
	  * @return User
	  */
	 public function setUserType($userType) {
	 	$this->userType = $userType;
	 	return $this;
	 }
	 
	 /**
	  * Get userType
	  *
	  * @return smallint userType
	  */
	 public function getUserType() {
	 	return $this->userType;
	 }
	 
	 /**
	  * Set userStatus
	  *
	  * @param smallint userStatus
	  * @return User
	  */
	 public function setUserStatus($userStatus) {
	 	$this->userStatus = $userStatus;
	 	return $this;
	 }
	 
	 /**
	  * Get userStatus
	  *
	  * @return smallint userStatus
	  */
	 public function getUserStatus() {
	 	return $this->userStatus;
	 }
	 
	 /**
	  * Set uniqueKey
	  *
	  * @param string uniqueKey
	  * @return User
	  */
	 public function setUniqueKey($uniqueKey) {
	 	$this->uniqueKey = $uniqueKey;
	 	return $this;
	 }
	 
	 /**
	  * Get uniqueKey
	  *
	  * @return string uniqueKey
	  */
	 public function getUniqueKey() {
	 	return $this->uniqueKey;
	 }
	 
	 /**
	  * Set lastLoginDate
	  *
	  * @param datetime lastLoginDate
	  * @return User
	  */
	 public function setLastLoginDate($lastLoginDate) {
	 	$this->lastLoginDate = $lastLoginDate;
	 	return $this;
	 }
	 
	 /**
	  * Get $lastLoginDate
	  *
	  * @return datetime lastLoginDate
	  */
	 public function getLastLoginDate() {
	 	return $this->lastLoginDate;
	 }
	 
	 /**
	  * Set uniqueToken
	  *
	  * @param string uniqueToken
	  * @return User
	  */
	 public function setUniqueToken($uniqueToken) {
	 	$this->uniqueToken = $uniqueToken;
	 	return $this;
	 }
	 
	 /**
	  * Get uniqueToken
	  *
	  * @return string uniqueToken
	  */
	 public function getUniqueToken() {
	 	return $this->uniqueToken;
	 }
	 
	 /**
	  * Set tokenValidDate
	  *
	  * @param datetime tokenValidDate
	  * @return User
	  */
	 public function setTokenValidDate($tokenValidDate) {
	 	$this->tokenValidDate = $tokenValidDate;
	 	return $this;
	 }
	 
	 /**
	  * Get tokenValidDate
	  *
	  * @return datetime tokenValidDate
	  */
	 public function getTokenValidDate() {
	 	return $this->tokenValidDate;
	 }
	 
	 /**
	  * This method return the primary ID for entity,
	  * Primary ID might be composite ID
	  * @return mixed[] - It return array of primary key
	  */
	 public function getPrimaryKey() {
	 	$idParam = array();
	 	if(isset($this->userId)) {
	 		$idParam['userId'] = $this->userId;
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