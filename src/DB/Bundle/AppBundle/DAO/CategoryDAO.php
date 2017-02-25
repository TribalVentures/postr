<?php
namespace DB\Bundle\AppBundle\DAO;

use DB\Bundle\CommonBundle\Base\BaseDAO;
use DB\Bundle\AppBundle\Entity\Category;
use DB\Bundle\CommonBundle\Util\DBUtil;
use DB\Bundle\AppBundle\Common\Config;

/**
 * Class For CategoryDAO, This class is responsible for manage database 
 * operation for category table/entity
 *
 * @namespace DB\Bundle\AppBundle\DAO
 *
 * @author Dipak Patil
 */
class CategoryDAO extends BaseDAO { 
	/**
	 * Always need doctrim object to initilise CategoryDAO dao object
	 * @param $_dm - Doctrime object
	 */
	function __construct($_dm) {
		parent :: __construct($_dm);
	}
	
	/**
	 * This function add new cateory into DB
	 * @param array $categoryDetail
	 */
	public function addCategory($categoryDetail = array()) {
		if(empty($categoryDetail)) {
			return array();
		}
		
		if(empty($categoryDetail['parentCategoryId'])) {
			$categoryDetail['parentCategoryId'] = 0;
		}
		
		if(!isset($categoryDetail['categoryType'])) {
			$categoryDetail['categoryType'] = 1;
		}
		
		if(empty($categoryDetail['image'])) {
			$categoryDetail['image'] = '';
		}
		
		if(empty($categoryDetail['fromTime'])) {
			$categoryDetail['fromTime'] = '3';
		}
		
		if(empty($categoryDetail['language'])) {
			$categoryDetail['language'] = 'en';
		}
		
		if(empty($categoryDetail['size'])) {
			$categoryDetail['size'] = '150';
		}
		
		if(empty($categoryDetail['sortBy'])) {
			$categoryDetail['sortBy'] = 'fb_total_engagement';
		}
		
		if(empty($categoryDetail['includeKeywords'])) {
			$categoryDetail['includeKeywords'] = '';
		}
		
		if(empty($categoryDetail['excludeKeywords'])) {
			$categoryDetail['excludeKeywords'] = '';
		}
		
		if(empty($categoryDetail['includePublisher'])) {
			$categoryDetail['includePublisher'] = '';
		}
		
		if(empty($categoryDetail['excludePublisher'])) {
			$categoryDetail['excludePublisher'] = '';
		}
		
		if(empty($categoryDetail['includeCountry'])) {
			$categoryDetail['includeCountry'] = '';
		}
		
		if(empty($categoryDetail['excludeCountry'])) {
			$categoryDetail['excludeCountry'] = '';
		}
		
		if(empty($categoryDetail['includeTopic'])) {
			$categoryDetail['includeTopic'] = '';
		}
		
		if(empty($categoryDetail['excludeTopic'])) {
			$categoryDetail['excludeTopic'] = '';
		}
		
		if(empty($categoryDetail['categoryStatus'])) {
			$categoryDetail['categoryStatus'] = '0';
		}
		
		$category = new Category();
		$category->setParentCategoryId($categoryDetail['parentCategoryId']);
		
		$category->setCategory($categoryDetail['category']);
		$category->setCategoryType($categoryDetail['categoryType']);
		
		$category->setImage($categoryDetail['image']);
		
		$category->setFromTime($categoryDetail['fromTime']);
		$category->setLanguage($categoryDetail['language']);
		$category->setSize($categoryDetail['size']);
		$category->setSortBy($categoryDetail['sortBy']);
		
		$category->setIncludeKeywords($categoryDetail['includeKeywords']);
		$category->setExcludeKeywords($categoryDetail['excludeKeywords']);
		
		$category->setIncludePublisher($categoryDetail['includePublisher']);
		$category->setExcludePublisher($categoryDetail['excludePublisher']);
		
		$category->setIncludeCountry($categoryDetail['includeCountry']);
		$category->setExcludeCountry($categoryDetail['excludeCountry']);
		
		$category->setIncludeTopic($categoryDetail['includeTopic']);
		$category->setExcludeTopic($categoryDetail['excludeTopic']);
		
		$category->setCategoryStatus($categoryDetail['categoryStatus']);
		
		$category = $this->save($category);
		
		$newDetail = false;
		if(is_object($category)) {
			$newDetail = $category->toArray();
		}
		return $newDetail;
	}
	
