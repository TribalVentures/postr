<?php
namespace DB\Bundle\AppBundle\DAO;

use DB\Bundle\CommonBundle\Base\BaseDAO;
use DB\Bundle\CommonBundle\Util\DBUtil;
use DB\Bundle\AppBundle\Common\Config;
use DB\Bundle\AppBundle\Entity\Account;
use DB\Bundle\AppBundle\Entity\EmailNotification;
use DB\Bundle\AppBundle\Entity\SocialPost;
use DB\Bundle\AppBundle\Entity\SocialProfile;
use DB\Bundle\AppBundle\Entity\User;
use DB\Bundle\AppBundle\Entity\SocialPostMetric;
use DB\Bundle\AppBundle\Entity\AccountCategory;
/**
 * Class For Account Category DAO, This class is responsible for manage database 
 * operation for account_category table/entity
 *
 * @namespace DB\Bundle\AppBundle\DAO
 *
 * @author Dipak Patil
 */
class AccountCategoryDAO extends BaseDAO {
	
	/**
	 * Always need doctrim object to initilise Account dao object
	 * @param $_dm - Doctrime object
	 */
	function __construct($_dm) {
		parent :: __construct($_dm);
	}
	
	/**
	 * This fucntion add new account category
	 * @param integer $accountId
	 * @param integer $categoryId
	 * @return array - Return account categorydetail
	 */
	public function addAccountCategory($accountId, $categoryId) {
		$accountCategory = new AccountCategory();
		
		$accountCategory->setAccountId($accountId);
		$accountCategory->setCategoryId($categoryId);
		
		$accountCategory = $this->save($accountCategory);
		
		$newDetail = false;
		if(is_object($accountCategory)) {
			$newDetail = $accountCategory->toArray();
		}
		return $newDetail;
	}
	
	/**
	 * 
	 * @param unknown $accountId
	 * @param unknown $categoryIdList
	 */
	public function addCategoryList($accountId, $categoryIdList) {
		if(empty($categoryIdList)) {
			return array();
		}
		
		//Ignore existing if already in the list of category
		$existingAccountCategoryList = $this->getAccountCategoryList($accountId);
		$deleteCategoryIdList = array();
		if(!empty($existingAccountCategoryList)) {
			foreach($existingAccountCategoryList as $accountCategoryDetail) {
				if(in_array($accountCategoryDetail['categoryId'], $categoryIdList)) {
					//Remove existing category
					$key = array_search($accountCategoryDetail['categoryId'], $categoryIdList);
					if($key !== false) {
						unset($categoryIdList[$key]);
					}
				} else {
					//Add in delete array
					$deleteCategoryIdList[] = $accountCategoryDetail['categoryId'];
				}
			}
		}
		
		//Remove all category from DB those are not selected
		if(!empty($deleteCategoryIdList)) {
			$this->deleteAccoutnCategory($accountId,$deleteCategoryIdList);
		}
		
		$categoryList = array();
		foreach($categoryIdList as $categoryId) {
			$accountCategory = new AccountCategory();
			$accountCategory->setAccountId($accountId);
			$accountCategory->setCategoryId($categoryId);
			
			$categoryList[] = $accountCategory;
		}
		
		$this->saveBatch($categoryList);
	}
	
	/**
	 * This function return all account category
	 * @param integer $accountId
	 */
	public function getAccountCategoryList($accountId) {
		return $this->findDetailBy(new AccountCategory(), array('accountId'=>$accountId));
	}
	
	/**
	 * This function delete category from account
	 * @param unknown $accountId
	 * @param unknown $categoryId
	 */
	public function deleteAccountCategory($accountId, $categoryId = 0) {
		$param = array();
		$param['accountId'] = $accountId;
		
		if($categoryId > 0) {
			$param['categoryId'] = $categoryId;
		}
		
		return $this->deleteBy(new AccountCategory(), $param);
	}
	
	/**
	 * This function delete account category by account id and categoryIds
	 * @param integer $accountId
	 * @param array $categoryIdList
	 */
	public function deleteAccoutnCategory($accountId, $categoryIdList) {
		if(empty($accountId) || empty($categoryIdList)) {
			return array();
		}
		
		$em = $this->getDoctrine()->getManager();
		
		//$from for entity name (table name)
		$from = "DB\\Bundle\\AppBundle\Entity\\AccountCategory accountCategory ";
		
		$whereCondition = "accountCategory.accountId = '" . $accountId . "' " . 
						"AND accountCategory.categoryId IN (0";
		foreach($categoryIdList as $categoryId) {
			$whereCondition .= ", " . $categoryId;
		}
		
		$whereCondition .= ") ";
		
		
		$sql = 'DELETE FROM ' . $from ;
		
		if(!empty($whereCondition)) {
			$sql .= " WHERE " . $whereCondition . " ";
		}
		
		//echo $sql;
		
		$query = $em->createQuery($sql);
		
		$result = $query->getResult();
		
		return $result;
	}
}
?>