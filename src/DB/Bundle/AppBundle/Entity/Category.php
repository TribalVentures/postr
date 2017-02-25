<?php
namespace DB\Bundle\AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use DB\Bundle\CommonBundle\Base\BaseEntity;

/**
 * DB\Bundle\AppBundle\Entity\Category
 *
 * @ORM\Table(name="category")
 * @ORM\Entity
 */
class Category extends BaseEntity {
	const CATEGORY_STATUS_ENABLE 	= '0';
	const CATEGORY_STATUS_DISABLE 	= '1';
	
	/**
	 * @var integer categoryId
	 * @ORM\Column(name="categoryId", type="integer", length=10)
	 * @ORM\Id
	 * @ORM\GeneratedValue(strategy="AUTO")
	 */
	private $categoryId;
	/**
	 * @var integer parentCategoryId
	 * @ORM\Column(name="parentCategoryId", type="integer", length=10)
	 */
	private $parentCategoryId;
	
	/**
	 * @var string category
	 * @ORM\Column(name="category", type="string", length=100)
	 */
	private $category;
	
	/**
	 * @var smallint categoryType
	 * @ORM\Column(name="categoryType", type="smallint", length=1)
	 */
	private $categoryType;
	
	/**
	 * @var string image
	 * @ORM\Column(name="image", type="string")
	 */
	private $image;
	
	/**
	 * @var smallint fromTime
	 * @ORM\Column(name="fromTime", type="smallint", length=2)
	 */
	private $fromTime;
	
	/**
	 * @var string language
	 * @ORM\Column(name="language", type="string", length=50)
	 */
	private $language;
	
	/**
	 * @var smallint size
	 * @ORM\Column(name="size", type="smallint", length=3)
	 */
	private $size;
	 
	 /**
	  * @var string sortBy
	  * @ORM\Column(name="sortBy", type="string", length=50)
	  */	 
	 private $sortBy;
	
	/**
	 * @var string includeKeywords
	 * @ORM\Column(name="includeKeywords", type="string")
	 */
	private $includeKeywords;
	
	/**
	 * @var string excludeKeywords
	 * @ORM\Column(name="excludeKeywords", type="string")
	 */
	private $excludeKeywords;
	
	/**
	 * @var string includePublisher
	 * @ORM\Column(name="includePublisher", type="string")
	 */
	private $includePublisher;
	
	/**
	 * @var string excludePublisher
	 * @ORM\Column(name="excludePublisher", type="string")
	 */
	private $excludePublisher;
	
	/**
	 * @var string includeCountry
	 * @ORM\Column(name="includeCountry", type="string")
	 */
	private $includeCountry;
	
	/**
	 * @var string excludeCountry
	 * @ORM\Column(name="excludeCountry", type="string")
	 */
	private $excludeCountry;
	
	/**
	 * @var string includeTopic
	 * @ORM\Column(name="includeTopic", type="string")
	 */
	private $includeTopic;
	
	/**
	 * @var string excludeTopic
	 * @ORM\Column(name="excludeTopic", type="string")
	 */
	private $excludeTopic;
	
	/**
	 * @var smallint categoryStatus
	 * @ORM\Column(name="categoryStatus", type="smallint", length=1)
	 */
	private $categoryStatus;
	
	/**
	 * Set categoryId
	 *
	 * @param integer categoryId
	 * @return Category
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
	 * Set parentCategoryId
	 *
	 * @param integer parentCategoryId
	 * @return Category
	 */
	public function setParentCategoryId($parentCategoryId) {
		$this->parentCategoryId = $parentCategoryId;
		return $this;
	}
	
	/**
	 * Get parentCategoryId
	 *
	 * @return integer parentCategoryId
	 */
	public function getParentCategoryId() {
		return $this->parentCategoryId;
	}
	 
	 /**
	  * Set category
	  *
	  * @param string category
	  * @return Category
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
	  * Set categoryType
	  *
	  * @param smallint categoryType
	  * @return Category
	  */
	 public function setCategoryType($categoryType) {
	 	$this->categoryType = $categoryType;
	 	return $this;
	 }
	 
	 /**
	  * Get categoryType
	  *
	  * @return smallint categoryType
	  */
	 public function getCategoryType() {
	 	return $this->categoryType;
	 }
	 
	 /**
	  * Set image
	  *
	  * @param string image
	  * @return Category
	  */
	 public function setImage($image) {
	 	$this->image = $image;
	 	return $this;
	 }
	 
	 /**
	  * Get image
	  *
	  * @return string image
	  */
	 public function getImage() {
	 	return $this->image;
	 }
	 
	 /**
	  * Set fromTime
	  *
	  * @param smallint fromTime
	  * @return Category
	  */
	 public function setFromTime($fromTime) {
	 	$this->fromTime = $fromTime;
	 	return $this;
	 }
	 
	 /**
	  * Get fromTime
	  *
	  * @return smallint fromTime
	  */
	 public function getFromTime() {
	 	return $this->fromTime;
	 }
	 
	 /**
	  * Set language
	  *
	  * @param string language
	  * @return Category
	  */
	 public function setLanguage($language) {
	 	$this->language = $language;
	 	return $this;
	 }
	 
