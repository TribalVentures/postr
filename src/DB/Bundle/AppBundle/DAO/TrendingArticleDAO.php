<?php
namespace DB\Bundle\AppBundle\DAO;

use DB\Bundle\CommonBundle\Base\BaseDAO;
use DB\Bundle\AppBundle\Entity\TrendingArticle;
use DB\Bundle\CommonBundle\Util\DBUtil;
use DB\Bundle\AppBundle\Common\Config;
use DB\Bundle\AppBundle\Entity\Account;
use DB\Bundle\CommonBundle\ApiClient\DBSendgridClient;
use DB\Bundle\AppBundle\Entity\Category;
use DB\Bundle\AppBundle\Entity\SocialProfile;
use DB\Bundle\AppBundle\Entity\User;
use DB\Bundle\AppBundle\Entity\TrendingArticleCategory;

/**
 * Class For TrendingArticleDAO DAO, This class is responsible for manage database 
 * operation for account table/entity
 *
 * @namespace DB\Bundle\AppBundle\DAO
 *
 * @author Dipak Patil
 */
class TrendingArticleDAO extends BaseDAO { 
	/**
	 * Always need doctrim object to initilise TrendingArticleDAO dao object
	 * @param $_dm - Doctrime object
	 */
	function __construct($_dm) {
		parent :: __construct($_dm);
	}
	

	/**
	 * This function return the trending article by category
	 * @param integer $categoryId
	 * @param integer $noOfDay
	 */
	public function getTrendingArticleListByAccount($accountId, $currentPage = 1, $noOfDay = 0, $noOfrecord = 0) {
		if(empty($accountId)) {
			return array();
		}
		
		$em = $this->getDoctrine()->getManager();
	
		//$from for entity name (table name)
		$from = "DB\\Bundle\\AppBundle\Entity\\TrendingArticle trendingArticle, 
				DB\\Bundle\\AppBundle\Entity\\TrendingArticleCategory trendingArticleCategory, 
				DB\\Bundle\\AppBundle\Entity\\AccountCategory accountCategory ";
		
		$articleNotificationHistorySQL = ''.
				//Added account id in table to no need join sub query here
				/*'SELECT DISTINCT articleNotifyHistory.trendingArticleId ' . 
				"FROM DB\\Bundle\\AppBundle\Entity\\ArticleNotifyHistory articleNotifyHistory, DB\\Bundle\\AppBundle\Entity\\NotificationSettings notificationSettings " . 
				"WHERE articleNotifyHistory.notificationSettingsId = notificationSettings.notificationSettingsId AND notificationSettings.accountId = " . $accountId;
				*/
				'SELECT DISTINCT articleNotifyHistory.trendingArticleId ' . 
				"FROM DB\\Bundle\\AppBundle\Entity\\ArticleNotifyHistory articleNotifyHistory " . 
				"WHERE articleNotifyHistory.accountId = " . $accountId;
		
		$socialPostSql = ''.
				'SELECT DISTINCT socialPost.trendingArticleId ' .
				"FROM DB\\Bundle\\AppBundle\Entity\\SocialPost socialPost " .
				"WHERE socialPost.accountId = " . $accountId;
		
		
		$whereCondition = 'trendingArticle.trendingArticleStatus = 0 AND trendingArticle.trendingArticleId NOT IN (' . $articleNotificationHistorySQL . ') ' .
				'AND trendingArticle.trendingArticleId NOT IN (' . $socialPostSql . ') ' .
				"AND trendingArticle.trendingArticleId = trendingArticleCategory.trendingArticleId " .
				"AND trendingArticle.approveStatus = " . TrendingArticle::APPROVE_STATUS_APPROVE . ' ' .
				"AND accountCategory.categoryId = trendingArticleCategory.categoryId " .
				"AND accountCategory.accountId = " . $accountId;
		
		if(isset($noOfDay) && $noOfDay > 0) {
			$startDate = date('Y-m-d H:i:s', strtotime('-' . $noOfDay . ' day', strtotime(date('Y-m-d H:i:s'))));
			$whereCondition .= " AND trendingArticle.lastUpdate > '" . $startDate . "' ";
		}
		
		//Get count of record available in table
		$count = $this->getCountByWhere("trendingArticle.trendingArticleId", $from, $whereCondition);
		if(empty($noOfrecord)) {
			$noOfrecord = Config::getSParameter('RECORDS_PER_PAGE');
		}
		$paggingDetails = DBUtil::getPaggingDetails($currentPage, $count, $noOfrecord);
		
		$sql = "SELECT DISTINCT trendingArticle.trendingArticleId, trendingArticle.postId, trendingArticle.category, " .
				"trendingArticle.url, trendingArticle.title, trendingArticle.image, trendingArticle.description, " .
				"trendingArticle.score, trendingArticle.caption, trendingArticle.trendingArticleStatus, ".
				"trendingArticle.approveStatus, trendingArticle.publicationDate, trendingArticle.lastUpdate " .
				"FROM " . $from;
	
		if(!empty($whereCondition)) {
			$sql .= " WHERE " . $whereCondition . " ";
		}
	
		$sql .= " ORDER BY trendingArticle.lastUpdate DESC ";
	
		$query = $em->createQuery($sql);
		$result = $query->setFirstResult($paggingDetails['MYSQL_LIMIT1'])->setMaxResults($paggingDetails['MYSQL_LIMIT2']);
		
		$result = $query->getResult();
	
		//get category detail for each
		if(!empty($result)) {
			$categoryDAO = new CategoryDAO($this->getDoctrine());
			$categoryMap = $categoryDAO->getCategoryMapByAccount($accountId);
			
			$TrendingArticleCategoryDAO = new TrendingArticleCategoryDAO($this->getDoctrine());
			for($index = 0; $index < count($result); $index ++) {
				$result[$index]['domain'] = DBUtil::getDomain($result[$index]['url']);
				
				$result[$index]['categoryList'] = array();
				$trendingArticleCategoryList = $TrendingArticleCategoryDAO->findDetailBy(new TrendingArticleCategory(), array('trendingArticleId'=>$result[$index]['trendingArticleId']));
				if(!empty($trendingArticleCategoryList)) {
					foreach($trendingArticleCategoryList as $trendingArticleCategory) {
						if(isset($categoryMap[$trendingArticleCategory['categoryId']])) {
							$record = array();
							$record['categoryId'] = $categoryMap[$trendingArticleCategory['categoryId']]['categoryId'];
							$record['category'] = $categoryMap[$trendingArticleCategory['categoryId']]['category'];
							$result[$index]['categoryList'][] = $record;
						}
					}
				}
			}
		}
		
		$list = array();
		$list['PAGING'] = $paggingDetails;
		$list['LIST'] = $result;
		
		return $list;
	}
	
	/**
	 * This function manage approce and article status
	 * @param array $param
	 */
	public function manageArticleStatus($param = array()) {
		if(empty($param) || empty($param['trendingArticleId'])) {
			return array();
		}
		
		$articleDetail = array();
		if(!empty($param['type']) && $param['type'] == 'APPROVE') {
			if(isset($param['op'])) {
				if($param['op'] == 'APPROVE') {
					$articleDetail['approveStatus'] = TrendingArticle::APPROVE_STATUS_APPROVE;
				}
				if($param['op'] == 'DISAPPROVE') {
					$articleDetail['approveStatus'] = TrendingArticle::APPROVE_STATUS_DISAPPROVE;
				}
			} else {
				if($param['status'] == TrendingArticle::APPROVE_STATUS_DISAPPROVE) {
					$articleDetail['approveStatus'] = TrendingArticle::APPROVE_STATUS_APPROVE;
				} else {
					$articleDetail['approveStatus'] = TrendingArticle::APPROVE_STATUS_DISAPPROVE;
				}
			}
		} else if(!empty($param['type']) && $param['type'] == 'ARTICLE') {
			$articleDetail['trendingArticleStatus'] = $param['status'];
			/*if($param['status'] == '0') {
				$articleDetail['trendingArticleStatus'] = '1';
			} else {
				$articleDetail['trendingArticleStatus'] = '0';
			}*/
		}
		
		if(!empty($articleDetail)) {
			$trendingArticle = new TrendingArticle();
			$trendingArticle->setTrendingArticleId($param['trendingArticleId']);
			
			$this->update($trendingArticle, $articleDetail);
			
			$articleDetail['trendingArticleId'] = $param['trendingArticleId'];
			
			return $articleDetail;
		}
		
		return array();
	}
	
