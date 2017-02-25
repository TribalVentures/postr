<?php
namespace DB\Bundle\AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use DB\Bundle\CommonBundle\Base\BaseEntity;

/**
 * Btit\Bundle\AppBundle\Entity\TrendingArticleCategory
 * @ORM\Table(name="trending_article_category")
 * @ORM\Entity
 * @author patildipakr
 *
 */
class TrendingArticleCategory extends BaseEntity {
	/**
	 * @var integer trendingArticleCategoryId
	 * @ORM\Column(name="trendingArticleCategoryId", type="integer", length=10)
	 * @ORM\Id
	 * @ORM\GeneratedValue(strategy="AUTO")
	 */
	private $trendingArticleCategoryId;
	
	/**
	 * @var integer trendingArticleId
	 * @ORM\Column(name="trendingArticleId", type="integer", length=10)
	 */
	private $trendingArticleId;
	
	/**
	 * @var integer categoryId
	 * @ORM\Column(name="categoryId", type="integer", length=10)
	 */
	private $categoryId;
	
	/**
	 * @var integer score
	 * @ORM\Column(name="score", type="integer", length=5)
	 */
	private $score;
	
	/**
	 * Set trendingArticleCategoryId
	 *
	 * @param integer trendingArticleCategoryId
	 * @return TrendingArticleCategory
	 */
	public function setTrendingArticleCategoryId($trendingArticleCategoryId) {
		$this->trendingArticleCategoryId = $trendingArticleCategoryId;
		return $this;
	}
	
	/**
	 * Get trendingArticleCategoryId
	 *
	 * @return integer trendingArticleCategoryId
	 */
	public function getTrendingArticleCategoryId() {
		return $this->trendingArticleCategoryId;
	}
	
	/**
	 * Set trendingArticleId
	 *
	 * @param integer trendingArticleId
	 * @return TrendingArticleCategory
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
	 * Set categoryId
	 *
	 * @param integer categoryId
	 * @return TrendingArticleCategory
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
	 * Set score
	 *
	 * @param integer score
	 * @return TrendingArticleCategory
	 */
	public function setScore($score) {
		$this->score = $score;
		return $this;
	}
	
	/**
	 * Get score
	 *
	 * @return integer score
	 */
	public function getScore() {
		return $this->score;
	}
	
	/**
	 *This method return the primary ID for entity,
	 * Primary ID might be composite ID
	 * @return mixed[] - It return array of primary key
	 */
	public function getPrimaryKey() {
		$idParam = array();
		if(isset($this->trendingArticleCategoryId)) {
			$idParam['trendingArticleCategoryId'] = $this->trendingArticleCategoryId;
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