	 /**
	  * Get language
	  *
	  * @return string language
	  */
	 public function getLanguage() {
	 	return $this->language;
	 }
	 
	 /**
	  * Set size
	  *
	  * @param smallint size
	  * @return Category
	  */
	 public function setSize($size) {
	 	$this->size = $size;
	 	return $this;
	 }
	 
	 /**
	  * Get size
	  *
	  * @return smallint size
	  */
	 public function getSize() {
	 	return $this->size;
	 }
	 
	 /**
	  * Set sortBy
	  *
	  * @param string sortBy
	  * @return Category
	  */
	 public function setSortBy($sortBy) {
	 	$this->sortBy = $sortBy;
	 	return $this;
	 }
	 
	 /**
	  * Get sortBy
	  *
	  * @return string sortBy
	  */
	 public function getSortBy() {
	 	return $this->sortBy;
	 }
	 
	 /**
	  * Set includeKeywords
	  *
	  * @param string includeKeywords
	  * @return Category
	  */
	 public function setIncludeKeywords($includeKeywords) {
	 	$this->includeKeywords = $includeKeywords;
	 	return $this;
	 }
	 
	 /**
	  * Get includeKeywords
	  *
	  * @return string includeKeywords
	  */
	 public function getIncludeKeywords() {
	 	return $this->includeKeywords;
	 }
	 
	 /**
	  * Set excludeKeywords
	  *
	  * @param string excludeKeywords
	  * @return Category
	  */
	 public function setExcludeKeywords($excludeKeywords) {
	 	$this->excludeKeywords = $excludeKeywords;
	 	return $this;
	 }
	 
	 /**
	  * Get excludeKeywords
	  *
	  * @return string excludeKeywords
	  */
	 public function getExcludeKeywords() {
	 	return $this->excludeKeywords;
	 }
	 
	 /**
	  * Set includePublisher
	  *
	  * @param string includePublisher
	  * @return Category
	  */
	 public function setIncludePublisher($includePublisher) {
	 	$this->includePublisher = $includePublisher;
	 	return $this;
	 }
	 
	 /**
	  * Get includePublisher
	  *
	  * @return string includePublisher
	  */
	 public function getIncludePublisher() {
	 	return $this->includePublisher;
	 }
	 
	 /**
	  * Set excludePublisher
	  *
	  * @param string excludePublisher
	  * @return Category
	  */
	 public function setExcludePublisher($excludePublisher) {
	 	$this->excludePublisher = $excludePublisher;
	 	return $this;
	 }
	 
	 /**
	  * Get excludePublisher
	  *
	  * @return string excludePublisher
	  */
	 public function getExcludePublisher() {
	 	return $this->excludePublisher;
	 }
	 
	 /**
	  * Set includeCountry
	  *
	  * @param string includeCountry
	  * @return Category
	  */
	 public function setIncludeCountry($includeCountry) {
	 	$this->includeCountry = $includeCountry;
	 	return $this;
	 }
	 
	 /**
	  * Get includeCountry
	  *
	  * @return string includeCountry
	  */
	 public function getIncludeCountry() {
	 	return $this->includeCountry;
	 }
	 
	 /**
	  * Set excludeCountry
	  *
	  * @param string excludeCountry
	  * @return Category
	  */
	 public function setExcludeCountry($excludeCountry) {
	 	$this->excludeCountry = $excludeCountry;
	 	return $this;
	 }
	 
	 /**
	  * Get excludeCountry
	  *
	  * @return string excludeCountry
	  */
	 public function getExcludeCountry() {
	 	return $this->excludeCountry;
	 }
	 
	 /**
	  * Set includeTopic
	  *
	  * @param string includeTopic
	  * @return Category
	  */
	 public function setIncludeTopic($includeTopic) {
	 	$this->includeTopic = $includeTopic;
	 	return $this;
	 }
	 
	 /**
	  * Get includeTopic
	  *
	  * @return string includeTopic
	  */
	 public function getIncludeTopic() {
	 	return $this->includeTopic;
	 }
	 
	 /**
	  * Set excludeTopic
	  *
	  * @param string excludeTopic
	  * @return Category
	  */
	 public function setExcludeTopic($excludeTopic) {
	 	$this->excludeTopic = $excludeTopic;
	 	return $this;
	 }
	 
	 /**
	  * Get excludeTopic
	  *
	  * @return string excludeTopic
	  */
	 public function getExcludeTopic() {
	 	return $this->excludeTopic;
	 }
	 
	 /**
	  * Set categoryStatus
	  *
	  * @param smallint categoryStatus
	  * @return Category
	  */
	 public function setCategoryStatus($categoryStatus) {
	 	$this->categoryStatus = $categoryStatus;
	 	return $this;
	 }
	 
	 /**
	  * Get categoryStatus
	  *
	  * @return smallint categoryStatus
	  */
	 public function getCategoryStatus() {
	 	return $this->categoryStatus;
	 }
	
	/**
	 * This method return the primary ID for entity,
	 * Primary ID might be composite ID
	 * @return mixed[] - It return array of primary key
	 */
	public function getPrimaryKey() {
		$idParam = array();
		if(isset($this->categoryId)) {
			$idParam['categoryId'] = $this->categoryId;
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