	/**
	 * This function return article List
	 * @param string $search
	 * @param number $currentPage
	 * @return mixed[]  list of campaign and pagination
	 */
	public function getTrendingArticleListByCategoryId($searchOption = array(), $currentPage = 1, $noOfDay = 2) {
		$em = $this->getDoctrine()->getManager();
	
		//$from for entity name (table name)
		$from = "DB\\Bundle\\AppBundle\Entity\\TrendingArticle trendingArticle " ;
			//"DB\\Bundle\\AppBundle\Entity\\TrendingArticleCategory trendingArticleCategory, " . 
			//"DB\\Bundle\\AppBundle\Entity\\Category category ";
	
		$whereCondition = " trendingArticle.trendingArticleStatus = 0 " ;
				//"AND trendingArticle.trendingArticleId = trendingArticleCategory.trendingArticleId " . 
				//"AND trendingArticleCategory.categoryId = category.categoryId ";
		
		if(isset($searchOption['approveStatus']) && ($searchOption['approveStatus'] == '0' || $searchOption['approveStatus'] == '1')) {
			$whereCondition .= "AND trendingArticle.approveStatus = " . $searchOption['approveStatus'] . " ";
		}
		
		//if(!empty($searchOption['categoryId']) && is_numeric($searchOption['categoryId']) && $searchOption['categoryId'] > 0) {
		if(!empty($searchOption['categoryId'])) {
			$subSql = 'SELECT DISTINCT trendingArticleCategory.trendingArticleId ' . 
				'FROM DB\\Bundle\\AppBundle\Entity\\TrendingArticleCategory trendingArticleCategory ' . 
				'WHERE trendingArticleCategory.categoryId IN (' . $searchOption['categoryId'] . ') ' ;
			
			$whereCondition .= 'AND trendingArticle.trendingArticleId IN(' . $subSql . ') '; 
		}	
		
		
		if(!empty($noOfDay) && is_numeric($noOfDay) && $noOfDay > 0) {
			$startDate = date('Y-m-d H:i:s', strtotime('-' . $noOfDay . ' day', strtotime(date('Y-m-d H:i:s'))));
			$whereCondition .= "AND trendingArticle.lastUpdate > '" . $startDate . "' ";
		}
		

		if(!empty($searchOption['type']) && $searchOption['type'] == 'LU') {
			if(!empty($searchOption['fromDate']) && !empty($searchOption['fromDate'])) {
				$searchOption['fromDate'] = $searchOption['fromDate'] . ' 00:00:01';
				$searchOption['toDate'] = $searchOption['toDate'] . ' 23:59:59';
				$whereCondition .= "AND trendingArticle.lastUpdate > '" . $searchOption['fromDate'] . "' ";
				$whereCondition .= "AND trendingArticle.lastUpdate < '" . $searchOption['toDate'] . "' ";
			}
		} else if(!empty($searchOption['type']) && $searchOption['type'] == 'P') {
			if(!empty($searchOption['fromDate']) && !empty($searchOption['toDate'])) {
				$searchOption['fromDate'] = $searchOption['fromDate'] . ' 00:00:01';
				$searchOption['toDate'] = $searchOption['toDate'] . ' 23:59:59';
				$whereCondition .= "AND trendingArticle.publicationDate > '" . $searchOption['fromDate'] . "' ";
				$whereCondition .= "AND trendingArticle.publicationDate < '" . $searchOption['toDate'] . "' ";
			}
		}
	
		//Get count of record available in table
		$count = $this->getCountByWhere("trendingArticle.trendingArticleId", $from, $whereCondition);
	
		//Get paging detail
		$paggingDetails = DBUtil::getPaggingDetails($currentPage, $count, Config::getSParameter('RECORDS_PER_PAGE'));
	
		$sql = "SELECT trendingArticle.trendingArticleId, trendingArticle.postId, trendingArticle.category, " . 
				"trendingArticle.url, trendingArticle.title, trendingArticle.image, " . 
				"trendingArticle.description, trendingArticle.score, trendingArticle.caption, " . 
				"trendingArticle.approveStatus, trendingArticle.publicationDate, trendingArticle.lastUpdate " . 
		"FROM " . $from;
	
		if(!empty($whereCondition)) {
			$sql .= " WHERE " . $whereCondition . " ";
		}
	
		$sql .= "ORDER BY trendingArticle.lastUpdate DESC ";
	
		//echo $sql;
	
		$query = $em->createQuery($sql);
	
		$result = $query->setFirstResult($paggingDetails['MYSQL_LIMIT1'])->setMaxResults($paggingDetails['MYSQL_LIMIT2']);
	
		$result = $query->getResult();
	
		//make event type
		if(!empty($result)) {
			$categoryDAO = new CategoryDAO($this->getDoctrine());
			$categoryMap = $categoryDAO->getAllDefaultCategoryMap();
				
			$TrendingArticleCategoryDAO = new TrendingArticleCategoryDAO($this->getDoctrine());
			for($index = 0; $index < count($result); $index ++) {
				if(!empty($result[$index]['lastUpdate'])) {
					$result[$index]['lastUpdatedAt'] = $result[$index]['lastUpdate']->format('M d, Y, g:i a');
					$result[$index]['publicationDateAt'] = $result[$index]['publicationDate']->format('M d, Y, g:i a');
				}
				
				$result[$index]['categoryList'] = array();
				$trendingArticleCategoryList = $TrendingArticleCategoryDAO->findDetailBy(new TrendingArticleCategory(), array('trendingArticleId'=>$result[$index]['trendingArticleId']));
				if(!empty($trendingArticleCategoryList)) {
					foreach($trendingArticleCategoryList as $trendingArticleCategory) {
						if(isset($categoryMap[$trendingArticleCategory['categoryId']])) {
							$record = array();
							$record['categoryId'] = $categoryMap[$trendingArticleCategory['categoryId']]['categoryId'];
							$record['category'] = $categoryMap[$trendingArticleCategory['categoryId']]['category'];
							$result[$index]['categoryList'][] = $record;
						}
					}
				}
			}
		}
		
		//$result = $this->sortObjectList($result, 'score');
		
		$trendingArticleList = array();
		$trendingArticleList['PAGING'] = $paggingDetails;
		$trendingArticleList['LIST'] = $result;
	
		return $trendingArticleList;
	}
	
	/**
	 * This function return the trendign article list search by postId return from spike
	 * @param unknown $articleList
	 */
	public function getTredingArtcleByPostId($articleList) {
		if(empty($articleList)) {
			return array();
		}
		$trendignArticleMap = array();
		
		$postIdList = "'00'";
		$articleTitleList = "'A'";
		foreach($articleList as $article) {
			$postIdList .= ", '" . $article['postId'] . "'";
			
			$title = $article['title'];
			
			$title = str_replace("'", "''", $title);
			$title = str_replace("’", "", $title);
			
			$articleTitleList .= ", '" . $title . "'";
		}
		
		if($postIdList != "'00'" || true) {
			$em = $this->getDoctrine()->getManager();
			
			//$from for entity name (table name)
			$from = "DB\\Bundle\\AppBundle\Entity\\TrendingArticle trendingArticle ";
			
			$whereCondition = "trendingArticle.postId IN(" . $postIdList . ") " .
						" OR trendingArticle.title IN (" . $articleTitleList . ") ";
			
			$sql = "SELECT trendingArticle.trendingArticleId, trendingArticle.postId FROM " . $from;
			
			if(!empty($whereCondition)) {
				$sql .= " WHERE " . $whereCondition . " ";
			}
			
			$sql .= "ORDER BY trendingArticle.trendingArticleId ASC ";
			
			//echo $sql . "\r\n";
			
			$query = $em->createQuery($sql);
			
			$result = $query->getResult();
			
			$categoryArticleMap = array();
			if(!empty($result)) {
				foreach($result as $trendingArticleDetail) {
					$categoryArticleMap[$trendingArticleDetail['postId']] = $trendingArticleDetail;
				}
			}
			
			return $categoryArticleMap;
		}
		
		return $trendignArticleMap;
	}
	
	/**
	 * This function return article List
	 * @param string $search
	 * @param number $currentPage
	 * @return mixed[]  list of campaign and pagination
	 */
	public function getTrendingArticleList($search = '', $period='', $currentPage = 1, $noOfDay = 2) {
		//remove all charecter from perios and only keep the numbers
		$period = DBUtil::keepNumeric($period);
		
		$em = $this->getDoctrine()->getManager();
	
		//$from for entity name (table name)
		$from = "DB\\Bundle\\AppBundle\Entity\\TrendingArticle trendingArticle ";
	
		$whereCondition = " trendingArticle.trendingArticleStatus = 0 ";
		if(!empty($search)) {
			if(!empty($whereCondition)) {
				$whereCondition .= ' AND ';
			}
			$whereCondition .= "trendingArticle.category LIKE '%" . $search . "%' ";
		}
		
		if(!empty($period)) {
			if(!empty($whereCondition)) {
				$whereCondition .= ' AND ';
			}
			
			$whereCondition .= "trendingArticle.period LIKE '%" . $period . "%' ";
		}
		
		$startDate = date('Y-m-d H:i:s', strtotime('-' . $noOfDay . ' day', strtotime(date('Y-m-d H:i:s'))));
		$whereCondition .= "AND trendingArticle.lastUpdate > '" . $startDate . "' ";
	
		//Get count of record available in table
		$count = $this->getCountByWhere("trendingArticle.trendingArticleId", $from, $whereCondition);
	
		//Get paging detail
		$paggingDetails = DBUtil::getPaggingDetails($currentPage, $count, Config::getSParameter('RECORDS_PER_PAGE'));
	
		$sql = "SELECT trendingArticle.trendingArticleId, trendingArticle.postId, trendingArticle.category, trendingArticle.url, trendingArticle.title, " .
				"trendingArticle.image, trendingArticle.description, trendingArticle.score, trendingArticle.period, trendingArticle.caption, " . 
				"trendingArticle.approveStatus, trendingArticle.lastUpdate " .
		"FROM " . $from;
	
		if(!empty($whereCondition)) {
			$sql .= " WHERE " . $whereCondition . " ";
		}
	
		$sql .= "ORDER BY trendingArticle.score DESC ";
	
		//echo $sql;
	
		$query = $em->createQuery($sql);
	
		$result = $query->setFirstResult($paggingDetails['MYSQL_LIMIT1'])->setMaxResults($paggingDetails['MYSQL_LIMIT2']);
	
		$result = $query->getResult();
	
		//make event type
		if(!empty($result)) {
			for($index = 0; $index < count($result); $index ++) {
				if(!empty($result[$index]['lastUpdate'])) {
					$result[$index]['lastUpdatedAt'] = $result[$index]['lastUpdate']->format('Y-m-d H:i:s');
				}
				$result[$index]['categoryList'] = explode(',', $result[$index]['category']);
				$result[$index]['categoryScoreList'] = array();
				if(!empty($result[$index]['categoryList'])) {
					foreach($result[$index]['categoryList'] as $category) {
						$record = array();
						$record['category'] = $category;
						$record['score'] = 50;
						$result[$index]['categoryScoreList'][] = $record;
					}
				}
				$result[$index]['category'] = str_replace(',', ', ', $result[$index]['category']);
			}
		}
		
		//$result = $this->sortObjectList($result, 'score');
		
		$trendingArticleList = array();
		$trendingArticleList['PAGING'] = $paggingDetails;
		$trendingArticleList['LIST'] = $result;
	
		return $trendingArticleList;
	}
	