	/**
	 * This function update  cateory into DB
	 * @param array $categoryDetail
	 */
	public function updateCategory($categoryDetail = array()) {
		if(empty($categoryDetail)) {
			return array();
		}
		$record = array();
		if(!empty($categoryDetail['parentCategoryId'])) {
			$record['parentCategoryId'] = $categoryDetail['parentCategoryId'];
		}
		
		if(!empty($categoryDetail['category'])) {
			$record['category'] = $categoryDetail['category'];
		}
		
		if(!empty($categoryDetail['categoryType'])) {
			$record['categoryType'] = $categoryDetail['categoryType'];
		}
		
		if(!empty($categoryDetail['image'])) {
			$record['image'] = $categoryDetail['image'];
		}
		
		if(!empty($categoryDetail['fromTime'])) {
			$record['fromTime'] = $categoryDetail['fromTime'];
		}
		
		if(!empty($categoryDetail['fromTime'])) {
			$record['language'] = $categoryDetail['language'];
		}
		
		if(!empty($categoryDetail['size'])) {
			$record['size'] = $categoryDetail['size'];
		}
		
		if(!empty($categoryDetail['sortBy'])) {
			$record['sortBy'] = $categoryDetail['sortBy'];
		}
		
		if(isset($categoryDetail['includeKeywords'])) {
			$record['includeKeywords'] = $categoryDetail['includeKeywords'];
		}
		
		if(isset($categoryDetail['excludeKeywords'])) {
			$record['excludeKeywords'] = $categoryDetail['excludeKeywords'];
		}
		
		if(isset($categoryDetail['includePublisher'])) {
			$record['includePublisher'] = $categoryDetail['includePublisher'];
		}
		
		if(isset($categoryDetail['excludePublisher'])) {
			$record['excludePublisher'] = $categoryDetail['excludePublisher'];
		}
		
		if(isset($categoryDetail['includeCountry'])) {
			$record['includeCountry'] = $categoryDetail['includeCountry'];
		}
		
		if(isset($categoryDetail['excludeCountry'])) {
			$record['excludeCountry'] = $categoryDetail['excludeCountry'];
		}
		
		if(isset($categoryDetail['includeTopic'])) {
			$record['includeTopic'] = $categoryDetail['includeTopic'];
		}
		
		if(isset($categoryDetail['excludeTopic'])) {
			$record['excludeTopic'] = $categoryDetail['excludeTopic'];
		}
		
		if(isset($categoryDetail['categoryStatus'])) {
			$record['categoryStatus'] = $categoryDetail['categoryStatus'];
		}
	
		$updatedDetail = array();
		if(!empty($record)) {
			$category = new Category();
			$category->setCategoryId($categoryDetail['categoryId']);
			$category = $this->update($category, $record);
				
			if(is_object($category)) {
				$updatedDetail = $category->toArray();
			}
		}
		
		return $updatedDetail;
	}
	
