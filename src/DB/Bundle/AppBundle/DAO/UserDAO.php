<?php
namespace DB\Bundle\AppBundle\DAO;

use DB\Bundle\CommonBundle\Base\BaseDAO;
use DB\Bundle\AppBundle\Entity\User;
use DB\Bundle\CommonBundle\Util\DBUtil;
use DB\Bundle\CommonBundle\ApiClient\DBSendgridClient;
use DB\Bundle\AppBundle\Common\Config;
use DB\Bundle\AppBundle\Entity\Account;

/**
 * Class For Account DAO, This class is responsible for manage database 
 * operation for account table/entity
 *
 * @namespace DB\Bundle\AppBundle\DAO
 *
 * @author Dipak Patil
 */
class UserDAO extends BaseDAO { 
	const USER_STATUS_ACTIVE = '0';
	const USER_STATUS_INACTIVE = '1';
	
	/**
	 * Always need doctrim object to initilise User dao object
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
		return $this->findSingleDetailBy(new User(), array('email'=>$email, 'password'=>$password));
	}
	
	/**
	 * This function will be check user exist or not by unique key, If exist 
	 * then return all user information, Otherwise return blank array
	 * @param string $email
	 * @param string $password
	 */
	public function loginByUniqueKey($uniqueKey) {
		return $this->findSingleDetailBy(new User(), array('uniqueKey'=>$uniqueKey));
	}
	
	/**
	 * This function add new user
	 * @param array $userDetail
	 */
	public function addUser($userDetail = array()) {
		if(empty($userDetail)) {
			return false;
		}
		
		//Set defautl data
		if(!empty($userDetail['name']) && empty($userDetail['firstName']) && empty($userDetail['lastName'])) {
			$name = explode(' ', $userDetail['name']);
			if(!empty($name[0])) {
				$userDetail['firstName'] = $name[0];
			}
				
			if(!empty($name[1])) {
				$userDetail['lastName'] = $name[1];
			}
		}
		
		if(empty($userDetail['lastName'])) {
			$userDetail['lastName'] = '';
		}
		
		//Set password
		if(!empty($userDetail['password'])) {
			$hash = DBUtil::getPassword($userDetail['password']);
			if($hash) {
				$userDetail['password'] = $hash;
			}
		}
		
		if(empty($userDetail['profile'])) {
			$userDetail['profile'] = '';
		}
		
		if(empty($userDetail['fbId'])) {
			$userDetail['fbId'] = '';
		}
		
		if(empty($userDetail['senderId'])) {
			$userDetail['senderId'] = '';
		}
		
		if(empty($userDetail['twId'])) {
			$userDetail['twId'] = '';
		}
		
		if(empty($userDetail['timezone'])) {
			$userDetail['timezone'] = '';
		}
		
		if(empty($userDetail['userType'])) {
			$userDetail['userType'] = '0';
		}
		
		if(empty($userDetail['userStatus'])) {
			$userDetail['userStatus'] = '1';
		}
		
		if(empty($userDetail['uniqueKey'])) {
			$userDetail['uniqueKey'] = DBUtil::getUniqueKey();
		}
		
		if(empty($userDetail['lastLoginDate'])) {
			$userDetail['lastLoginDate'] = new \DateTime();
		}
		
		if(empty($userDetail['uniqueToken'])) {
			$userDetail['uniqueToken'] = '';;
		}
		
		if(empty($userDetail['tokenValidDate'])) {
			$userDetail['tokenValidDate'] = new \DateTime();
		}
		
		$user = new User();
		
		$user->setAccountId($userDetail['accountId']);
		
		$user->setFirstName($userDetail['firstName']);
		$user->setLastName($userDetail['lastName']);
		
		$user->setEmail($userDetail['email']);
		$user->setPassword($userDetail['password']);
		
		$user->setFbId($userDetail['fbId']);
		$user->setSenderId($userDetail['senderId']);
		$user->setTwId($userDetail['twId']);
		
		$user->setTimezone($userDetail['timezone']);
		
		$user->setUserType($userDetail['userType']);
		$user->setUserStatus($userDetail['userStatus']);
		$user->setUniqueKey($userDetail['uniqueKey']);
		
		$user->setLastLoginDate($userDetail['lastLoginDate']);
		
		$user->setUniqueToken($userDetail['uniqueToken']);
		$user->setTokenValidDate($userDetail['tokenValidDate']);
		
		$user = $this->save($user);
		
		$newDetail = false;
		if(is_object($user)) {
			//Make account status as user created
			$account = new Account();
			$account->setAccountId($userDetail['accountId']);
			
			$accountDAO = new AccountDAO($this->getDoctrine());
			$accountDAO->update($account, array('accountStatus'=>AccountDAO::ACCOUNT_STATUS_USER_CREATED));
			
			$newDetail = $user->toArray();
		}
		return $newDetail;
	}
	