	/**
	 * This function return trending article list by criteria
	 * @param string $search
	 * @param number $currentPage
	 * @return mixed[]  list of campaign and pagination
	 */
	public function searchTrendingArticleList($accountId, $search = '', $currentPage = 1, $noOfDay = 2) {
		//getTrendingArticleListByNotification($period = 1, $emailNotificationId = '', $categoryList = array(), $noOfDay = 2) {
		$em = $this->getDoctrine()->getManager();
		
		$startDate = date('Y-m-d H:i:s', strtotime('-' . $noOfDay . ' hour', strtotime(date('Y-m-d H:i:s'))));
		
		$subSql  = 'SELECT DISTINCT emailHistory.postId ' ;
		$subSql .= 'FROM DB\\Bundle\\AppBundle\Entity\\EmailHistory emailHistory, DB\\Bundle\\AppBundle\Entity\\EmailNotification emailNotification ';
		$subSql .= 'WHERE emailHistory.emailNotificationId = emailNotification.emailNotificationId AND emailNotification.accountId = ' . $accountId;
		
		$socialPostSubSql = 'SELECT DISTINCT socialPost.trendingArticlePostId FROM DB\\Bundle\\AppBundle\Entity\\SocialPost socialPost WHERE socialPost.accountId = ' . $accountId;
		
		//$from for entity name (table name)
		$from = "DB\\Bundle\\AppBundle\Entity\\TrendingArticle trendingArticle ";
	
		//$whereCondition = ' trendingArticle.postId IN (' . $subSql . ') AND trendingArticle.postId NOT IN (' . $socialPostSubSql . ') ';
		$whereCondition = ' trendingArticle.postId NOT IN (' . $socialPostSubSql . ') ';
		
		$whereCondition .= "AND trendingArticle.lastUpdate > '" . $startDate . "' ";
	
		//Get count of record available in table
		$count = $this->getCountByWhere("trendingArticle.trendingArticleId", $from, $whereCondition);
		
		//Get paging detail
		$paggingDetails = DBUtil::getPaggingDetails($currentPage, $count, Config::getSParameter('RECORDS_PER_PAGE'));
		
		$sql = "SELECT trendingArticle.trendingArticleId, trendingArticle.postId, trendingArticle.category, trendingArticle.url, trendingArticle.title, " .
				"trendingArticle.image, trendingArticle.description, trendingArticle.score, trendingArticle.period, trendingArticle.caption, " . 
				"trendingArticle.approveStatus, trendingArticle.lastUpdate " .
				"FROM " . $from;
	
		if(!empty($whereCondition)) {
			$sql .= " WHERE " . $whereCondition . " ";
		}
	
		$sql .= "ORDER BY trendingArticle.score DESC ";
	
		//echo $sql;
	
		$query = $em->createQuery($sql);
		$result = $query->setFirstResult($paggingDetails['MYSQL_LIMIT1'])->setMaxResults($paggingDetails['MYSQL_LIMIT2']);
		
		$result = $query->getResult();
	
		//make event type
		if(!empty($result)) {
			for($index = 0; $index < count($result); $index ++) {
				if(!empty($result[$index]['lastUpdate'])) {
					$result[$index]['lastUpdatedAt'] = $result[$index]['lastUpdate']->format('Y-m-d H:i:s');
				}
				$result[$index]['categoryList'] = explode(',', $result[$index]['category']);
				$result[$index]['category'] = str_replace(',', ', ', $result[$index]['category']);
			}
		}
		
		$trendingArticleList = array();
		$trendingArticleList['PAGING'] = $paggingDetails;
		$trendingArticleList['LIST'] = $result;
		
		return $trendingArticleList;
	}
	
	/**
	 * This function return the trneidng topic by category map
	 */
	public function getTrendingTopicMap() {
		$trendingTopicDetailMap = array();
		
		//Get all trending topics
		$trendingTopicDetailList = $this->findDetailBy(new TrendingArticle(), array(), array('score'=>'DESC'));
		
		if(!empty($trendingTopicDetailList)) {
			foreach($trendingTopicDetailList as $trendingTopicDetail) {
				$categoryList = explode(',', $trendingTopicDetail['category']);
				if(!empty($categoryList)) {
					foreach($categoryList as $category) {
						$trendingTopicDetailMap[$category][$trendingTopicDetail['url']] = $trendingTopicDetail;
					}
				}
			}
		}
		
		return $trendingTopicDetailMap;
	}
	
	/**
	 * This function return the trneidng topic by category map
	 */
	public function getTrendingarticleMap($onlyPostId = false) {
		$trendingarticleMap = array();
		
		//Get all trending topics
		$trendingTopicDetailList = $this->findDetailBy(new TrendingArticle(), array(), array('score'=>'DESC'));
		
		if(!empty($trendingTopicDetailList)) {
			foreach($trendingTopicDetailList as $trendingTopicDetail) {
				if(!isset($trendingarticleMap[$trendingTopicDetail['postId']])) {
					if($onlyPostId == true) {
						$trendingarticleMap[$trendingTopicDetail['postId']] = $trendingTopicDetail['postId'];
					} else {
						$trendingarticleMap[$trendingTopicDetail['postId']] = $trendingTopicDetail;
					}
					
				}
			}
		}
		
		return $trendingarticleMap;
	}
	
	/**
	 * This function add new Trending Article 
	 * @param array $trendingArticleDetail
	 */
	public function addTrendingArticle($trendingArticleDetail = array(), $isInsert = true) {
		if(empty($trendingArticleDetail)) {
			return false;
		}
		
		//Set defautl data
		if(empty($trendingArticleDetail['category'])) {
			$trendingArticleDetail['category'] = '';
		}
		
		if(empty($trendingArticleDetail['score'])) {
			$trendingArticleDetail['score'] = 0.00;
		}
		
		if(empty($trendingArticleDetail['image'])) {
			$trendingArticleDetail['image'] = Config::getSParameter('SERVER_APP_PATH') . '/bundles/dbapp/v1poll/images/blank.png';
		}
		
		if(empty($trendingArticleDetail['postId'])) {
			$trendingArticleDetail['postId'] = 0;
		}
		
		if(empty($trendingArticleDetail['caption'])) {
			$trendingArticleDetail['caption'] = '';
		}
		
		if(empty($trendingArticleDetail['trendingArticleStatus'])) {
			$trendingArticleDetail['trendingArticleStatus'] = 0;
		}
		
		if(empty($trendingArticleDetail['approveStatus'])) {
			$trendingArticleDetail['approveStatus'] = 0;
		}
		
		if(!empty($trendingArticleDetail['publicationDate'])) {
			$trendingArticleDetail['publicationDate'] = new \DateTime($trendingArticleDetail['publicationDate']);
		} else {
			$trendingArticleDetail['publicationDate'] = null;
		}
		
		if(empty($trendingArticleDetail['lastUpdate'])) {
			$trendingArticleDetail['lastUpdate'] = new \DateTime();
		}
		
		$trendingArticle = new TrendingArticle();
		
		$trendingArticle->setPostId($trendingArticleDetail['postId']);
		$trendingArticle->setCategory($trendingArticleDetail['category']);
		$trendingArticle->setUr($trendingArticleDetail['url']);
		
		$trendingArticle->setTitle($trendingArticleDetail['title']);
		$trendingArticle->setImage($trendingArticleDetail['image']);
		
		$trendingArticle->setDescription($trendingArticleDetail['description']);
		$trendingArticle->setScore($trendingArticleDetail['score']);
		$trendingArticle->setCaption($trendingArticleDetail['caption']);
		
		$trendingArticle->setTrendingArticleStatus($trendingArticleDetail['trendingArticleStatus']);
		$trendingArticle->setApproveStatus($trendingArticleDetail['approveStatus']);
		
		$trendingArticle->setPublicationDate($trendingArticleDetail['publicationDate']);
		$trendingArticle->setLastUpdate($trendingArticleDetail['lastUpdate']);
		
		if($isInsert == true) {
			$trendingArticle = $this->save($trendingArticle);
			
			$newDetail = false;
			if(is_object($trendingArticle)) {
				$newDetail = $trendingArticle->toArray();
			}
			return $newDetail;
		} else {
			return $trendingArticle;
		}
	}
	