	/**
	 * This function returl all category from DB
	 */
	public function getCategoryList($parentCategoryId, $categoryStatus = -1, $currentPage = 1) {
		$em = $this->getDoctrine()->getManager();
		
		//$from for entity name (table name)
		$from = "DB\\Bundle\\AppBundle\Entity\\Category category ";
		
		$whereCondition = "";
		
		if(is_numeric($parentCategoryId) && $parentCategoryId > -1) {
			$whereCondition .= "category.parentCategoryId = " . $parentCategoryId . " ";
		}
		
		if(is_numeric($categoryStatus) && $categoryStatus > -1) {
			$whereCondition .= " AND category.categoryStatus = " . $categoryStatus . " ";
		}
		
		//Get count of record available in table
		$count = $this->getCountByWhere("category.categoryId", $from, $whereCondition);
		
		//Get paging detail
		$paggingDetails = DBUtil::getPaggingDetails($currentPage, $count, Config::getSParameter('RECORDS_PER_PAGE'));
		
		$sql = "SELECT category.categoryId, category.parentCategoryId, category.category, category.categoryType, category.image, " .
				"category.fromTime, category.language, category.size, category.sortBy, " .
				"category.includeKeywords, category.excludeKeywords, category.includePublisher, category.excludePublisher, " .
				"category.includeCountry, category.excludeCountry, category.includeTopic, category.excludeTopic, category.categoryStatus " .
				"FROM " . $from;
		
		if(!empty($whereCondition)) {
			$sql .= " WHERE " . $whereCondition . " ";
		}
		
		$sql .= "ORDER BY category.categoryId ASC ";
		
		//echo $sql;
		
		$query = $em->createQuery($sql);
		
		$result = $query->setFirstResult($paggingDetails['MYSQL_LIMIT1'])->setMaxResults($paggingDetails['MYSQL_LIMIT2']);
		
		$result = $query->getResult();
		
		if(!empty($result)) {
			for($index = 0; $index < count($result); $index ++) {
				
				if($result[$index]['categoryType'] == 0) {
					$result[$index]['categoryTypeLabel'] = 'User Type';
				} else if($result[$index]['categoryType'] == 1) {
					$result[$index]['categoryTypeLabel'] = 'Category';
				}
				
				$result[$index]['includeKeywordsList'] = array();
				if(!empty($result[$index]['includeKeywords'])) {
					$list = explode(',', $result[$index]['includeKeywords']);
					if(!empty($list)) {
						foreach($list as $element) {
							$element = trim($element);
							$result[$index]['includeKeywordsList'][] = $element;
						}
					}
				}
				
				$result[$index]['excludeKeywordsList'] = array();
				if(!empty($result[$index]['excludeKeywords'])) {
					$list = explode(',', $result[$index]['excludeKeywords']);
					if(!empty($list)) {
						foreach($list as $element) {
							$element = trim($element);
							$result[$index]['excludeKeywordsList'][] = $element;
						}
					}
				}
				
				$result[$index]['includePublisherList'] = array();
				if(!empty($result[$index]['includePublisher'])) {
					$list = explode(',', $result[$index]['includePublisher']);
					if(!empty($list)) {
						foreach($list as $element) {
							$element = trim($element);
							$result[$index]['includePublisherList'][] = $element;
						}
					}
				}
				
				$result[$index]['excludePublisherList'] = array();
				if(!empty($result[$index]['excludePublisher'])) {
					$list = explode(',', $result[$index]['excludePublisher']);
					if(!empty($list)) {
						foreach($list as $element) {
							$element = trim($element);
							$result[$index]['excludePublisherList'][] = $element;
						}
					}
				}
				
				$result[$index]['includeCountryList'] = array();
				if(!empty($result[$index]['includeCountry'])) {
					$list = explode(',', $result[$index]['includeCountry']);
					if(!empty($list)) {
						foreach($list as $element) {
							$element = trim($element);
							$result[$index]['includeCountryList'][] = $element;
						}
					}
				}
				
				$result[$index]['excludeCountryList'] = array();
				if(!empty($result[$index]['excludeCountry'])) {
					$list = explode(',', $result[$index]['excludeCountry']);
					if(!empty($list)) {
						foreach($list as $element) {
							$element = trim($element);
							$result[$index]['excludeCountryList'][] = $element;
						}
					}
				}
				
				$result[$index]['includeTopicList'] = array();
				if(!empty($result[$index]['includeTopic'])) {
					$list = explode(',', $result[$index]['includeTopic']);
					if(!empty($list)) {
						foreach($list as $element) {
							$element = trim($element);
							$result[$index]['includeTopicList'][] = $element;
						}
					}
				}
				
				$result[$index]['excludeTopicList'] = array();
				if(!empty($result[$index]['excludeTopic'])) {
					$list = explode(',', $result[$index]['excludeTopic']);
					if(!empty($list)) {
						foreach($list as $element) {
							$element = trim($element);
							$result[$index]['excludeTopicList'][] = $element;
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
	 * This function remove acategory
	 * @param int $categoryId
	 */
	public function removeCategory($categoryId) {
		//Remove child first
		$this->deleteBy(new Category(), array('parentCategoryId'=>$categoryId));
		
		//Remove category
		return $this->deleteBy(new Category(), array('categoryId'=>$categoryId));
	}

	/**
	 * This function change the cateory status
	 * @param array $categoryDetail
	 */
	public function changeCategoryStatus($categoryDetail = array()) {
		if(empty($categoryDetail) || empty($categoryDetail['categoryId'])) {
			return array();
		}
		
		if($categoryDetail['categoryStatus'] < 0 || $categoryDetail['categoryStatus'] > 1) {
			return array();
		}
		
		$category = new Category();
		$category->setCategoryId($categoryDetail['categoryId']);
		
		return $this->update($category, array('categoryStatus'=>$categoryDetail['categoryStatus']));
	}

	/**
	 * This function change the cateory name
	 * @param array $categoryDetail
	 */
	public function changeCategory($categoryDetail = array()) {
		if(empty($categoryDetail) || empty($categoryDetail['categoryId']) || empty($categoryDetail['category'])) {
			return array();
		}
		
		$category = new Category();
		$category->setCategoryId($categoryDetail['categoryId']);
		
		return $this->update($category, array('category'=>$categoryDetail['category']));
	}
	
	/**
	 * This function get the category detail and also create extra collection by string
	 * @param number $categoryId
	 */
	public function getCaegoryByCategoryId($categoryId = 0) {
		if(empty($categoryId)) {
			return array();
		}
		$categoryDetail = $this->findSingleDetailBy(new Category(), array('categoryId'=>$categoryId));
		if(empty($categoryDetail)) {
			return array();
		}
		
		//Check for included keywrods
		$categoryDetail['includeKeywordsList'] = DBUtil::explode(',', $categoryDetail['includeKeywords']);
		
		//Check for excluded keywrods
		$categoryDetail['excludeKeywordsList'] = DBUtil::explode(',', $categoryDetail['excludeKeywords']);
		
		//Check for included publisher
		$categoryDetail['includePublisherList'] = DBUtil::explode(',', $categoryDetail['includePublisher']);
		
		//Check for exclude publisher
		$categoryDetail['excludePublisherList'] = DBUtil::explode(',', $categoryDetail['excludePublisher']);
		
		//Check for include country
		$categoryDetail['includeCountryList'] = DBUtil::explode(',', $categoryDetail['includeCountry']);
		
		//Check for exclude country
		$categoryDetail['excludeCountryList'] = DBUtil::explode(',', $categoryDetail['excludeCountry']);
		
		//Check for include topic
		$categoryDetail['includeTopicList'] = DBUtil::explode(',', $categoryDetail['includeTopic']);
		
		//Check for exclude topic
		$categoryDetail['excludeTopicList'] = DBUtil::explode(',', $categoryDetail['excludeTopic']);
		
		return $categoryDetail;
	}
	
	public function getSpikeCriteriaBYCategory($hour, $categoryDetail = array()) {
		$criteria = array();
		
		//Set language
		if(empty($categoryDetail['language'])) {
			$criteria['language'] = 'en';
		} else {
			$criteria['language'] = $categoryDetail['language'];
		}
		
		//Set sort by
		if(empty($categoryDetail['sort_by'])) {
			$criteria['sort_by'] = 'fb_total_engagement';
		} else {
			$criteria['sort_by'] = $categoryDetail['sortBy'];
		}
		
		//Set size
		if(empty($categoryDetail['size'])) {
			$criteria['size'] = '150';
		} else {
			$criteria['size'] = $categoryDetail['size'];
		}
		//Make size to be number of send as string
		$criteria['size'] = intval($criteria['size']);
		
		//Set filter
		$filter = '';
		
		//Add include keywrods into filter
		if(!empty($categoryDetail['includeKeywords'])) {
			$keywords = $this->getFilterString($categoryDetail['includeKeywords'], false);
			
			if(!empty($keywords)) {
				if(!empty($filter)) {
					$filter .= ' AND ';
				}
				$filter .= '(' . $keywords . ')';
			}
		}

		//Add exclude keywrods in filter
		if(!empty($categoryDetail['excludeKeywords'])) {
			$keywords = $this->getFilterString($categoryDetail['excludeKeywords'], true);
			
			if(!empty($keywords)) {
				if(!empty($filter)) {
					$filter .= ' AND ';
				}
				$filter .= '(' . $keywords . ')';
			}
		}

		//Add include publisher in filter
		if(!empty($categoryDetail['includePublisher'])) {
			$keywords = $this->getFilterString($categoryDetail['includePublisher'], false);
			
			if(!empty($keywords)) {
				if(!empty($filter)) {
					$filter .= ' AND ';
				}
				$filter .= ' publisher:(' . $keywords . ')';
			}
		}

		//Add exclude publisher in filter
		if(!empty($categoryDetail['excludePublisher'])) {
			$keywords = $this->getFilterString($categoryDetail['excludePublisher'], false);
			
			if(!empty($keywords)) {
				if(!empty($filter)) {
					$filter .= ' AND ';
				}
				$filter .= ' -publisher:(' . $keywords . ')';
			}
		}

		//Add include country in filter
		if(!empty($categoryDetail['includeCountry'])) {
			$keywords = $this->getFilterString($categoryDetail['includeCountry'], false);
			
			if(!empty($keywords)) {
				$keywords = strtolower($keywords);
				if(!empty($filter)) {
					$filter .= ' AND ';
				}
				$filter .= ' country_code:(' . $keywords . ')';
			}
		}

		//Add exclude country in filter
		if(!empty($categoryDetail['excludeCountry'])) {
			$keywords = $this->getFilterString($categoryDetail['excludeCountry'], false);
			
			if(!empty($keywords)) {
				$keywords = strtolower($keywords);
				if(!empty($filter)) {
					$filter .= ' AND ';
				}
				$filter .= ' -country_code:(' . $keywords . ')';
			}
		}

		//Add include topic in filter
		if(!empty($categoryDetail['includeTopic'])) {
			$keywords = $this->getFilterString($categoryDetail['includeTopic'], false);
			
			if(!empty($keywords)) {
				if(!empty($filter)) {
					$filter .= ' AND ';
				}
				$filter .= ' categories:(' . $keywords . ')';
			}
		}

		//Add exclude country in filter
		if(!empty($categoryDetail['excludeTopic'])) {
			$keywords = $this->getFilterString($categoryDetail['excludeTopic'], false);
			
			if(!empty($keywords)) {
				if(!empty($filter)) {
					$filter .= ' AND ';
				}
				$filter .= ' -categories:(' . $keywords . ')';
			}
		}
		
		if(!empty($categoryDetail['fromTime']) && is_numeric($categoryDetail['fromTime'])) {
			$hour = $categoryDetail['fromTime'];
		}
		
		$criteria['from'] = ((round(microtime(true) * 1000)) - ($hour * 60 * 60 * 1000));
		$criteria['find_related'] = false;
		$criteria['video_only'] = false;
		
		//Set filter
		$criteria['filters'] = array($filter);
		
		return $criteria;
	}
	
	/**
	 * 
	 * @param array $list
	 * @param string $operator
	 */
	private function getFilterString($list = '', $isNegative = false, $operator = 'OR') {
		if(!is_array($list)) {
			$list = DBUtil::explode(',', $list);
		}
		
		$keywords = '';
		if(!empty($list)) {
			foreach($list as $includeKeywords) {
				if(!empty($keywords)) {
					$keywords .= ' ' . $operator . ' ';
				}
				
				$leftAnglePos = strpos($includeKeywords, '[');
				$rightAnglePos = strpos($includeKeywords, ']');
				$category = '';
				if($leftAnglePos === false && $rightAnglePos === false) {
					$category =  '"'. $includeKeywords . '"';
				} else {
					$category = $includeKeywords;
					$category = str_replace("[", '"', $category);
					$category = str_replace("]", '"', $category);
					
					$category = "(" . $category . ")";
				}
				
				if($isNegative) {
					$keywords = $keywords . '-' . $category;
				} else {
					$keywords = $keywords . $category;
				}
			}
		}
		
		return $keywords;
	}
	
	/**
	 * This function return user type and category
	 * @param string $isCategory
	 * @param number $categoryStatus
	 */
	public function getUserType($parentCategoryId = 0, $categoryType = 1, $categoryStatus = 0) {
		$em = $this->getDoctrine()->getManager();
		
		//$from for entity name (table name)
		$from = "DB\\Bundle\\AppBundle\Entity\\Category category ";
		
		$whereCondition = "";
		
		if(is_numeric($parentCategoryId) && $parentCategoryId > -1) {
			$whereCondition .= "category.parentCategoryId = " . $parentCategoryId . " ";
		}
		
		if(is_numeric($categoryStatus) && $categoryStatus > -1) {
			$whereCondition .= " AND category.categoryStatus = " . $categoryStatus . " ";
		}
		
		if(is_numeric($categoryType) && $categoryType > -1) {
			$whereCondition .= " AND category.categoryType = " . $categoryStatus . " ";
		}
		
		$sql = "SELECT category.categoryId, category.parentCategoryId, category.category, category.categoryType, category.image, " .
				"category.fromTime, category.language, category.size, category.sortBy, " .
				"category.includeKeywords, category.excludeKeywords, category.includePublisher, category.excludePublisher, " .
				"category.includeCountry, category.excludeCountry, category.includeTopic, category.excludeTopic, category.categoryStatus " .
				"FROM " . $from;
		
		if(!empty($whereCondition)) {
			$sql .= " WHERE " . $whereCondition . " ";
		}
		
		$sql .= "ORDER BY category.categoryId ASC ";
		
		//echo $sql;
		
		$query = $em->createQuery($sql);
		
		$result = $query->getResult();
		
		return $result;
	}
	
	/**
	 * This function return all user types with there category
	 */
	public function getUserTypeWithCategory($categoryStatus = 0, $isChild = true) {
		$userTypeList = array();
		//Get all user tyep
		$userTypeDetailList = $this->getUserType(0, 0, $categoryStatus);
		if(!empty($userTypeDetailList)) {
			foreach($userTypeDetailList as $userTypeDetail) {
				$record = array();
				
				$record['categoryId'] 		= $userTypeDetail['categoryId'];
				$record['parentCategoryId'] = $userTypeDetail['parentCategoryId'];
				
				$record['category'] 		= $userTypeDetail['category'];
				$record['categoryType'] 	= $userTypeDetail['categoryType'];
				$record['image'] 			= $userTypeDetail['image'];
				$record['categoryStatus'] 	= $userTypeDetail['categoryStatus'];
				
				if($isChild) {
					$categoryList = array();
					$categoryDetailList = $this->getUserType($record['categoryId'], -11, $categoryStatus);
					if(!empty($categoryDetailList)) {
						foreach($categoryDetailList as $categoryDetail) {
							$categoryRecord = array();
					
							$categoryRecord['categoryId'] 		= $categoryDetail['categoryId'];
							$categoryRecord['parentCategoryId'] = $categoryDetail['parentCategoryId'];
					
							$categoryRecord['category'] 		= $categoryDetail['category'];
							$categoryRecord['categoryType'] 	= $categoryDetail['categoryType'];
							$categoryRecord['image'] 			= $categoryDetail['image'];
							$categoryRecord['categoryStatus'] 	= $categoryDetail['categoryStatus'];
							
							$categoryList[] = $categoryRecord;
						}
					}
					
					$record['categoryList'] = $categoryList;
				}
				
				$userTypeList[] = $record;
			}
		}
		
		return $userTypeList;
	}
	
	/**
	 * This function return all category those are seelcted by account users
	 */
	public function getAccountCategoryList() {
		$em = $this->getDoctrine()->getManager();
		
		//$from for entity name (table name)
		$from = "DB\\Bundle\\AppBundle\Entity\\Category category, DB\\Bundle\\AppBundle\Entity\\AccountCategory accountCategory ";
		
		$whereCondition = "category.categoryType = 1 AND category.categoryId = accountCategory.categoryId "; 
		
		$sql = "SELECT DISTINCT category.categoryId, category.parentCategoryId, category.category, category.categoryType, category.image, " .
				"category.fromTime, category.language, category.size, category.sortBy, " .
				"category.includeKeywords, category.excludeKeywords, category.includePublisher, category.excludePublisher, " .
				"category.includeCountry, category.excludeCountry, category.includeTopic, category.excludeTopic, category.categoryStatus " .
				"FROM " . $from;
		
		if(!empty($whereCondition)) {
			$sql .= " WHERE " . $whereCondition . " ";
		}
		
		$sql .= "ORDER BY category.categoryId ASC ";
		
		//echo $sql;
		
		$query = $em->createQuery($sql);
		
		$result = $query->getResult();
		
		return $result;
	}
	
	/**
	 * This function return all default category
	 */
	public function getAllDefaultCategoryList($parentCategoryId = '') {
		if(empty($parentCategoryId)) {
			$parentCategoryId = Config::getSParameter('POSTR_DEFAULT_USER_TYPE');
		}
		return $this->findDetailBy(new Category(), array('parentCategoryId'=>$parentCategoryId, 'categoryStatus'=>Category::CATEGORY_STATUS_ENABLE)); 
	}

	/**
	 * This function return all category
	 * @param interger $accountId
	 */
	public function getAllDefaultCategoryMap() {
		$categoryList = $this->getAllDefaultCategoryList();
		$categoryMap = array();
		if(!empty($categoryList)) {
			foreach($categoryList as $category) {
				$categoryMap[$category['categoryId']] = $category;
			}
		}
	
		return $categoryMap;
	}
	
	/**
	 * This function return all account category list
	 * @param interger $accountId
	 */
	public function getCategoryListByAccount($accountId) {
		$em = $this->getDoctrine()->getManager();
		
		//$from for entity name (table name)
		$from = "DB\\Bundle\\AppBundle\Entity\\Category category,  DB\\Bundle\\AppBundle\Entity\\AccountCategory accountCategory ";
		
		$whereCondition = "category.categoryType = 1 AND category.categoryId = accountCategory.categoryId AND accountCategory.accountId = " . $accountId; 
		
		$sql = "SELECT category.categoryId, category.parentCategoryId, category.category, category.categoryType, category.image, " .
				"category.fromTime, category.language, category.size, category.sortBy, " .
				"category.includeKeywords, category.excludeKeywords, category.includePublisher, category.excludePublisher, " .
				"category.includeCountry, category.excludeCountry, category.includeTopic, category.excludeTopic, category.categoryStatus " .
				"FROM " . $from;
		
		if(!empty($whereCondition)) {
			$sql .= " WHERE " . $whereCondition . " ";
		}
		
		$sql .= "ORDER BY category.categoryId ASC ";
		
		//echo $sql;
		
		$query = $em->createQuery($sql);
		$result = $query->getResult();
		
		return $result;
	}
	
	/**
	 * This function return all account category map
	 * @param interger $accountId
	 */
	public function getCategoryMapByAccount($accountId) {
		$categoryList = $this->getCategoryListByAccount($accountId);
		$categoryMap = array();
		if(!empty($categoryList)) {
			foreach($categoryList as $category) {
				$categoryMap[$category['categoryId']] = $category;
			}
		}
		
		return $categoryMap;
	}
}
?>