<?php
namespace DB\Bundle\AppBundle\DAO;

use DB\Bundle\CommonBundle\Base\BaseDAO;

/**
 * Class For TrendingArticleDAO DAO, This class is responsible for manage database 
 * operation for account table/entity
 *
 * @namespace DB\Bundle\AppBundle\DAO
 *
 * @author Dipak Patil
 */
class TrendingArticleCategoryDAO extends BaseDAO { 
	/**
	 * Always need doctrim object to initilise TrendingArticleDAO dao object
	 * @param $_dm - Doctrime object
	 */
	function __construct($_dm) {
		parent :: __construct($_dm);
	}
	
	/**
	 * This function return the category and no of article for that category
	 * @param number $noOfDay
	 */
	public function getCategoryArticleCountMap($noOfDay = 2) {
		$em = $this->getDoctrine()->getManager();
		
		//$from for entity name (table name)
		$from = "DB\\Bundle\\AppBundle\Entity\\TrendingArticleCategory trendingArticleCategory, DB\\Bundle\\AppBundle\Entity\\TrendingArticle trendingArticle ";
		
		$startDate = date('Y-m-d H:i:s', strtotime('-' . $noOfDay . ' day', strtotime(date('Y-m-d H:i:s'))));
		
		$whereCondition = "trendingArticleCategory.trendingArticleId = trendingArticle.trendingArticleId AND trendingArticle.lastUpdate > '" . $startDate . "' ";
		
		$sql = "SELECT trendingArticleCategory.categoryId, count(trendingArticleCategory.trendingArticleId) as totalArticle FROM " . $from;
		
		if(!empty($whereCondition)) {
			$sql .= " WHERE " . $whereCondition . " ";
		}
		
		$sql .= "GROUP BY trendingArticleCategory.categoryId ORDER BY trendingArticleCategory.categoryId ASC ";
		
		//echo $sql;
		
		$query = $em->createQuery($sql);
		
		$result = $query->getResult();
		
		$categoryArticleMap = array();
		if(!empty($result)) {
			foreach($result as $category) {
				$categoryArticleMap[$category['categoryId']] = $category['totalArticle'];
			}
		}
		
		return $categoryArticleMap;
	}
	
	/**
	 * This function return all article category based on trending article ids
	 * @param array $articleList
	 */
	public function getCategoryArticleMap($categoryId, $articleList = array()) {
		if(empty($articleList)) {
			return array();
		}
		$categoryArticleMap = array();
		
		$trendingArticleIds = "0";
		foreach($articleList as $article) {
			$trendingArticleIds .= ", " . $article['trendingArticleId'];
		}
		
		if($trendingArticleIds != '0') {
			$em = $this->getDoctrine()->getManager();
			
			//$from for entity name (table name)
			$from = "DB\\Bundle\\AppBundle\Entity\\TrendingArticleCategory trendingArticleCategory ";
			
			$whereCondition = " trendingArticleCategory.categoryId = " . $categoryId . ' '.
							"AND trendingArticleCategory.trendingArticleId IN (" . $trendingArticleIds . ") ";
			
			$sql = "SELECT trendingArticleCategory.trendingArticleId, trendingArticleCategory.categoryId FROM " . $from;
			
			if(!empty($whereCondition)) {
				$sql .= " WHERE " . $whereCondition . " ";
			}
			
			$sql .= "ORDER BY trendingArticleCategory.trendingArticleId ASC ";
			
			//echo $sql . "\r\n";
			
			$query = $em->createQuery($sql);
			$result = $query->setFirstResult(0)->setMaxResults(150);
			$result = $query->getResult();
			
			if(!empty($result)) {
				foreach($result as $category) {
					$categoryArticleMap[$category['trendingArticleId'] . '_' . $category['categoryId']] = $category;
				}
			}
			
			return $categoryArticleMap;
		}
		
		return $categoryArticleMap;
	}
	
	/**
	 * This function delete all old articles
	 * @param number $noOfday
	 */
	public function deleteOldTrendingArticleCategory() {
		$em = $this->getDoctrine()->getManager();
		
		//$from for entity name (table name)
		$from = "DB\\Bundle\\AppBundle\Entity\\TrendingArticleCategory trendingArticleCategory ";
		
		$subSQL = 'SELECT DISTINCT trendingArticle.trendingArticleId FROM DB\\Bundle\\AppBundle\Entity\\TrendingArticle trendingArticle';
		$whereCondition = 'trendingArticleCategory.trendingArticleId NOT IN(' . $subSQL . ')';
		
		$sql = 'DELETE FROM ' . $from ;
		
		if(!empty($whereCondition)) {
			$sql .= " WHERE " . $whereCondition . " ";
		}
		
		$query = $em->createQuery($sql);
		
		$result = $query->getResult();
		
		return $result;
	}
}
?>