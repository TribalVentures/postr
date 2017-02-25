<?php
namespace DB\Bundle\AppBundle\DAO;

use DB\Bundle\CommonBundle\Base\BaseDAO;
use DB\Bundle\AppBundle\Entity\ArticleNotifyHistory;

/**
 * Class For ArticleNotifyHistoryDAO DAO, This class is responsible for manage database 
 * operation for account table/entity
 *
 * @namespace DB\Bundle\AppBundle\DAO
 *
 * @author Dipak Patil
 */
class ArticleNotifyHistoryDAO extends BaseDAO { 
	/**
	 * Always need doctrim object to initilise ArticleNotifyHistoryDAO dao object
	 * @param $_dm - Doctrime object
	 */
	function __construct($_dm) {
		parent :: __construct($_dm);
	}
	
	/**
	 * This function add new email history
	 * @param array $trendingArticleDetail
	 */
	public function addArticleNotifyHistory($articleNotifyHistoryDetail = array()) {
		if(empty($articleNotifyHistoryDetail)) {
			return array();
		}
		
		//Set defautl data
		if(empty($articleNotifyHistoryDetail['accountId'])) {
			$articleNotifyHistoryDetail['accountId'] = '0';
		}
		
		if(empty($articleNotifyHistoryDetail['notificationSettingsId'])) {
			$articleNotifyHistoryDetail['notificationSettingsId'] = '0';
		}
		
		if(empty($articleNotifyHistoryDetail['trendingArticleId'])) {
			$articleNotifyHistoryDetail['trendingArticleId'] = '0';
		}
		
		if(empty($articleNotifyHistoryDetail['notifyType'])) {
			$articleNotifyHistoryDetail['notifyType'] = ArticleNotifyHistory::NOTIFY_TYPE_SUGGESTED;
		}
		
		if(empty($articleNotifyHistoryDetail['creationDate'])) {
			$articleNotifyHistoryDetail['creationDate'] = new \DateTime();
		}
		
		$articleNotifyHistory = new ArticleNotifyHistory();
		
		$articleNotifyHistory->setAccountId($articleNotifyHistoryDetail['accountId']);
		$articleNotifyHistory->setNotificationSettingsId($articleNotifyHistoryDetail['notificationSettingsId']);
		$articleNotifyHistory->setTrendingArticleId($articleNotifyHistoryDetail['trendingArticleId']);
		$articleNotifyHistory->setNotifyType($articleNotifyHistoryDetail['notifyType']);
		$articleNotifyHistory->setCreationDate($articleNotifyHistoryDetail['creationDate']);
		
		$articleNotifyHistory = $this->save($articleNotifyHistory);
		
		$newDetail = false;
		if(is_object($articleNotifyHistory)) {
			$newDetail = $articleNotifyHistory->toArray();
		}
		
		return $newDetail;
	}
	
	/**
	 * This function add new email history
	 * @param array $trendingArticleDetail
	 */
	public function addEmailArticleNotifyHistory($accountId, $trendingArticleList) {
		if(!isset($trendingArticleList) ||  !is_array($trendingArticleList) || !isset($accountId) || empty($accountId)) {
			return array();
		}
		
		$saveList = array();
		foreach($trendingArticleList as $trendingArticle) {
			$articleNotifyHistory = new ArticleNotifyHistory();
			$articleNotifyHistory->setAccountId($accountId);
			$articleNotifyHistory->setNotificationSettingsId(0);
			$articleNotifyHistory->setNotifyType(ArticleNotifyHistory::NOTIFY_TYPE_EMAIL);
			$articleNotifyHistory->setTrendingArticleId($trendingArticle['trendingArticleId']);
			$articleNotifyHistory->setCreationDate(new \DateTime());
			
			$saveList[] = $articleNotifyHistory;
		}
		
		$this->saveBatch($saveList);
	}
	
	/**
	 * This function return campaign List
	 * @param string $search
	 * @param number $currentPage
	 * @return mixed[]  list of campaign and pagination
	 */
	public function getTrendingArticleList($emailNotificationId = '', $noOfDay = 5) {
		$em = $this->getDoctrine()->getManager();
	
		//$from for entity name (table name)
		$from = "DB\\Bundle\\AppBundle\Entity\\EmailHistory emailHistory ";
		
		$startDate = date('Y-m-d H:i:s', strtotime('-' . $noOfDay . ' day', strtotime(date())));
		$endDate = date('Y-m-d H:i:s');
		
		$whereCondition = "emailHistory.emailNotificationId = '" . $emailNotificationId . "' ";
		$whereCondition .= " AND emailHistory.creationDate >= '" . $startDate . "' AND emailHistory.creationDate <= '" . $endDate . "' ";
	
		$sql = "SELECT emailHistory.emailHistoryId, emailHistory.emailNotificationId, emailHistory.postId, emailHistory.sendStatus, emailHistory.creationDate " .
		"FROM " . $from;
	
		if(!empty($whereCondition)) {
			$sql .= " WHERE " . $whereCondition . " ";
		}
	
		$sql .= "ORDER BY emailHistory.creationDate DESC ";
	
		//echo $sql;
	
		$query = $em->createQuery($sql);
		$result = $query->getResult();
	
		return $result;
	}
	