	/**
	 * This function return campaign List
	 * @param string $search
	 * @param number $currentPage
	 * @return mixed[]  list of campaign and pagination
	 */
	public function removeTrendingArticleList($category = '') {
		$em = $this->getDoctrine()->getManager();
	
		//$from for entity name (table name)
		$from = "DB\\Bundle\\AppBundle\Entity\\TrendingArticle trendingArticle ";
	
		$whereCondition = "";
		if(!empty($category)) {
			$whereCondition .= "trendingArticle.category LIKE '%" . $category . "%' ";
		}
	
		$sql = 'DELETE FROM ' . $from ;
	
		if(!empty($whereCondition)) {
			$sql .= " WHERE " . $whereCondition . " ";
		}
	
		$query = $em->createQuery($sql);
	
		$result = $query->getResult();
	
		return $result;
	}
	
	/**
	 * This function return all trending stories thsoe are not send
	 * @param number $period
	 * @param string $emailNotificationId
	 * @param array $categoryList
	 */
	public function getTrendingArticleListByNotification($period = 1, $emailNotificationId = '', $categoryList = array(), $noOfDay = 2) {
		//remove all charecter from perios and only keep the numbers
		$period = DBUtil::keepNumeric($period);
		
		$em = $this->getDoctrine()->getManager();
	
		$startDate = date('Y-m-d H:i:s', strtotime('-' . $noOfDay . ' day', strtotime(date('Y-m-d H:i:s'))));
		
		$subSql = 'SELECT DISTINCT emailHistory.postId FROM DB\\Bundle\\AppBundle\Entity\\EmailHistory emailHistory';
		if(!empty($emailNotificationId)) {
			$subSql .= ' WHERE emailHistory.emailNotificationId = ' . $emailNotificationId;
		}
		
		$socialSql = 'SELECT DISTINCT socialPost.link FROM DB\\Bundle\\AppBundle\Entity\\SocialPost socialPost';
		
		//$from for entity name (table name)
		$from = "DB\\Bundle\\AppBundle\Entity\\TrendingArticle trendingArticle ";
	
		$whereCondition = " trendingArticle.trendingArticleStatus = 0 AND trendingArticle.postId NOT IN (" . $subSql . ") ";
		$whereCondition .= " AND trendingArticle.url NOT IN (" . $socialSql . ") ";
		
		if(!empty($period)) {
			$whereCondition .= "AND trendingArticle.period like '%" . $period . "%' ";
		}
		
		if(!empty($categoryList)) {
			$whereCondition .= "AND ( ";
			$isFirst = true;
			foreach($categoryList as $category) {
				if($isFirst) {
					$whereCondition .= "trendingArticle.category LIKE '%" . $category . "%' ";
					$isFirst = false;
				} else {
					$whereCondition .= "OR trendingArticle.category LIKE '%" . $category . "%' ";
				}
			}
			$whereCondition .= " ) ";
		} else {
			/*
			$picklistDAO = new PicklistDAO($this->getDoctrine());
			$categoryList = $picklistDAO->getPicklistElement();
			//Consider default categpry from DB
			$whereCondition .= "AND ( ";
			$isFirst = true;
			foreach($categoryList as $category) {
				if($isFirst) {
					$whereCondition .= "trendingArticle.category LIKE '%" . $category . "%' ";
					$isFirst = false;
				} else {
					$whereCondition .= "OR trendingArticle.category LIKE '%" . $category . "%' ";
				}
			}
			$whereCondition .= " ) ";
			*/
		}
		
		$whereCondition .= "AND trendingArticle.lastUpdate > '" . $startDate . "' ";
	
		$sql = "SELECT trendingArticle.trendingArticleId, trendingArticle.postId, trendingArticle.category, trendingArticle.url, trendingArticle.title, " .
				"trendingArticle.image, trendingArticle.description, trendingArticle.score, trendingArticle.period, trendingArticle.caption, " . 
				"trendingArticle.approveStatus, trendingArticle.lastUpdate " .
				"FROM " . $from;
	
		if(!empty($whereCondition)) {
			$sql .= " WHERE " . $whereCondition . " ";
		}
	
		$sql .= "ORDER BY trendingArticle.score DESC ";
	
		//echo $sql;
	
		$query = $em->createQuery($sql);
		$result = $query->getResult();
	
		//make event type
		if(!empty($result)) {
			for($index = 0; $index < count($result); $index ++) {
				if(!empty($result[$index]['lastUpdate'])) {
					$result[$index]['lastUpdatedAt'] = $result[$index]['lastUpdate']->format('Y-m-d H:i:s');
				}
				$result[$index]['categoryList'] = explode(',', $result[$index]['category']);
				$result[$index]['category'] = str_replace(',', ', ', $result[$index]['category']);
			}
		}
		
		//remove dublicate articles
		$result = $this->ignoreDublicateArticleByFields($result, 'title');
	
		return $result;
	}
	
	/**
	 * This function return all trending stories thsoe are not send
	 * @param number $period
	 * @param string $emailNotificationId
	 * @param array $categoryList
	 */
	public function getTrendingArticleListByCategory($accountId, $categoryList = array(), $emailNotificationId = '', $currentPage = 1, $noOfDay = 2, $search = '') {
		$em = $this->getDoctrine()->getManager();
	
		$startDate = date('Y-m-d H:i:s', strtotime('-' . $noOfDay . ' hour', strtotime(date('Y-m-d H:i:s'))));
		
		/* $subSql = 'SELECT DISTINCT emailHistory.postId FROM DB\\Bundle\\AppBundle\Entity\\EmailHistory emailHistory';
		if(!empty($emailNotificationId)) {
			$subSql .= ' WHERE emailHistory.emailNotificationId = ' . $emailNotificationId;
		} */
		
		$socialPostSubSql = 'SELECT DISTINCT socialPost.trendingArticlePostId FROM DB\\Bundle\\AppBundle\Entity\\SocialPost socialPost WHERE socialPost.accountId = ' . $accountId;
		
		//$from for entity name (table name)
		$from = "DB\\Bundle\\AppBundle\Entity\\TrendingArticle trendingArticle ";
	
		$whereCondition = " trendingArticle.trendingArticleStatus = 0 AND trendingArticle.postId NOT IN (" . $socialPostSubSql . ") ";
		
		if(!empty($categoryList)) {
			$whereCondition .= "AND ( ";
			$isFirst = true;
			foreach($categoryList as $category) {
				if($isFirst) {
					$whereCondition .= "trendingArticle.category LIKE '%" . $category . "%' ";
					$isFirst = false;
				} else {
					$whereCondition .= "OR trendingArticle.category LIKE '%" . $category . "%' ";
				}
			}
			$whereCondition .= " ) ";
		} else {
			$picklistDAO = new PicklistDAO($this->getDoctrine());
			$categoryList = $picklistDAO->getPicklistElement();
			//Consider default categpry from DB
			$whereCondition .= "AND ( ";
			$isFirst = true;
			foreach($categoryList as $category) {
				if($isFirst) {
					$whereCondition .= "trendingArticle.category LIKE '%" . $category . "%' ";
					$isFirst = false;
				} else {
					$whereCondition .= "OR trendingArticle.category LIKE '%" . $category . "%' ";
				}
			}
			$whereCondition .= " ) ";
		}
		
		if(!empty($search)) {
			$whereCondition .= "AND trendingArticle.title LIKE '%" . $search . "%' ";
		}
		
		$whereCondition .= "AND trendingArticle.lastUpdate > '" . $startDate . "' ";
		
		//Get count of record available in table
		$count = $this->getCountByWhere("trendingArticle.trendingArticleId", $from, $whereCondition);
		
		//Get paging detail
		$paggingDetails = DBUtil::getPaggingDetails($currentPage, $count, Config::getSParameter('RECORDS_PER_PAGE'));
	
		$sql = "SELECT trendingArticle.trendingArticleId, trendingArticle.postId, trendingArticle.category, trendingArticle.url, trendingArticle.title, " .
				"trendingArticle.image, trendingArticle.description, trendingArticle.score, trendingArticle.period, trendingArticle.caption, " . 
				"trendingArticle.approveStatus, trendingArticle.lastUpdate " .
				"FROM " . $from;
	
		if(!empty($whereCondition)) {
			$sql .= " WHERE " . $whereCondition . " ";
		}
	
		$sql .= "ORDER BY trendingArticle.score DESC ";
	
		//echo $sql;
	
		$query = $em->createQuery($sql);
		
		$result = $query->setFirstResult($paggingDetails['MYSQL_LIMIT1'])->setMaxResults($paggingDetails['MYSQL_LIMIT2']);
		$result = $query->getResult();
	
		//make event type
		if(!empty($result)) {
			for($index = 0; $index < count($result); $index ++) {
				if(!empty($result[$index]['lastUpdate'])) {
					$result[$index]['lastUpdatedAt'] = $result[$index]['lastUpdate']->format('Y-m-d H:i:s');
				}
				$result[$index]['categoryList'] = explode(',', $result[$index]['category']);
				$result[$index]['category'] = str_replace(',', ', ', $result[$index]['category']);
			}
		}
	
		$trendingArticleList = array();
		$trendingArticleList['PAGING'] = $paggingDetails;
		$trendingArticleList['LIST'] = $result;
		
		return $trendingArticleList;
	}
	
