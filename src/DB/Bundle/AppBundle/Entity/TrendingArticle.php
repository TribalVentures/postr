<?php
namespace DB\Bundle\AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use DB\Bundle\CommonBundle\Base\BaseEntity;

/**
 * Btit\Bundle\AppBundle\Entity\TrendingArticle
 * @ORM\Table(name="trending_article")
 * @ORM\Entity
 * @author patildipakr
 *
 */
class TrendingArticle extends BaseEntity {
	const APPROVE_STATUS_APPROVE = '1';
	const APPROVE_STATUS_DISAPPROVE = '0';
	
	/**
	 * @var integer trendingArticleId
	 * @ORM\Column(name="trendingArticleId", type="integer", length=10)
	 * @ORM\Id
	 * @ORM\GeneratedValue(strategy="AUTO")
	 */
	private $trendingArticleId;
	
	/**
	 * @var string postId
	 * @ORM\Column(name="postId", type="string", length=255)
	 */
	private $postId;
	
	/**
	 * @var string category
	 * @ORM\Column(name="category", type="string", length=100)
	 */
	private $category;
	
	/**
	 * @var string url
	 * @ORM\Column(name="url", type="string", length=255)
	 */
	private $url;
	
	/**
	 * @var string title
	 * @ORM\Column(name="title", type="string", length=255)
	 */
	private $title;
	
	/**
	 * @var string image
	 * @ORM\Column(name="image", type="string", length=255)
	 */
	private $image;
	
	/**
	 * @var string description
	 * @ORM\Column(name="description", type="string")
	 */
	private $description;
	
	/**
	 * @var decimal score
	 * @ORM\Column(name="score", type="decimal")
	 */
	private $score;
	
	/**
	 * @var string caption
	 * @ORM\Column(name="caption", type="string")
	 */
	private $caption;
	
	/**
	 * @var smallint trendingArticleStatus
	 * @ORM\Column(name="trendingArticleStatus", type="smallint", length=1)
	 */
	private $trendingArticleStatus;
	
	/**
	 * @var smallint approveStatus
	 * @ORM\Column(name="approveStatus", type="smallint", length=1)
	 */
	private $approveStatus;
	
	/**
	 * @var datetime publicationDate
	 * @ORM\Column(name="publicationDate", type="datetime")
	 */
	private $publicationDate;
	
	/**
	 * @var datetime lastUpdate
	 * @ORM\Column(name="lastUpdate", type="datetime")
	 */
	private $lastUpdate;
	
	/**
	 * Set trendingArticleId
	 *
	 * @param integer trendingArticleId
	 * @return TrendingArticle
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
	 * Set postId
	 *
	 * @param string postId
	 * @return TrendingArticle
	 */
	public function setPostId($postId) {
		$this->postId = $postId;
		return $this;
	}
	
	/**
	 * Get postId
	 *
	 * @return string postId
	 */
	public function getPostId() {
		return $this->postId;
	}
	
	/**
	 * Set category
	 *
	 * @param string category
	 * @return TrendingArticle
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
	 * Set url
	 *
	 * @param string url
	 * @return TrendingArticle
	 */
	public function setUr($url) {
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
	 * Set title
	 *
	 * @param string title
	 * @return TrendingArticle
	 */
	public function setTitle($title) {
		$this->title = $title;
		return $this;
	}
	/**
	 * Get title
	 *
	 * @return string title
	 */
	public function getTitle() {
		return $this->title;
	}
	
	/**
	 * Set image
	 *
	 * @param string image
	 * @return TrendingArticle
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
	 * Set description
	 *
	 * @param string description
	 * @return TrendingArticle
	 */
	public function setDescription($description) {
		$this->description = $description;
		return $this;
	}
	
	/**
	 * Get description
	 *
	 * @return string description
	 */
	public function getDescription() {
		return $this->description;
	}
	
	/**
	 * Set score
	 *
	 * @param decimal score
	 * @return TrendingArticle
	 */
	public function setScore($score) {
		$this->score = $score;
		return $this;
	}
	
	/**
	 * Get score
	 *
	 * @return decimal score
	 */
	public function getScore() {
		return $this->score;
	}
	
	/**
	 * Set caption
	 *
	 * @param string caption
	 * @return TrendingArticle
	 */
	public function setCaption($caption) {
		$this->caption = $caption;
		return $this;
	}
	
	/**
	 * Get caption
	 *
	 * @return string caption
	 */
	public function getCaption() {
		return $this->caption;
	}
	
	/**
	 * Set trendingArticleStatus
	 *
	 * @param smallint trendingArticleStatus
	 * @return TrendingArticle
	 */
	public function setTrendingArticleStatus($trendingArticleStatus) {
		$this->trendingArticleStatus = $trendingArticleStatus;
		return $this;
	}
	
	/**
	 * Get trendingArticleStatus
	 *
	 * @return smallint trendingArticleStatus
	 */
	public function getTrendingArticleStatus() {
		return $this->trendingArticleStatus;
	}
	
	/**
	 * Set approveStatus
	 *
	 * @param smallint approveStatus
	 * @return TrendingArticle
	 */
	public function setApproveStatus($approveStatus) {
		$this->approveStatus = $approveStatus;
		return $this;
	}
	
	/**
	 * Get trendingArticleStatus
	 *
	 * @return smallint trendingArticleStatus
	 */
	public function getApproveStatus() {
		return $this->approveStatus;
	}
	
	/**
	 * Set publicationDate
	 *
	 * @param datetime publicationDate
	 * @return TrendingArticle
	 */
	public function setPublicationDate($publicationDate) {
		$this->publicationDate = $publicationDate;
		return $this;
	}
	
	/**
	 * Get lastUpdate
	 *
	 * @return datetime lastUpdate
	 */
	public function getPublicationDate() {
		return $this->publicationDate;
	}
	
	/**
	 * Set lastUpdate
	 *
	 * @param datetime lastUpdate
	 * @return TrendingArticle
	 */
	public function setLastUpdate($lastUpdate) {
		$this->lastUpdate = $lastUpdate;
		return $this;
	}
	
	/**
	 * Get lastUpdate
	 *
	 * @return datetime lastUpdate
	 */
	public function getLastUpdate() {
		return $this->lastUpdate;
	}
	
	/**
	 *This method return the primary ID for entity,
	 * Primary ID might be composite ID
	 * @return mixed[] - It return array of primary key
	 */
	public function getPrimaryKey() {
		$idParam = array();
		if(isset($this->trendingArticleId)) {
			$idParam['trendingArticleId'] = $this->trendingArticleId;
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