	/**
	 * This function edit accoun detail
	 * @param array $userDetail
	 */
	public function editUser($userDetail = array()) {
		if(empty($userDetail)) {
			return false;
		}
		
		$record = array();
		
		//Set defautl data		
		if(!empty($userDetail['accountId'])) {
			$record['accountId'] = $userDetail['accountId'];
		}
		
		if(!empty($userDetail['name']) && empty($userDetail['firstName']) && empty($userDetail['lastName'])) {
			$name = explode(' ', $userDetail['name']);
			if(!empty($name[0])) {
				$record['firstName'] = $name[0];
			}
			
			if(!empty($name[1])) {
				$record['lastName'] = $name[1];
			}
		}
		
		if(!empty($userDetail['firstName'])) {
			$record['firstName'] = $userDetail['firstName'];
		}

		if(!empty($userDetail['lastName'])) {
			$record['lastName'] = $userDetail['lastName'];
		}

		if(!empty($userDetail['email'])) {
			$record['email'] = $userDetail['email'];
		}
		
		//Set password
		if(!empty($userDetail['password'])) {
			$hash = DBUtil::getPassword($userDetail['password']);
			if($hash) {
				$record['password'] = $hash;
			}
		}

		if(!empty($userDetail['profile'])) {
			$record['profile'] = $userDetail['profile'];
		}

		if(!empty($userDetail['fbId'])) {
			$record['fbId'] = $userDetail['fbId'];
		}

		if(!empty($userDetail['senderId'])) {
			$record['senderId'] = $userDetail['senderId'];
		}

		if(!empty($userDetail['twId'])) {
			$record['twId'] = $userDetail['twId'];
		}

		if(!empty($userDetail['timezone'])) {
			$record['timezone'] = $userDetail['timezone'];
		}

		if(!empty($userDetail['userType'])) {
			$record['userType'] = $userDetail['userType'];
		}

		if(!empty($userDetail['userStatus'])) {
			$record['userStatus'] = $userDetail['userStatus'];
		}

		if(!empty($userDetail['uniqueKey'])) {
			$record['uniqueKey'] = $userDetail['uniqueKey'];
		}

		if(!empty($userDetail['lastLoginDate'])) {
			$record['lastLoginDate'] = $userDetail['lastLoginDate'];
		}

		if(!empty($userDetail['uniqueToken'])) {
			$record['uniqueToken'] = $userDetail['uniqueToken'];
		}

		if(!empty($userDetail['tokenValidDate'])) {
			$record['tokenValidDate'] = $userDetail['tokenValidDate'];
		}
		
		$updatedUserDetail = array();
		if(!empty($record)) {
			$user = new User();
			$user->setUserId($userDetail['userId']);
			
			$user = $this->update($user, $record);
			
			if(is_object($user)) {
				$updatedUserDetail = $user->toArray();
			}
		}
		
		return $updatedUserDetail;
	}
	
	/**
	 * This function update timezone in user table and return detail
	 * @param array $param
	 */
	public function manageUserTimezone($param = array()) {
		if(empty($param) || empty($param['userId'])) {
			return array();
		}
		
		$record = array();
		$record['userId'] = $param['userId'];
		if(!empty($param['timezone'])) {
			$record['timezone'] = $param['timezone'];
		} else {
			$record['timezone'] = 'America/New_York';
		}
		
		return $this->editUser($record);
	}
	
	/**
	 * This function return user detail by facebook detail
	 */
	public function getFBLogin($fbUserDetail = array()) {
		if(empty($fbUserDetail) || empty($fbUserDetail['fbId'])) {
			return array();
		}
		
		$userDetail = $this->findSingleDetailBy(new User(), array('fbId'=>$fbUserDetail['fbId']));;
		if(empty($userDetail) && !empty($fbUserDetail['email'])) {
			$userDetail = $this->findSingleDetailBy(new User(), array('eamil'=>$userDetail['email']));
			if(!empty($userDetail) && !empty($userDetail['fbId'])) {
				$this->update((new User())->setUserId($userDetail['userId']), array('fbId'=>$fbUserDetail['fbId']));
				$userDetail['fbId'] = $fbUserDetail['fbId'];
			}
		} else if(empty($userDetail)) {
			//Create account first
			$accountDetail = array();
			$accountDetail['account'] = $fbUserDetail['name'];
			
			$accountDAO = new AccountDAO($this->getDoctrine());
			$accountDetail = $accountDAO->addAccount($accountDetail);
			if(!empty($accountDetail['accountId'])) {
				$fbUserDetail['accountId'] = $accountDetail['accountId'];
				$fbUserDetail['password'] = '';
				$userDetail = $this->addUser($fbUserDetail);
			}
		}
		
		return $userDetail;
	}
	