	/**
	 * This function set article sttus to used i.e set to 1
	 */
	public function changeTrendingArticleStatus($trendingArticleStatus = 0) {
		$em = $this->getDoctrine()->getManager();
	
		//$from for entity name (table name)
		$from = "DB\\Bundle\\AppBundle\Entity\\TrendingArticle trendingArticle ";
		
		$subSql = 'SELECT DISTINCT emailHistory.postId FROM DB\\Bundle\\AppBundle\Entity\\EmailHistory emailHistory';
		$sql = 'UPDATE ' . $from . ' SET trendingArticle.trendingArticleStatus = ' . $trendingArticleStatus . ' WHERE trendingArticle.postId IN (' . $subSql . ')';
	
		$query = $em->createQuery($sql);
	
		$result = $query->getResult();
	
		return $result;
	}
	


	/**
	 * This function set article sttus to used i.e set to 1
	 */
	public function changeApprovStatus($trendingArticleIdList, $approveStatus = 0) {
		if(empty($trendingArticleIdList)) {
			return false;
		}
		
		$em = $this->getDoctrine()->getManager();
	
		//$from for entity name (table name)
		$from = "DB\\Bundle\\AppBundle\Entity\\TrendingArticle trendingArticle ";
		
		$idList = array();
		if(is_array($trendingArticleIdList)) {
			$idList = implode(',', $trendingArticleIdList);
		} else {
			$idList = $trendingArticleIdList;
		}
		
		$sql = 'UPDATE ' . $from . ' SET trendingArticle.approveStatus = :approveStatus WHERE trendingArticle.trendingArticleId IN (' . $idList . ')';
	
		$query = $em->createQuery($sql);
		$query->setParameter('approveStatus', $approveStatus);
		
		$result = $query->getResult();
	
		return $result;
	}
	
	/**
	 * This function delete all old articles
	 * @param number $noOfday
	 */
	public function deleteOldArticle($criteria = array(), $noOfDay = 2) {
		$em = $this->getDoctrine()->getManager();
		
		//$from for entity name (table name)
		$from = "DB\\Bundle\\AppBundle\Entity\\TrendingArticle trendingArticle ";
		
		$startDate = date('Y-m-d H:i:s', strtotime('-' . $noOfDay . ' day', strtotime(date('Y-m-d H:i:s'))));
		
		$whereCondition = " trendingArticle.lastUpdate < '" . $startDate . "' ";
		if(isset($criteria['trendingArticleStatus'])) {
			$whereCondition .= 'AND trendingArticle.trendingArticleStatus = ' . $criteria['trendingArticleStatus'] . ' ';
		}
		
		$sql = 'DELETE FROM ' . $from ;
		
		if(!empty($whereCondition)) {
			$sql .= " WHERE " . $whereCondition . " ";
		}
		
		$query = $em->createQuery($sql);
		
		$result = $query->getResult();
		
		return $result;
	}
	
	/**
	 * This function clean the all trending articles by criteria
	 * @param array $criteria
	 */
	public function cleanTrendingArticle($criteria = array()) {
		//Clean form trending article table
		$noOfDay = Config::getSParameter('DB_CLEAN_NO_DAY_OLD');
		$this->deleteOldArticle($criteria, $noOfDay);
		
		//Clean from trending article category table
		$trendingArticleCategoryDAO = new TrendingArticleCategoryDAO($this->getDoctrine());
		$trendingArticleCategoryDAO->deleteOldTrendingArticleCategory();
	}
	
	/**
	 * This functio send email based on period
	 * @param integer $period
	 * @param Object $caller
	 */
	public function sendTrendingArtileEmail($period, $caller, $url, $trendingArticleAddStatus = false) {
		$emailNotificationDAO = new EmailNotificationDAO($this->getDoctrine());
		$emailNotificationDetailList = $emailNotificationDAO->getEmailNotificationByPeriod($period);
		
		$searchCategotyList = array();
		if(!empty($emailNotificationDetailList)) {
			foreach($emailNotificationDetailList as $emailNotificationDetail) {
				$categoryList = explode(',', $emailNotificationDetail['category']);
				if(!empty($categoryList)) {
					foreach($categoryList as $category) {
						$category = trim($category);
						$searchCategotyList[$category] = $category;
					}
				}
			}
		}
		
		$count = 4;
		if($trendingArticleAddStatus == true) {
			$count = 20;
		}
		
		//Allow this link in Article only cron becasue need to sortout those category thsoe already having articles
		$trendingArticleDetailList = $this->getTrendingArticleListByNotification($period, '', $searchCategotyList, Config::NO_OF_DAY);
		
		$trendingArticleMap = array();
		
		if(!empty($trendingArticleDetailList)) {
			foreach($trendingArticleDetailList as $trendingArticleDetail) {
				if(!empty($trendingArticleDetail['categoryList'])) {
					$isArticlePush = false;
					foreach($trendingArticleDetail['categoryList'] as $category) {
						$category = trim(strtolower($category));
						if(empty($category)) {
							continue;
						}
		
						if(!isset($trendingArticleMap[$category])) {
							$trendingArticleMap[$category] = array();
						}
		
						if($isArticlePush == false && is_array($trendingArticleMap[$category]) && !isset($trendingArticleMap[$category][$trendingArticleDetail['postId']]) && count($trendingArticleMap[$category]) < $count) {
							$trendingArticleMap[$category][$trendingArticleDetail['postId']] = $trendingArticleDetail;
							$isArticlePush = true;
							continue;
						}
					}
				}
			}
		}
		
		if(!empty($trendingArticleMap)) {
			foreach($trendingArticleMap as $key=>$value) {
				echo $key . ' = ' . count($value) . "\r\n";
				
				if($trendingArticleAddStatus == true && count($trendingArticleMap[$key]) < $count) {
					unset($trendingArticleMap[$key]);
				}
			}
		}
		
		//crete new category list by filtering the category thsoe are already having articles
		$tempSearchCategotyList = array();
		if(!empty($searchCategotyList)) {
			foreach($searchCategotyList as $searchCategoty) {
				if(!isset($trendingArticleMap[$searchCategoty])) {
					$tempSearchCategotyList[$searchCategoty] = $searchCategoty;
				}
			}
		}
		//If not enough article exist then call spike API for new article
		if(!empty($tempSearchCategotyList) || $trendingArticleAddStatus == true || empty($trendingArticleDetailList) || count($trendingArticleDetailList) < 20) {
			//$this->fetchAndAddSpikeArticle($period, $searchCategotyList);
			$this->processSpikeArticle($tempSearchCategotyList, $period);
			
			if($trendingArticleAddStatus == false) {
				$trendingArticleDetailList = $this->getTrendingArticleListByNotification($period, '', $searchCategotyList, Config::NO_OF_DAY);
			}
		}
		
		if($trendingArticleAddStatus == true) {
			return array();
		}
		
		$trendingArticleMap = array();
		
		if(!empty($trendingArticleDetailList)) {
			foreach($trendingArticleDetailList as $trendingArticleDetail) {
				if(!empty($trendingArticleDetail['categoryList'])) {
					$isArticlePush = false;
					foreach($trendingArticleDetail['categoryList'] as $category) {
						$category = strtolower($category);
						if(empty($category)) {
							continue;
						}
		
						if(!isset($trendingArticleMap[$category])) {
							$trendingArticleMap[$category] = array();
						}
		
						if($isArticlePush == false && is_array($trendingArticleMap[$category]) && !isset($trendingArticleMap[$category][$trendingArticleDetail['postId']]) && count($trendingArticleMap[$category]) < 4) {
							$trendingArticleMap[$category][$trendingArticleDetail['postId']] = $trendingArticleDetail;
							$isArticlePush = true;
							continue;
						}
					}
				}
			}
		}
		
		
		foreach($trendingArticleMap as $key=>$value) {
			 echo $key . ' = ' . count($value) . "\r\n";
		}

		if(!empty($emailNotificationDetailList)) {
			$subject = "Here are suggested trending stories";
			
			$accountDAO = new AccountDAO($this->getDoctrine());
			$emailHistoryDAO = new EmailHistoryDAO($this->getDoctrine());
			$socialProfileDAO = new SocialProfileDAO($this->getDoctrine());
			foreach($emailNotificationDetailList as $emailNotificationDetail) {
				$articleList = array();
				$categoryList = explode(',', $emailNotificationDetail['category']);
				if(!empty($categoryList)) {
					foreach($categoryList as $category) {
						$category = trim(strtolower($category));
						
						if(isset($trendingArticleMap[$category])) {
							foreach($trendingArticleMap[$category] as $articleDetail) {
								$articleList[] = $articleDetail;
							}
						}
					}
				}
				
				usort($articleList, function($a, $b) {
					return $a['score'] < $b['score'];
				});
				
				//Add records into email history
				if(!empty($articleList)) {
					$emailHistoryList = array();
						
					foreach($articleList as $articleDetail) {
						$emailHistory = new EmailHistory();
						$emailHistory->setEmailNotificationId($emailNotificationDetail['emailNotificationId']);
						$emailHistory->setPostId($articleDetail['postId']);
						$emailHistory->setSendStatus(0);
						$emailHistory->setCreationDate(new \DateTime());
	
						$emailHistoryList[] = $emailHistory;
					}
						
					$emailHistoryDAO->saveBatch($emailHistoryList);
				} else {
					if(method_exists($caller,'log')) {
						$caller->log('NO article found for receiver - Account Id - ' . $emailNotificationDetail['accountId'] . ', Emails - '  . $emailNotificationDetail['receivers'] . ', Category - '. $emailNotificationDetail['category']);
					}
					continue;
				}
				
				//This continue here becasue we made cron to just process the article, another cron will take care to send mail
				continue;
				
				$caller->addInResponse('articleList', $articleList);
				$caller->addInResponse('emailNotificationDetail', $emailNotificationDetail);
				$caller->addInResponse('url', $url);
	
				//getAccountDetail
				$accountDetail = $accountDAO->findSingleDetailBy(new Account(), array('accountId'=>$emailNotificationDetail['accountId']));
				$caller->addInResponse('accountDetail', $accountDetail);
				if(!empty($accountDetail['accountId'])) {
					$userDAO = new UserDAO($this->getDoctrine());
					$userDetail = $userDAO->findSingleDetailBy(new User(), array('accountId'=>$accountDetail['accountId']));
					$caller->addInResponse('userDetail', $userDetail);
				}
				
				//get social media account detail
				$socialProfile = $socialProfileDAO->findSingleDetailBy(new SocialProfile(), array('profileType'=>'Facebook', 'accountId'=>$accountDetail['accountId']));
				$caller->addInResponse('socialProfile', $socialProfile);
					
				$html = $caller->renderView("DBAppBundle:email:layout-postreachv1.html.twig", $caller->getResponse());
				
				$receiverList = explode(',', $emailNotificationDetail['receivers']);
				if(!empty($receiverList)) {
					$dbSendgridClient = new DBSendgridClient(Config::getSParameter('SENDGRID_API_KEY_GENERATE_TOKEN'));
					foreach($receiverList as $receiver) {
						$receiver = trim($receiver);
						
						$emailDetail = array();
						
						$emailDetail['from'] = Config::getSParameter('FROM_EMAIL');
						$emailDetail['fromName'] = Config::getSParameter('FROM_EMAIL_NAME', Config::DEFAULT_FROM_NAME);
						
						$emailDetail['to'] = $receiver;
						$emailDetail['bcc'] = array(Config::getSParameter('BCC_EMAIL'));
						$emailDetail['subject'] = $subject;
						
						$emailDetail['body'] = $html;
						
						//$result = $dbAwsClient->sendMailBySESV($emailDetail);
						/* if(method_exists($caller,'log')) {
							$caller->log('Send to  T0 - ' . $receiver . ', MessageId : ' . $result['MessageId']);
						} */
						
						$result = $dbSendgridClient->sendMail($emailDetail);
						if(method_exists($caller,'log') && method_exists($result, 'getBody')) {
							$body = $result->getBody();
							$caller->log('Send to  T0 - ' . $receiver . ', MessageId : ' . $body['message']);
						}
						
						/* echo "MessageId : " . $result['MessageId'] . '<br/><hr/>';
						echo $html; */
					}
				}
				//echo $html;
			}
				
			//Set trending article status to used
			$this->changeTrendingArticleStatus(1);
			$this->deleteOldArticle(array(), Config::OLD_RECORD_NO_OF_DAY);
			$emailHistoryDAO->removeEmailHistoryByDays(Config::OLD_RECORD_NO_OF_DAY);
		}
	}
	
