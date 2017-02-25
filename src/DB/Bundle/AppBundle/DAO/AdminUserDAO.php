<?php
namespace DB\Bundle\AppBundle\DAO;

use DB\Bundle\CommonBundle\Base\BaseDAO;
use DB\Bundle\AppBundle\Entity\AdminUser;
use DB\Bundle\CommonBundle\Util\DBUtil;
use DB\Bundle\AppBundle\Common\Config;

/**
 * Class For AdminUser DAO, This class is responsible for manage database 
 * operation for account table/entity
 *
 * @namespace DB\Bundle\AppBundle\DAO
 *
 * @author Dipak Patil
 */
class AdminUserDAO extends BaseDAO { 
	/**
	 * Always need doctrim object to initilise Admin User dao object
	 * @param $_dm - Doctrime object
	 */
	function __construct($_dm) {
		parent :: __construct($_dm);
	}
	
	/**
	 * this function will be check user exist or not, If exist 
	 * then return all user information, Otherwise return blank array
	 * @param string $email
	 * @param string $password
	 */
	public function login($email, $password) {
		return $this->findSingleDetailBy(new AdminUser(), array('email'=>$email, 'password'=>$password));
	}
	
	/**
	 * This function add new user
	 * @param array $userDetail
	 */
	public function addUser($userDetail = array()) {	
		if(empty($userDetail) || empty($userDetail['email']) || empty($userDetail['password'])) {
			return false;
		}
	
		if(!empty($userDetail['password'])) {
			$userDetail['password'] = DBUtil::getPassword($userDetail['password']);
		}

		if(empty($userDetail['name'])) {
			$name = explode('@', $userDetail['email']);
			if(!empty($name[0])) {
				$userDetail['name'] = $name[0];
			}
		}
	
		$adminUser = new AdminUser();
	
		$adminUser->setName($userDetail['name']);
		$adminUser->setEmail($userDetail['email']);
		$adminUser->setPassword($userDetail['password']);
	
		$adminUser = $this->save($adminUser);
	
		$newUserDetail = false;
		if(is_object($adminUser)) {
			$newUserDetail = $adminUser->toArray();
		}
		return $newUserDetail;
	}
	
	/**
	 * This function edit accoun detail
	 * @param array $userDetail
	 */
	public function editUser($userDetail = array()) {
		if(empty($userDetail) || empty($userDetail['adminUserId'])) {
			return false;
		}
		
		$record = array();
	
		//Set defautl data
		if(!empty($userDetail['email'])) {
			$record['email'] = $userDetail['email'];
		}
	
		if(!empty($userDetail['name'])) {
			$record['name'] = $userDetail['name'];
		}
	
		//Set password
		if(!empty($userDetail['password'])) {
			$hash = DBUtil::getPassword($userDetail['password']);
			if($hash) {
				$record['password'] = $hash;
			}
		}
	
		$updatedUserDetail = array();
		if(!empty($record) && !empty($userDetail['adminUserId'])) {
			$adminUser = new AdminUser();
			$adminUser->setAdminUserId($userDetail['adminUserId']);
			
			$adminUser = $this->update($adminUser, $record);
				
			if(is_object($adminUser)) {
				$updatedUserDetail = $adminUser->toArray();
			}
			
			return $updatedUserDetail;
		} else {
			return false;
		}
	}
	
	/**
	 * This function return account List
	 * @param string $search
	 * @param number $currentPage
	 * @return mixed[]  list of advertiser and pagination
	 */
	public function getAdminUserList($search = '', $currentPage = 1) {
		$em = $this->getDoctrine()->getManager();
	
		//$from for entity name (table name)
		$from = "DB\\Bundle\\AppBundle\Entity\\AdminUser adminUser ";
		
		$whereCondition = "";
		
		if(!empty($search)) {
			$whereCondition = "adminUser.name LIKE '%" . $search . "%' " . 
							"OR adminUser.email LIKE '%" . $search . "%' " ;
		}
		
		//Get count of record available in table
		$count = $this->getCountByWhere("adminUser.adminUserId", $from, $whereCondition);
	
		//Get paging detail
		$paggingDetails = DBUtil::getPaggingDetails($currentPage, $count, Config::getSParameter('RECORDS_PER_PAGE'));
	
		$sql = "SELECT adminUser.adminUserId, adminUser.name, adminUser.email " .
			"FROM " . $from;
		
		if(!empty($whereCondition)) {
			$sql .= " WHERE " . $whereCondition . " ";
		}
		
		$sql .= "ORDER BY adminUser.adminUserId DESC ";
	
		//echo $sql;
	
		$query = $em->createQuery($sql);
	
		$result = $query->setFirstResult($paggingDetails['MYSQL_LIMIT1'])->setMaxResults($paggingDetails['MYSQL_LIMIT2']);
		$result = $query->getResult();
		
	
		$adminUserList = array();
		$adminUserList['PAGING'] = $paggingDetails;
		$adminUserList['LIST'] = $result;
	
		return $adminUserList;
	}
	
	/**
	 * This function get the adminUser detail and also create extra collection by string
	 * @param number $adminUserId
	 */
	public function getAdminUserByadminUserId($adminUserId = 0) {
		if(empty($adminUserId)) {
			return array();
		}
		$adminUserDetail = $this->findSingleDetailBy(new AdminUser(), array('adminUserId'=>$adminUserId));
		if(empty($adminUserDetail)) {
			return array();
		}
	
		return $adminUserDetail;
	}
}
?>