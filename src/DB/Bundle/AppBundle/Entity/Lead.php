<?php
namespace DB\Bundle\AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use DB\Bundle\CommonBundle\Base\BaseEntity;

/**
 * DB\Bundle\AppBundle\Entity\Lead
 *
 * @ORM\Table(name="lead")
 * @ORM\Entity
 */
class Lead extends BaseEntity {
	/**
	 * @var integer id
	 * @ORM\Column(name="id", type="bigint", length=15)
	 * @ORM\Id
	 * @ORM\GeneratedValue(strategy="AUTO")
	 */
	private $id;
	
	/**
	 * @var string objectId
	 * @ORM\Column(name="objectId", type="string", length=50)
	 */
	private $objectId;
	
	/**
	 * @var string company
	 * @ORM\Column(name="company", type="string", length=150)
	 */
	private $company;
	
	/**
	 * @var string contactPerson
	 * @ORM\Column(name="contactPerson", type="string", length=150)
	 */
	private $contactPerson;
	
	/**
	 * @var string phone
	 * @ORM\Column(name="phone", type="string", length=40)
	 */
	private $phone;
	
	/**
	 * @var string email
	 * @ORM\Column(name="email", type="string", length=150)
	 */
	private $email;
	
	/**
	 * @var string streetAddress
	 * @ORM\Column(name="streetAddress", type="string", length=100)
	 */
	private $streetAddress;
	
	/**
	 * @var string locality
	 * @ORM\Column(name="locality", type="string", length=100)
	 */
	private $locality;
	
	/**
	 * @var string region
	 * @ORM\Column(name="region", type="string", length=100)
	 */
	private $region;
	
	/**
	 * @var string postalCode
	 * @ORM\Column(name="postalCode", type="string", length=30)
	 */
	private $postalCode;
	
	/**
	 * @var string country
	 * @ORM\Column(name="country", type="string", length=45)
	 */
	private $country;
	
	/**
	 * @var string url
	 * @ORM\Column(name="url", type="string", length=255)
	 */
	private $url;
	
	/**
	 * @var string houzzUrl
	 * @ORM\Column(name="houzzUrl", type="string", length=255)
	 */
	private $houzzUrl;
	 
	 /**
	  * @var smallint leadStatus
	  * @ORM\Column(name="leadStatus", type="smallint", length=1)
	  */
	 private $leadStatus;
	 
	 /**
	  * Set id
	  *
	  * @param bigint id
	  * @return Lead
	  */
	 public function setId($id) {
	 	$this->id = $id;
	 	return $this;
	 }
	 
	 /**
	  * Get id
	  *
	  * @return bigint id
	  */
	 public function getId() {
	 	return $this->id;
	 }
	 
	 /**
	  * Set objectId
	  *
	  * @param string objectId
	  * @return Lead
	  */
	 public function setObjectId($objectId) {
	 	$this->objectId = $objectId;
	 	return $this;
	 }
	 
	 /**
	  * Get objectId
	  *
	  * @return string objectId
	  */
	 public function getObjectId() {
	 	return $this->objectId;
	 }
	 
	 /**
	  * Set company
	  *
	  * @param string company
	  * @return Lead
	  */
	 public function setCompany($company) {
	 	$this->company = $company;
	 	return $this;
	 }
	 
	 /**
	  * Get company
	  *
	  * @return string company
	  */
	 public function getCompany() {
	 	return $this->company;
	 }
	 
	 /**
	  * Set contactPerson
	  *
	  * @param string contactPerson
	  * @return Lead
	  */
	 public function setContactPerson($contactPerson) {
	 	$this->contactPerson = $contactPerson;
	 	return $this;
	 }
	 
	 /**
	  * Get contactPerson
	  *
	  * @return string contactPerson
	  */
	 public function getContactPerson() {
	 	return $this->contactPerson;
	 }
	 
	 /**
	  * Set phone
	  *
	  * @param string phone
	  * @return Lead
	  */
	 public function setPhone($phone) {
	 	$this->phone = $phone;
	 	return $this;
	 }
	 
	 /**
	  * Get phone
	  *
	  * @return string phone
	  */
	 public function getPhone() {
	 	return $this->phone;
	 }
	 
	 /**
	  * Set email
	  *
	  * @param string email
	  * @return Lead
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
	  * Set streetAddress
	  *
	  * @param string streetAddress
	  * @return Lead
	  */
	 public function setStreetAddress($streetAddress) {
	 	$this->streetAddress = $streetAddress;
	 	return $this;
	 }
	 
	 /**
	  * Get streetAddress
	  *
	  * @return string streetAddress
	  */
	 public function getStreetAddress() {
	 	return $this->streetAddress;
	 }
	 
	 /**
	  * Set locality
	  *
	  * @param string locality
	  * @return Lead
	  */
	 public function setLocality($locality) {
	 	$this->locality = $locality;
	 	return $this;
	 }
	 
	 /**
	  * Get locality
	  *
	  * @return string locality
	  */
	 public function getLocality() {
	 	return $this->locality;
	 }
	 
	 /**
	  * Set region
	  *
	  * @param string region
	  * @return Lead
	  */
	 public function setRegion($region) {
	 	$this->region = $region;
	 	return $this;
	 }
	 
	 /**
	  * Get region
	  *
	  * @return string region
	  */
	 public function getRegion() {
	 	return $this->region;
	 }
	 
	 /**
	  * Set postalCode
	  *
	  * @param string postalCode
	  * @return Lead
	  */
	 public function setPostalCode($postalCode) {
	 	$this->postalCode = $postalCode;
	 	return $this;
	 }
	 
	 /**
	  * Get postalCode
	  *
	  * @return string postalCode
	  */
	 public function getPostalCode() {
	 	return $this->postalCode;
	 }
	 
	 /**
	  * Set country
	  *
	  * @param string country
	  * @return Lead
	  */
	 public function setCountry($country) {
	 	$this->country = $country;
	 	return $this;
	 }
	 
	 /**
	  * Get country
	  *
	  * @return string country
	  */
	 public function getCountry() {
	 	return $this->country;
	 }
	 
	 /**
	  * Set url
	  *
	  * @param string url
	  * @return Lead
	  */
	 public function setUrl($url) {
	 	$this->url = $url;
	 	return $this;
	 }
	 
	 /**
	  * Get url
	  *
	  * @return string url
	  */
	 public function getUrl() {
	 	return $this->url;
	 }
	 
	 /**
	  * Set houzzUrl
	  *
	  * @param string houzzUrl
	  * @return Lead
	  */
	 public function setHouzzUrl($houzzUrl) {
	 	$this->houzzUrl = $houzzUrl;
	 	return $this;
	 }
	 
	 /**
	  * Get houzzUrl
	  *
	  * @return string houzzUrl
	  */
	 public function getHouzzUrl() {
	 	return $this->houzzUrl;
	 }
	 
	 /**
	  * Set leadStatus
	  *
	  * @param smallint leadStatus
	  * @return Lead
	  */
	 public function setLeadStatus($leadStatus) {
	 	$this->leadStatus = $leadStatus;
	 	return $this;
	 }
	 
	 /**
	  * Get leadStatus
	  *
	  * @return smallint leadStatus
	  */
	 public function getLeadStatus() {
	 	return $this->leadStatus;
	 }
	 
	 /**
	  * This method return the primary ID for entity,
	  * Primary ID might be composite ID
	  * @return mixed[] - It return array of primary key
	  */
	 public function getPrimaryKey() {
	 	$idParam = array();
	 	if(isset($this->id)) {
	 		$idParam['id'] = $this->id;
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