	/**
	 * This functio send email based on period
	 * @param integer $period
	 * @param Object $caller
	 */
	public function sendTrendingArtileEmailByAccount($period, $caller, $url, $trendingArticleAddStatus = false, $accountId = '') {
		$emailNotificationDAO = new EmailNotificationDAO($this->getDoctrine());
		$emailNotificationDetailList = array();
		
		if(!empty($accountId) && $accountId > 0) {
			$emailNotificationDetailList = $emailNotificationDAO->findDetailBy(new EmailNotification(), array('accountId'=>$accountId));
		} else {
			$emailNotificationDetailList = $emailNotificationDAO->getEmailNotificationByPeriod($period);
		}
		
		$searchCategotyList = array();
		if(!empty($emailNotificationDetailList)) {
			foreach($emailNotificationDetailList as $emailNotificationDetail) {
				$categoryList = explode(',', $emailNotificationDetail['category']);
				if(!empty($categoryList)) {
					foreach($categoryList as $category) {
						$category = trim($category);
						$searchCategotyList[$category] = $category;
					}
				}
			}
		}
		
		$trendingArticleDetailList = array();
		if(!empty($accountId) && $accountId > 0) {
			if(!empty($emailNotificationDetailList[0]['emailNotificationId'])) {
				$trendingArticleDetailList = $this->getTrendingArticleListByNotification($period, $emailNotificationDetailList[0]['emailNotificationId'], $searchCategotyList, Config::NO_OF_DAY);
			}
		} else {
			$trendingArticleDetailList = $this->getTrendingArticleListByNotification($period, '', $searchCategotyList, Config::NO_OF_DAY);
		}
		
		//If not enough article exist then call spike API for new article
		if(empty($trendingArticleDetailList) || count($trendingArticleDetailList) < 20 || $trendingArticleAddStatus == true) {
			//$this->fetchAndAddSpikeArticle($period, $searchCategotyList);
			$this->processSpikeArticle($searchCategotyList, $period);
			
			if($trendingArticleAddStatus == false) {
				if(!empty($accountId) && $accountId > 0) {
					if(!empty($emailNotificationDetailList[0]['emailNotificationId'])) {
						$trendingArticleDetailList = $this->getTrendingArticleListByNotification($period, $emailNotificationDetailList[0]['emailNotificationId'], $searchCategotyList, Config::NO_OF_DAY);
					}
				} else {
					$trendingArticleDetailList = $this->getTrendingArticleListByNotification($period, '', $searchCategotyList, Config::NO_OF_DAY);
				}
			}
		}
		
		if($trendingArticleAddStatus == true) {
			return array();
		}
		
		$trendingArticleMap = array();
		
		if(!empty($trendingArticleDetailList)) {
			foreach($trendingArticleDetailList as $trendingArticleDetail) {
				if(!empty($trendingArticleDetail['categoryList'])) {
					$isArticlePush = false;
					foreach($trendingArticleDetail['categoryList'] as $category) {
						$category = strtolower($category);
						if(empty($category)) {
							continue;
						}
		
						if(!isset($trendingArticleMap[$category])) {
							$trendingArticleMap[$category] = array();
						}
		
						if($isArticlePush == false && is_array($trendingArticleMap[$category]) && !isset($trendingArticleMap[$category][$trendingArticleDetail['postId']]) && count($trendingArticleMap[$category]) < 4) {
							$trendingArticleMap[$category][$trendingArticleDetail['postId']] = $trendingArticleDetail;
							$isArticlePush = true;
							continue;
						}
					}
				}
			}
		}
		
		foreach($trendingArticleMap as $key=>$value) {
			 echo $key . ' = ' . count($value) . "\r\n";
		}

		if(!empty($emailNotificationDetailList)) {
			$subject = "Here are suggested trending stories";
			
			$accountDAO = new AccountDAO($this->getDoctrine());
			$emailHistoryDAO = new EmailHistoryDAO($this->getDoctrine());
			$socialProfileDAO = new SocialProfileDAO($this->getDoctrine());
				
			foreach($emailNotificationDetailList as $emailNotificationDetail) {
				$articleList = array();
				$categoryList = explode(',', $emailNotificationDetail['category']);
				if(!empty($categoryList)) {
					foreach($categoryList as $category) {
						$category = trim($category);
						if(isset($trendingArticleMap[$category])) {
							foreach($trendingArticleMap[$category] as $articleDetail) {
								$articleList[] = $articleDetail;
							}
						}
					}
				}
				
				usort($articleList, function($a, $b) {
					return $a['score'] < $b['score'];
				});
				
				//Add records into email history
				if(!empty($articleList)) {
					$emailHistoryList = array();
						
					foreach($articleList as $articleDetail) {
						$emailHistory = new EmailHistory();
						$emailHistory->setEmailNotificationId($emailNotificationDetail['emailNotificationId']);
						$emailHistory->setPostId($articleDetail['postId']);
						$emailHistory->setCreationDate(new \DateTime());
	
						$emailHistoryList[] = $emailHistory;
					}
						
					$emailHistoryDAO->saveBatch($emailHistoryList);
				} else {
					if(method_exists($caller,'log')) {
						$caller->log('NO article found for receiver - Account Id - ' . $emailNotificationDetail['accountId'] . ', Emails - '  . $emailNotificationDetail['receivers'] . ', Category - '. $emailNotificationDetail['category']);
					}
					continue;
				}
	
				$caller->addInResponse('articleList', $articleList);
				$caller->addInResponse('emailNotificationDetail', $emailNotificationDetail);
				$caller->addInResponse('url', $url);
	
				//getAccountDetail
				$accountDetail = $accountDAO->findSingleDetailBy(new Account(), array('accountId'=>$emailNotificationDetail['accountId']));
				$caller->addInResponse('accountDetail', $accountDetail);
				
				//get social media account detail
				$socialProfile = $socialProfileDAO->findSingleDetailBy(new SocialProfile(), array('profileType'=>'Facebook', 'accountId'=>$accountDetail['accountId']));
				$caller->addInResponse('socialProfile', $socialProfile);
					
				$html = $caller->renderView("DBAppBundle:email:layout-postreachv1.html.twig", $caller->getResponse());
				
				$receiverList = explode(',', $emailNotificationDetail['receivers']);
				
				if(!empty($receiverList)) {
					$dbSendgridClient = new DBSendgridClient(Config::getSParameter('SENDGRID_API_KEY_GENERATE_TOKEN'));
					foreach($receiverList as $receiver) {
						$receiver = trim($receiver);
						if(empty($receiver)) {
							continue;
						}
						
						$emailDetail = array();
						
						$emailDetail['from'] = Config::getSParameter('FROM_EMAIL');
						$emailDetail['fromName'] = Config::getSParameter('FROM_EMAIL_NAME', Config::DEFAULT_FROM_NAME);
						
						$emailDetail['to'] = $receiver;
						$emailDetail['bcc'] = array(Config::getSParameter('BCC_EMAIL'));
						$emailDetail['subject'] = $subject;
						
						$emailDetail['body'] = $html;
						
						//$result = $dbAwsClient->sendMailBySESV($emailDetail);
						/* if(method_exists($caller,'log')) {
							$caller->log('Send to  T0 - ' . $receiver . ', MessageId : ' . $result['MessageId']);
						} */
						
						$result = $dbSendgridClient->sendMail($emailDetail);
						if(method_exists($caller,'log') && method_exists($result, 'getBody')) {
							$body = $result->getBody();
							$caller->log('Send to  T0 - ' . $receiver . ', MessageId : ' . $body['message']);
						}
						
						/* echo "MessageId : " . $result['MessageId'] . '<br/><hr/>';
						echo $html; */
					}
				}
				//echo $html;
			}
				
			//Set trending article status to used
			$this->changeTrendingArticleStatus(1);
			$this->deleteOldArticle(array(), Config::OLD_RECORD_NO_OF_DAY);
			$emailHistoryDAO->removeEmailHistoryByDays(Config::OLD_RECORD_NO_OF_DAY);
		}
	}
	
