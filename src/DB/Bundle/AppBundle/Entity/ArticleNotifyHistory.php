<?php
namespace DB\Bundle\AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use DB\Bundle\CommonBundle\Base\BaseEntity;

/**
 * Btit\Bundle\AppBundle\Entity\ArticleNotifyHistory
 * @ORM\Table(name="article_notify_history")
 * @ORM\Entity
 * @author patildipakr
 *
 */
class ArticleNotifyHistory extends BaseEntity {
	const NOTIFY_TYPE_EMAIL 	= 1;
	const NOTIFY_TYPE_MESSENGER = 2;
	const NOTIFY_TYPE_SUGGESTED = 3;
	const NOTIFY_TYPE_AUTOPOST 	= 4;
	
	/**
	 * @var integer articleNotifyHistoryId
	 * @ORM\Column(name="articleNotifyHistoryId", type="integer", length=10)
	 * @ORM\Id
	 * @ORM\GeneratedValue(strategy="AUTO")
	 */
	private $articleNotifyHistoryId;
	
	/**
	 * @var string accountId
	 * @ORM\Column(name="accountId", type="integer", length=10)
	 */
	private $accountId;
	
	/**
	 * @var integer notificationSettingsId
	 * @ORM\Column(name="notificationSettingsId", type="integer", length=10)
	 */
	private $notificationSettingsId;
	
	/**
	 * @var integer trendingArticleId
	 * @ORM\Column(name="trendingArticleId", type="integer", length=10)
	 */
	private $trendingArticleId;

	/**
	 * @var string notifyType
	 * @ORM\Column(name="notifyType", type="smallint", length=1)
	 */
	private $notifyType;
	
	/**
	 * @var datetime creationDate
	 * @ORM\Column(name="creationDate", type="datetime")
	 */
	private $creationDate;
	
	/**
	 * Set articleNotifyHistoryId
	 *
	 * @param integer articleNotifyHistoryId
	 * @return ArticleNotifyHistory
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
	 * Set accountId
	 *
	 * @param integer accountId
	 * @return ArticleNotifyHistory
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
	 * Set notificationSettingsId
	 *
	 * @param integer notificationSettingsId
	 * @return ArticleNotifyHistory
	 */
	public function setNotificationSettingsId($notificationSettingsId) {
		$this->notificationSettingsId = $notificationSettingsId;
		return $this;
	}
	
	/**
	 * Get notificationSettingsId
	 *
	 * @return integer notificationSettingsId
	 */
	public function getNotificationSettingsId() {
		return $this->notificationSettingsId;
	}
	
	/**
	 * Set trendingArticleId
	 *
	 * @param integer trendingArticleId
	 * @return ArticleNotifyHistory
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
	  * Set notifyType
	  *
	  * @param string notifyType
	  * @return ArticleNotifyHistory
	  */
	 public function setNotifyType($notifyType) {
	 	$this->notifyType = $notifyType;
	 	return $this;
	 }
	 
	 /**
	  * Get notifyType
	  *
	  * @return integer notifyType
	  */
	 public function getNotifyType() {
	 	return $this->notifyType;
	 }
	
	/**
	 * Set creationDate
	 *
	 * @param datetime creationDate
	 * @return ArticleNotifyHistory
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
		if(isset($this->articleNotifyHistoryId)) {
			$idParam['articleNotifyHistoryId'] = $this->articleNotifyHistoryId;
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