	/**
	 * This function return user detail by facebook detail
	 */
	public function getTwLogin($fbUserDetail = array()) {
		if(empty($fbUserDetail) || empty($fbUserDetail['twId'])) {
			return array();
		}
		
		$userDetail = $this->findSingleDetailBy(new User(), array('twId'=>$fbUserDetail['twId']));
		
		return $userDetail;
	}
	
	/**
	 * This function return all email notification detail list
	 * @param string $period
	 */
	public function getNotConfirmUsers($noOfDay = 1, $endNoOfDay = 0) {
		$em = $this->getDoctrine()->getManager();
	
		//$from for entity name (table name)
		$from = "DB\\Bundle\\AppBundle\Entity\\User user, DB\\Bundle\\AppBundle\Entity\\Account account ";
	
		$startDate = date('Y-m-d', strtotime('-' . $noOfDay . ' day', strtotime(date('Y-m-d H:i:s'))));
		
		$endDate = date('Y-m-d');
		if($endNoOfDay > 0) {
			$endDate = date('Y-m-d', strtotime('-' . $noOfDay . ' day', strtotime(date('Y-m-d H:i:s'))));
		}
		
		$whereCondition = "user.userStatus = 1 AND user.accountId = account.accountId AND account.accountStatus = 2 " . 
			"AND account.creationDate >= '" . $startDate . "'  AND account.creationDate <= '" . $endDate . "'";
	
		$sql = "SELECT user.userId, user.accountId, user.firstName, user.lastName, user.email, " .
				"user.password, user.fbId, user.senderId, user.twId, user.userType, user.userStatus, " .
				"user.uniqueKey, user.lastLoginDate " .
		"FROM " . $from;
	
		if(!empty($whereCondition)) {
			$sql .= " WHERE " . $whereCondition . " ";
		}
	
		$sql .= "ORDER BY user.userId DESC ";
	
		//echo $sql . "\r\n\r\n";
	
		$query = $em->createQuery($sql);
		$result = $query->getResult();
	
		return $result;
	}
	
	/**
	 * This function send confirmation email to all users those are not yet confirm
	 */
	public function scheduleUserConfirmation($caller, $noOfDay = 2, $endNoOfDay = 1, $reminder = false) {
		//Get all suers
		$userDetailList = $this->getNotConfirmUsers($noOfDay, $endNoOfDay);
		if(!empty($userDetailList)) {
			foreach($userDetailList as $userDetail) {
				$this->sendConfirmationMail($caller, $noOfDay, $userDetail, $reminder);
			}
		} else {
			if(method_exists($caller,'log')) {
				$caller->log('No any not confirm user found for noOfDay: ' . $noOfDay . ' and endNoOfDay: ' . $endNoOfDay);
			}
		}
	}
	
	/**
	 * This function will send confirmation mail to signup person
	 * @param array $userDetail
	 */
	public function sendConfirmationMail($caller, $noOfDay, $userDetail = array(), $reminder = false) {
		if(empty($userDetail['email'])) {
			return;
		}
		
		$caller->addInResponse('userDetail', $userDetail);
		$this->addInResponse('serverAppPath', Config::getSParameter('SERVER_APP_PATH'));
		
		$html = '';
		if($noOfDay > 5) {
			$html = $caller->renderView("DBAppBundle:email:welcome-layout-reminder-last.html.twig", $caller->getResponse());
		} else {
			$html = $caller->renderView("DBAppBundle:email:welcome-layout-reminder.html.twig", $caller->getResponse());
		}
		
		$dbSendgridClient = new DBSendgridClient(Config::getSParameter('SENDGRID_API_KEY_GENERATE_TOKEN'));
						
		$emailDetail = array();
		
		$emailDetail['from'] = Config::getSParameter('FROM_EMAIL');
		$emailDetail['fromName'] = Config::getSParameter('FROM_EMAIL_NAME', Config::DEFAULT_FROM_NAME);
		
		$emailDetail['to'] = $userDetail['email'];
		$emailDetail['bcc'] = array(Config::getSParameter('BCC_EMAIL'));
		$emailDetail['subject'] = 'Please Confirm PostReach Invite';
		
		if($reminder == true) {
			$emailDetail['subject'] = 'Reminder: ' . $emailDetail['subject'];
		}
		
		$emailDetail['body'] = $html;
		
		if(method_exists($caller,'log')) {
			$caller->log('Confirmation email send to ' . $userDetail['email']);
		}
		
		return $dbSendgridClient->sendMail($emailDetail);
	}
}
?>