	/**
	 * This function add new email history
	 * @param array $trendingArticleDetail
	 */
	public function addTrendingArticle($emailHistoryDetail = array()) {
		if(empty($emailHistoryDetail)) {
			return false;
		}
		
		//Set defautl data
		if(empty($emailHistoryDetail['emailNotificationId'])) {
			$emailHistoryDetail['emailNotificationId'] = '0';
		}
		
		if(empty($emailHistoryDetail['postId'])) {
			$emailHistoryDetail['postId'] = '0';
		}
		
		if(empty($emailHistoryDetail['sendStatus'])) {
			$emailHistoryDetail['sendStatus'] = '0';
		}
		
		if(empty($emailHistoryDetail['creationDate'])) {
			$emailHistoryDetail['creationDate'] = new \DateTime();
		}
		
		$emailHistory = new EmailHistory();
		
		$emailHistory->setEmailHistoryId($emailHistoryDetail['emailNotificationId']);
		$emailHistory->setPostId($emailHistoryDetail['postId']);
		$emailHistory->setSendStatus($emailHistoryDetail['sendStatus']);
		$emailHistory->setCreationDate($emailHistoryDetail['creationDate']);
		
		$this->save($emailHistory);
		
		$newDetail = false;
		if(is_object($emailHistory)) {
			$newDetail = $emailHistory->toArray();
		}
		return $newDetail;
	}
	
	/**
	 * This function return campaign List
	 * @param string $search
	 * @param number $currentPage
	 * @return mixed[]  list of campaign and pagination
	 */
	public function removeEmailHistory($date = '') {
		$em = $this->getDoctrine()->getManager();
	
		//$from for entity name (table name)
		$from = "DB\\Bundle\\AppBundle\Entity\\EmailHistory emailHistory ";
	
		if(empty($date)) {
			$date = date("Y-m-d H:i:s");
		}
		
		$whereCondition = "emailHistory.creationDate < '" . $date . "' ";
	
		$sql = 'DELETE FROM ' . $from ;
	
		if(!empty($whereCondition)) {
			$sql .= " WHERE " . $whereCondition . " ";
		}
	
		$query = $em->createQuery($sql);
	
		$result = $query->getResult();
	
		return $result;
	}
	
	/**
	 * This function remove all old articles of no of day ago
	 * @param number $noOfDays
	 */
	public function removeEmailHistoryByDays($noOfDays = 2) {
		$startDate = date('Y-m-d H:i:s', strtotime('-' . $noOfDays . ' day', strtotime(date('Y-m-d H:i:s'))));
		
		return $this->removeEmailHistory($startDate);
	}
	
	/**
	 * This function return campaign List
	 * @param string $search
	 * @param number $currentPage
	 * @return mixed[]  list of campaign and pagination
	 */
	public function removeEmailHistoryByAccount($accountId = '') {
		$em = $this->getDoctrine()->getManager();
	
		$notificationSql = 'SELECT emailNotification.emailNotificationId FROM DB\\Bundle\\AppBundle\Entity\\EmailNotification emailNotification ';
		$notificationSql .= 'WHERE emailNotification.accountId = ' . $accountId;
		
		//$from for entity name (table name)
		$from = "DB\\Bundle\\AppBundle\Entity\\EmailHistory emailHistory ";
	
		$whereCondition = " emailHistory.emailNotificationId IN (" .$notificationSql . ") ";
	
		$sql = 'DELETE FROM ' . $from ;
	
		if(!empty($whereCondition)) {
			$sql .= " WHERE " . $whereCondition . " ";
		}
	
		$query = $em->createQuery($sql);
	
		$result = $query->getResult();
	
		return $result;
	}
	
	/**
	 * This function set article sttus to used i.e set to 1
	 */
	public function changeEmailHistorySendStatus($sendStatus = 1, $cehckSendStatus = 0, $postId = '', $period = '') {
		$em = $this->getDoctrine()->getManager();
	
		//$from for entity name (table name)
		$from = "DB\\Bundle\\AppBundle\Entity\\EmailHistory emailHistory ";
		
		$whereCondition = 'emailHistory.sendStatus = ' . $cehckSendStatus;
		
		if(!empty($postId)) {
			$whereCondition .= " AND emailHistory.postId = '" . $postId . "' ";
		} else {
			/* if(!empty($period)) {
				$subSQL = 'SELECT DISTINCT trendingArticle.postId ' ;
				$subSQL .= 'FROM DB\\Bundle\\AppBundle\Entity\\EmailHistory emailHistory1, DB\\Bundle\\AppBundle\Entity\\TrendingArticle trendingArticle ';
				$subSQL .= "WHERE emailHistory1.postId = trendingArticle.postId AND emailHistory1.sendStatus = " . $cehckSendStatus . " AND trendingArticle.period LIKE '%" . $period . "%' ";
				
				$whereCondition .= ' AND emailHistory.postId IN (' . $subSQL . ')';
			} */
		}
		
		$sql = 'UPDATE ' . $from . ' SET emailHistory.sendStatus = ' . $sendStatus . ' WHERE ' . $whereCondition;
	
		$query = $em->createQuery($sql);
	
		$result = $query->getResult();
	
		return $result;
	}
	
	/**
	 * This function create email history for post based on category
	 * @param string $category
	 * @param string $postId
	 * @param integer $sendStatus
	 */
	public function addArtileInEmailHistory($category, $postId, $sendStatus = 0) {
		//Get all email notification for category
		$enailNotificationDAO = new EmailNotificationDAO($this->getDoctrine());
		$emailNotificationList = $enailNotificationDAO->getEmailNotificationByCategory($category);
		
		if(!empty($emailNotificationList)) {
			$emailHistoryList = array();
			foreach($emailNotificationList as $emailNotification) {
				$emailHistory = new EmailHistory();
				$emailHistory->setEmailNotificationId($emailNotification['emailNotificationId']);
				$emailHistory->setPostId($postId);
				$emailHistory->setSendStatus($sendStatus);
				$emailHistory->setCreationDate(new \DateTime());
					
				$emailHistoryList[] = $emailHistory;
			}
			if(!empty($emailHistoryList)) {
				$this->saveBatch($emailHistoryList);
			}
		}
	}
}
?>