	/**
	 * This function fetch spike articl and put int DB
	 * @param unknown $period
	 * @param array $categoryNameList
	 */
	public function fetchAndAddSpikeArticle($period, $categoryNameList = array()) {
		//Provision to insert new article from spike
		$dbNewsWhipClient = new DBNewsWhipClient(Config::getSParameter('SPIKE_API_KEY'));
		
		//Get all existing trending article map
		$trendingArticleMap = $this->getTrendingarticleMap(true);
		
		$picklistDAO = new PicklistDAO($this->getDoctrine());
		
		//Get sub business type list
		$categoryList = $picklistDAO->findDetailBy(new Picklist(), array('picklistType'=>'category', 'childLavel'=>1));
		
		if(!empty($categoryNameList)) {
			$filteredCategoryList = array();
			if(!empty($categoryList)) {
				foreach($categoryList as $categoryDetail) {
					foreach($categoryNameList as $categoryName) {
						if(strtolower($categoryDetail['listElement']) == strtolower($categoryName)) {
							$filteredCategoryList[] = $categoryDetail;
						}
					}
				}
			}
			$categoryList = $filteredCategoryList;
		}

		if(!empty($categoryList)) {
			$trendingArticleNoOfRecords = Config::getSParameter('TRENDING_ARTICLE_NO_OF_RECORDS');
			foreach($categoryList as $categoryDetail) {
				/* if('Music/Hip-Hop' != $categoryDetail['listElement']) {
					continue;
				} */
				$categoryMap = $picklistDAO->getPicklistMap('category', $categoryDetail['picklistId'], 2);
				if(!empty($categoryMap)) {
					$keywords = '"' . strtolower($categoryDetail['listElement'])  . '"';
					$excludeKeywrods = '';
					foreach($categoryMap as $key=>$category) {
						//Check for exclude
						$leftCurlyAnglePos = strpos($category, '{');
						$rightCurlyAnglePos = strpos($category, '}');
						if($leftCurlyAnglePos === false && $rightCurlyAnglePos === false) {
							if(!empty($keywords)) {
								$keywords = $keywords . ' OR ';
							}
								
							$leftAnglePos = strpos($category, '[');
							$rightAnglePos = strpos($category, ']');
							if($leftAnglePos === false && $rightAnglePos === false) {
								$keywords = $keywords . '"'. $category . '"';
							} else {
								$category = str_replace('[', '"', $category);
								$category = str_replace(']', '"', $category);
									
								$keywords = $keywords . $category;
							}
						} else {
							if(!empty($excludeKeywrods)) {
								$excludeKeywrods = $excludeKeywrods . ' OR ';
							}
							$category = str_replace('{', '-"', $category);
							$category = str_replace('}', '"', $category);
							$excludeKeywrods = $excludeKeywrods . $category;
						}
					}
					$keywords = '(' . $keywords . ') ';
					
					if(!empty($excludeKeywrods)) {
						$keywords .= ' AND (' . $excludeKeywrods . ') ';
					}
					
					$keywords .= ' AND country_code:us AND -publisher:(complex.com OR thesource.com) ';
					
					echo $categoryDetail['listElement'] . ' == ' . $keywords . "\r\n\r\n";
					$articleList = $dbNewsWhipClient->getArticle($keywords, Config::getSParameter('SPIKE_API_DEFAULT_TIME_PER_KEYWORD'), Config::getSParameter('SPIKE_API_NO_OF_RECORDS'));
					
					if(!empty($articleList)) {
						$index = 0;
						$newArticleList = array();
						foreach($articleList as $article) {
							if(isset($trendingArticleMap[$article['postId']])) {
								continue;
							}
								
							$trendingArticleMap[$article['postId']] = $article['postId'];
								
							$article['score'] = $article['velocity'];
							$article['period'] = $period;
							$article['trendingArticleStatus'] = 0;
								
							$article['category'] = $categoryDetail['listElement'];
								
							$article = $this->addTrendingArticle($article, false);
							if(is_object($article)) {
								$newArticleList[] = $article;
							}
							
							if($index > $trendingArticleNoOfRecords) {
								break;
							} else {
								$index ++;
							}
						}
						//Save in batch
						if(!empty($newArticleList)) {
							$this->saveBatch($newArticleList);
						}
					}
				}
			}
		}
	}
	
	/**
	 * This function ignore all articles those are same with respective to passed field
	 * @param array $articleList
	 * @param strgin $field
	 * @return list of filtered articles
	 */
	public function ignoreDublicateArticleByFields($articleList, $field = 'title') {
		if(!empty($articleList)) {
			$filteredList = array();
			$articleMap = array();
			foreach($articleList as $article) {
				if(!isset($article[$field]) || empty($article[$field])) {
					continue;
				}
				if(!isset($articleMap[$article[$field]])) {
					$filteredList[] = $article;
					$articleMap[$article[$field]] = $article;
				}
			}
		}
		if(!empty($filteredList)) {
			$articleList = $filteredList;
		}
		return $articleList;	
	}
	
	/**
	 * This function will get cateogry those dont have any article
	 */
	public function getCateogryWithArticleCount($period, $minLimit = 4) {
		$trendingArticleDAO = new TrendingArticleDAO($this->getDoctrine());
		$trendingArticleDetailList = $trendingArticleDAO->getTrendingArticleListByNotification($period);
		
		$trendingArticleMap = array();
		$usedArticleMap = array();
		if(!empty($trendingArticleDetailList)) {
			foreach($trendingArticleDetailList as $trendingArticleDetail) {
				if(!empty($trendingArticleDetail['categoryList'])) {
					$isArticlePush = false;
					foreach($trendingArticleDetail['categoryList'] as $category) {
						if(empty($category)) {
							continue;
						}
		
						if(!isset($trendingArticleMap[$category])) {
							$trendingArticleMap[$category] = 0;
						}
		
						if($isArticlePush == false && is_numeric($trendingArticleMap[$category]) && !isset($usedArticleMap[$trendingArticleDetail['postId']])) {
							$trendingArticleMap[$category] = $trendingArticleMap[$category] + 1;
							$usedArticleMap[$trendingArticleDetail['postId']] = $trendingArticleDetail['postId'];
							$isArticlePush = true;
							continue;
						}
					}
				}
			}
		}
		
		//Get category those are selected by user
		$emailNotificationDAO = new EmailNotificationDAO($this->getDoctrine());
		$emailNotificationList = $emailNotificationDAO->findDetailBy(new EmailNotification());
		
		$categoryCountList = array();
		if(!empty($emailNotificationList)) {
			foreach($emailNotificationList as $emailNotification) {
				$emailNotification['categoryList'] = explode(',', $emailNotification['category']);
				if(!empty($emailNotification['categoryList'])) {
					foreach($emailNotification['categoryList'] as $category) {
						if(isset($trendingArticleMap[$category])) {
							if($trendingArticleMap[$category] <= $minLimit) {
								$categoryCountList[$category] = $trendingArticleMap[$category];
							}
						} else {
							$categoryCountList[$category] = 0;
						}
					}
				}
			}
		}
		
		$categoryCountDetailList = array();
		if(!empty($categoryCountList)) {
			foreach($categoryCountList as $category=>$count) {
				$categoryCountDetailList[] = array('category'=>$category, 'count'=>$count);
			}
		}
		
		return $categoryCountDetailList;
	}
	
	/**
	 * This function update spike article in local DB
	 * @param array $categoryList
	 * @param integer $period
	 */
	public function processSpikeArticle($searchCategotyList, $period) {
		//Start process
		echo 'Process spike article' . "\r\n\r\n";
		
		$categoryDAO = new CategoryDAO($this->getDoctrine());
		$categoryList = $categoryDAO->findDetailBy(new Category(), array('categoryType'=>'1'));

		if(!empty($searchCategotyList)) {
			$filteredCategoryList = array();
			if(!empty($categoryList)) {
				foreach($categoryList as $categoryDetail) {
					foreach($searchCategotyList as $categoryName) {
						if(trim(strtolower($categoryDetail['category'])) == trim(strtolower($categoryName))) {
							$filteredCategoryList[] = $categoryDetail;
						}
					}
				}
			}
			$categoryList = $filteredCategoryList;
		}
		
		$trendingArticleDAO = new TrendingArticleDAO($this->getDoctrine());
		
		$dbNewsWhipClient = new DBNewsWhipClient(Config::getSParameter('SPIKE_API_KEY'));
		if(!empty($categoryList)) {
			//Get all existing trending article map
			$trendingArticleMap = $trendingArticleDAO->getTrendingarticleMap(true);
			
			$trendingArticleNoOfRecords = Config::getSParameter('TRENDING_ARTICLE_NO_OF_RECORDS');
			foreach($categoryList as $category) {
				$criteria = $categoryDAO->getSpikeCriteriaBYCategory(3, $category);
				if(!empty($criteria['filters'][0])) {
					$articleList = $dbNewsWhipClient->getArticleByCriteriaV1($criteria);
					echo $category['category'] . ' = Count : ' . count($articleList) . ' = ' . json_encode($criteria) . "\r\n\r\n";
					
					if(!empty($articleList)) {
						$index = 0;
						$newArticleList = array();
						foreach($articleList as $article) {
							if(isset($trendingArticleMap[$article['postId']])) {
								continue;
							}
					
							$trendingArticleMap[$article['postId']] = $article['postId'];
					
							$article['score'] = $article['velocity'];
							$article['period'] = $period;
							$article['trendingArticleStatus'] = 0;
					
							$article['category'] = trim(strtolower($category['category']));;
					
							$article = $trendingArticleDAO->addTrendingArticle($article, false);
							if(is_object($article)) {
								$newArticleList[] = $article;
							}
								
							if($index > $trendingArticleNoOfRecords) {
								break;
							} else {
								$index ++;
							}
						}
						
						//Save in batch
						if(!empty($newArticleList)) {
							$trendingArticleDAO->saveBatch($newArticleList);
						}
					}
				}
			}
		}
	}
	
	/**
	 * This function return article List
	 * @param string $search
	 * @param number $currentPage
	 * @return mixed[]  list of campaign and pagination
	 */
	public function getScheduleTrendingArticles($category = '', $sendStatus = 0, $period='', $currentPage = 1, $noOfDay = 2) {
		//remove all charecter from perios and only keep the numbers
		$period = DBUtil::keepNumeric($period);
		
		$em = $this->getDoctrine()->getManager();
	
		//$from for entity name (table name)
		$from = "DB\\Bundle\\AppBundle\Entity\\TrendingArticle trendingArticle ";
	
		$whereCondition = ''; //" trendingArticle.trendingArticleStatus = 0 ";
		if(!empty($category)) {
			if(!empty($whereCondition)) {
				$whereCondition .= ' AND ';
			}
			$whereCondition .= "trendingArticle.category LIKE '%" . $category . "%' ";
		}
		
		if(!empty($period)) {
			if(!empty($whereCondition)) {
				$whereCondition .= ' AND ';
			}
			
			$whereCondition .= "trendingArticle.period LIKE '%" . $period . "%' ";
		}
		
		if(($sendStatus == 0 || $sendStatus == 1)) {
			$from .= ", DB\\Bundle\\AppBundle\Entity\\EmailHistory emailHistory ";
			if(!empty($whereCondition)) {
				$whereCondition .= ' AND ';
			}
				
			$whereCondition .= "emailHistory.postId = trendingArticle.postId AND emailHistory.sendStatus = " . $sendStatus . " ";
		}
		
		$startDate = date('Y-m-d H:i:s', strtotime('-' . $noOfDay . ' day', strtotime(date('Y-m-d H:i:s'))));
		$whereCondition .= "AND trendingArticle.lastUpdate > '" . $startDate . "' ";
	
		//Get count of record available in table
		$count = $this->getCountByWhere("trendingArticle.trendingArticleId", $from, $whereCondition);
	
		//Get paging detail
		$paggingDetails = DBUtil::getPaggingDetails($currentPage, $count, Config::getSParameter('RECORDS_PER_PAGE'));
	
		$sql = "SELECT DISTINCT trendingArticle.trendingArticleId, trendingArticle.postId, trendingArticle.category, trendingArticle.url, trendingArticle.title, " .
				"trendingArticle.image, trendingArticle.description, trendingArticle.score, trendingArticle.period, trendingArticle.caption, " . 
				"trendingArticle.approveStatus, trendingArticle.lastUpdate " .
		"FROM " . $from;
	
		if(!empty($whereCondition)) {
			$sql .= " WHERE " . $whereCondition . " ";
		}
	
		$sql .= "ORDER BY trendingArticle.score DESC ";
	
		//echo $sql;
	
		$query = $em->createQuery($sql);
	
		$result = $query->setFirstResult($paggingDetails['MYSQL_LIMIT1'])->setMaxResults($paggingDetails['MYSQL_LIMIT2']);
	
		$result = $query->getResult();
	
		//make event type
		if(!empty($result)) {
			for($index = 0; $index < count($result); $index ++) {
				if(!empty($result[$index]['lastUpdate'])) {
					$result[$index]['lastUpdatedAt'] = $result[$index]['lastUpdate']->format('Y-m-d H:i:s');
				}
				$result[$index]['categoryList'] = explode(',', $result[$index]['category']);
				$result[$index]['category'] = str_replace(',', ', ', $result[$index]['category']);
			}
		}
		
		//$result = $this->sortObjectList($result, 'score');
		
		$trendingArticleList = array();
		$trendingArticleList['PAGING'] = $paggingDetails;
		$trendingArticleList['LIST'] = $result;
	
		return $trendingArticleList;
	}
	
	/**
	 * This function return article List
	 * @param string $search
	 * @param number $currentPage
	 * @return mixed[]  list of campaign and pagination
	 */
	public function getScheduleTrendingArticlesEmailQueue($period = '', $noOfDay = 2) {
		//remove all charecter from perios and only keep the numbers
		$period = DBUtil::keepNumeric($period);
		
		$em = $this->getDoctrine()->getManager();
	
		//$from for entity name (table name)
		$from = "DB\\Bundle\\AppBundle\Entity\\TrendingArticle trendingArticle ";
		$from .= ", DB\\Bundle\\AppBundle\Entity\\EmailHistory emailHistory ";
	
		$whereCondition = "emailHistory.postId = trendingArticle.postId AND emailHistory.sendStatus = 0 ";
		
		if(!empty($period)) {
			if(!empty($whereCondition)) {
				$whereCondition .= ' AND ';
			}
				
			$whereCondition .= "trendingArticle.period LIKE '%" . $period . "%' ";
		}
		
		$startDate = date('Y-m-d H:i:s', strtotime('-' . $noOfDay . ' day', strtotime(date('Y-m-d H:i:s'))));
		$whereCondition .= "AND trendingArticle.lastUpdate > '" . $startDate . "' ";
	
		$sql = "SELECT DISTINCT trendingArticle.trendingArticleId, trendingArticle.postId, trendingArticle.category, trendingArticle.url, trendingArticle.title, " .
				"trendingArticle.image, trendingArticle.description, trendingArticle.score, trendingArticle.period, trendingArticle.caption, " . 
				"trendingArticle.approveStatus, trendingArticle.lastUpdate " .
		"FROM " . $from;
	
		if(!empty($whereCondition)) {
			$sql .= " WHERE " . $whereCondition . " ";
		}
	
		$sql .= "ORDER BY trendingArticle.score DESC ";
	
		//echo $sql;
	
		$query = $em->createQuery($sql);
		$result = $query->getResult();
	
		//make event type
		if(!empty($result)) {
			for($index = 0; $index < count($result); $index ++) {
				if(!empty($result[$index]['lastUpdate'])) {
					$result[$index]['lastUpdatedAt'] = $result[$index]['lastUpdate']->format('Y-m-d H:i:s');
				}
				$result[$index]['categoryList'] = explode(',', $result[$index]['category']);
				$result[$index]['category'] = str_replace(',', ', ', $result[$index]['category']);
			}
		}
		
		return $result;
	}

	/**
	 * This function change the caption of article
	 * @param integer $trendingArticleId
	 * @param string $caption
	 */
	public function changeCaption($trendingArticleId, $caption) {
		if(empty($trendingArticleId) || empty($caption)) {
			return array();
		}
		
		$trendingArticle = new TrendingArticle();;
		$trendingArticle->setTrendingArticleId($trendingArticleId);
		
		return $this->update($trendingArticle, array('caption'=>$caption));
	}
}
?>