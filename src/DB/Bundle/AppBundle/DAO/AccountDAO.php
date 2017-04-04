<?php
namespace DB\Bundle\AppBundle\DAO;

use DB\Bundle\CommonBundle\Base\BaseDAO;
use DB\Bundle\CommonBundle\Util\DBUtil;
use DB\Bundle\AppBundle\Common\Config;
use DB\Bundle\AppBundle\Entity\Account;
use DB\Bundle\AppBundle\Entity\SocialPost;
use DB\Bundle\AppBundle\Entity\SocialProfile;
use DB\Bundle\AppBundle\Entity\User;
use DB\Bundle\AppBundle\Entity\SocialPostMetric;
use DB\Bundle\CommonBundle\ApiClient\DBBraintreeClient;
use DB\Bundle\AppBundle\Entity\ArticleNotifyHistory;
use DB\Bundle\AppBundle\Entity\AccountFrequency;
/**
 * Class For Account DAO, This class is responsible for manage database 
 * operation for account table/entity
 *
 * @namespace DB\Bundle\AppBundle\DAO
 *
 * @author Dipak Patil
 */
class AccountDAO extends BaseDAO {
	const ACCOUNT_STATUS_SIGNUP 			= '0';
	const ACCOUNT_STATUS_USER_CREATED 		= '1';
	const ACCOUNT_STATUS_CONFIRM_EMAIL_SEND = '2';
	const ACCOUNT_STATUS_CONFIRM_DONE 		= '3';
	const ACCOUNT_STATUS_TOPIC_SETTING 		= '4';
	const ACCOUNT_STATUS_SOCIAL_SETTING 	= '5';
	const ACCOUNT_STATUS_FREQUENCY_SETTING 	= '6';
	const ACCOUNT_STATUS_PAYMENT_DONE 		= '7';
	const ACCOUNT_STATUS_CANCEL 			= '8';
	
	/**
	 * Always need doctrim object to initilise Account dao object
	 * @param $_dm - Doctrime object
	 */
	function __construct($_dm) {
		parent :: __construct($_dm);
	}
	
	/**
	 * This function add new account
	 * @param array $accountDetail
	 */
	public function addAccount($accountDetail = array()) {
		if(empty($accountDetail)) {
			return false;
		}
		
		//Set defautl data		
		if(empty($accountDetail['account'])) {
			$accountDetail['account'] = '';
		}
		
		if(empty($accountDetail['creationDate'])) {
			$accountDetail['creationDate'] = new \DateTime();
		} else {
			$accountDetail['creationDate'] = new \DateTime($accountDetail['creationDate']);
		}
		
		if(empty($accountDetail['endDate'])) {
			$date = new \DateTime();
			$date->add(new \DateInterval('P365D'));
			
			$accountDetail['endDate'] = $date;
		} else {
			$accountDetail['endDate'] = new \DateTime($accountDetail['endDate']);
		}
		
		if(empty($accountDetail['apiKey'])) {
			$accountDetail['apiKey'] = DBUtil::getUniqueKey();
		}
		
		if(empty($accountDetail['accountStatus'])) {
			$accountDetail['accountStatus'] = self::ACCOUNT_STATUS_SIGNUP;
		}
		
		//set default data
		if(empty($accountDetail['businessTypeId'])) {
			$accountDetail['businessTypeId'] = '69';
		}
		
		if(empty($accountDetail['categoryId'])) {
			$accountDetail['categoryId'] = '0';
		}
		
		if(empty($accountDetail['btCustomerId'])) {
			$accountDetail['btCustomerId'] = '';
		}
		
		if(empty($accountDetail['btCardtoken'])) {
			$accountDetail['btCardtoken'] = '';
		}
		
		if(empty($accountDetail['btCreditCardNo'])) {
			$accountDetail['btCreditCardNo'] = '';
		}
		
		if(empty($accountDetail['btExpirationDate'])) {
			$accountDetail['btExpirationDate'] = '';
		}
		
		if(empty($accountDetail['btCardType'])) {
			$accountDetail['btCardType'] = '';
		}
		
		if(empty($accountDetail['btPlanId'])) {
			$accountDetail['btPlanId'] = '';
		}
		
		if(empty($accountDetail['btSubscriptionId'])) {
			$accountDetail['btSubscriptionId'] = '';
		}
		
		
		$account = new Account();
		
		$account->setAccount($accountDetail['account']);
		
		$account->setBusinessTypeId($accountDetail['businessTypeId']);
		$account->setCategoryId($accountDetail['categoryId']);
		
		$account->setCreationDate($accountDetail['creationDate']);
		$account->setEndDate($accountDetail['endDate']);
		
		$account->setApiKey($accountDetail['apiKey']);
		$account->setAccountStatus($accountDetail['accountStatus']);
		
		//Set braintree field to default
		$account->setBtCustomerId($accountDetail['btCustomerId']);
		$account->setBtCardtoken($accountDetail['btCardtoken']);
		$account->setBtCreditCardNo($accountDetail['btCreditCardNo']);
		$account->setBtExpirationDate($accountDetail['btExpirationDate']);
		$account->setBtCardType($accountDetail['btCardType']);
		
		$account->setBtPlanId($accountDetail['btPlanId']);
		$account->setBtSubscriptionId($accountDetail['btSubscriptionId']);
		
		$account = $this->save($account);
		
		$newDetail = false;
		if(is_object($account)) {
			$newDetail = $account->toArray();
			
			//Create new user
			$userDAO = new UserDAO($this->getDoctrine());
			
			$userDetail = array();
			$userDetail['accountId']= $newDetail['accountId'];
			$userDetail['name'] 	= $newDetail['account'];
			$userDetail['email'] 	= $accountDetail['email'];
			$userDetail['password'] = $accountDetail['password'];
			
			$userDetail = $userDAO->addUser($userDetail);
			
			$newDetail['userDetail'] = $userDetail;
		}
		return $newDetail;
	}
	
	/**
	 * This function edit accoun detail
	 * @param array $accountDetail
	 */
	public function editAccount($accountDetail = array()) {
		if(empty($accountDetail)) {
			return false;
		}
		
		$record = array();
		
		//Set defautl data		
		if(!empty($accountDetail['account'])) {
			$record['account'] = $accountDetail['account'];
		}

		if(!empty($accountDetail['businessTypeId'])) {
			$record['businessTypeId'] = $accountDetail['businessTypeId'];
		}

		if(!empty($accountDetail['categoryId'])) {
			$record['categoryId'] = $accountDetail['categoryId'];
		}

		if(!empty($accountDetail['creationDate'])) {
			$record['creationDate'] = new \DateTime($accountDetail['creationDate']);
		}

		if(!empty($accountDetail['endDate'])) {
			$record['endDate'] = new \DateTime($accountDetail['endDate']);
		}

		if(!empty($accountDetail['apiKey'])) {
			$record['apiKey'] = $accountDetail['apiKey'];
		}
		
		if(!empty($accountDetail['accountStatus'])) {
			$record['accountStatus'] = $accountDetail['accountStatus'];
		}
		
		if(!empty($accountDetail['btCustomerId'])) {
			$record['btCustomerId'] = $accountDetail['btCustomerId'];
		}

		if(!empty($accountDetail['btCardtoken'])) {
			$record['btCardtoken'] = $accountDetail['btCardtoken'];
		}
		
		if(!empty($accountDetail['btCreditCardNo'])) {
			$record['btCreditCardNo'] = $accountDetail['btCreditCardNo'];
		}
		
		if(!empty($accountDetail['btExpirationDate'])) {
			$record['btExpirationDate'] = $accountDetail['btExpirationDate'];
		}
		
		if(!empty($accountDetail['btCardType'])) {
			$record['btCardType'] = $accountDetail['btCardType'];
		}
		
		if(isset($accountDetail['btPlanId'])) {
			$record['btPlanId'] = $accountDetail['btPlanId'];;
		}
		
		if(!empty($accountDetail['btSubscriptionId'])) {
			$record['btSubscriptionId'] = $accountDetail['btSubscriptionId'];
		}
		
		$updateDetail = array();
		if(!empty($record)) {
			$account = new Account();
			$account->setAccountId($accountDetail['accountId']);
			
			$account = $this->update($account, $record);
			
			if(is_object($account)) {
				$updateDetail = $account->toArray();
			}
		}
		
		return $updateDetail;
	}
	
	/**
	 * This function create customer in braintree and save customer ID and payment method to DB
	 * @param unknown $accountDetail
	 */
	public function updatePaymentMethod($accountDetail) {
		if(empty($accountDetail)) {
			return array();
		}
		
		//Create csutomer
		//Create obejct of DBBraintreeClient
		$dbBraintreeClient = new DBBraintreeClient(Config::getSParameter('BRAINTREE_ENVIRONMENT'),
				Config::getSParameter('BRAINTREE_MERCHANT_ID'), Config::getSParameter('BRAINTREE_PUBLIC_KEY'),
				Config::getSParameter('BRAINTREE_PRIVATE_KEY'));
		
		//Check if customer is already created
		$customer = array();
		$isCreate = false;
		if(!empty($accountDetail['btCustomerId'])) {
			$customer = $dbBraintreeClient->updateCustomer($accountDetail);
			
			//Make payment method default
			if(!empty($customer['customer']['cardToken'])) {
				$dbBraintreeClient->makePaymentMethodDefault($customer['customer']['cardToken']);
			}
		} else {
			$customer = $dbBraintreeClient->createCustomer($accountDetail);
			$isCreate = true;
		}
		
		//Assign ids to DB
		if(!empty($customer['customer']['id']) && !empty($customer['customer']['cardToken'])) {
			$accountParam = array();
			$accountParam['accountId'] 			= $accountDetail['accountId'];
			
			$accountParam['btCustomerId'] 		= $customer['customer']['id'];
			$accountParam['btCardtoken'] 		= $customer['customer']['cardToken'];

			$accountParam['btCreditCardNo'] 	= $customer['customer']['creditCardNo'];
			$accountParam['btExpirationDate'] 	= $customer['customer']['expirationDate'];
			$accountParam['btCardType'] 		= $customer['customer']['cardType'];
			
			//Create subscription
			if($isCreate || empty($accountDetail['btSubscriptionId'])) {
				$subscriptionDetail = array();
				$subscriptionDetail['paymentMethodToken'] = $accountParam['btCardtoken'];
				if(!empty($accountDetail['btPlanId'])) {
					//Check plan id s valid or not
					$planDetail = $dbBraintreeClient->getPlan($accountDetail['btPlanId']);
					if(!empty($planDetail)) {
						$subscriptionDetail['planId'] = $accountDetail['btPlanId'];
					} else {
						$subscriptionDetail['planId'] = Config::getSParameter('BRAINTREE_PLAN_ID');
						//$accountParam['btPlanId'] = ''; 
					}
				} else {
					$subscriptionDetail['planId'] = Config::getSParameter('BRAINTREE_PLAN_ID');
					//$accountParam['btPlanId'] = '';
				}

				$subscription = $dbBraintreeClient->createSubscription($subscriptionDetail);
			
				if(isset($subscription['subscription']['id'])) {
					$accountParam['btSubscriptionId'] = $subscription['subscription']['id'];
				}
			}
			
			$accountDetail = $this->editAccount($accountParam);
			
		} else {
			if(!empty($customer['error'])) {
				$accountDetail['error'] = $customer['error'];
			}
		}
		
		return $accountDetail;
	}
	
	/**
	 * This function return all account who having registered with notification
	 */
	public function getAccountListByNotificationSetting() {
		$em = $this->getDoctrine()->getManager();
		
		//$from for entity name (table name)
		$from = "DB\\Bundle\\AppBundle\Entity\\Account account ";
		
		
		$sql = "SELECT account.accountId, account.account, account.businessTypeId, account.categoryId, account.creationDate, account.endDate, account.apiKey, account.accountStatus FROM " . $from;
		
		$sql .= " JOIN DB\\Bundle\\AppBundle\Entity\\NotificationSettings notificationSettings WITH account.accountId = notificationSettings.accountId ORDER BY account.accountId ASC ";
		
		//echo $sql;
		
		$query = $em->createQuery($sql);
		
		$result = $query->getResult();
		
		return $result;
	}
	
	/**
	 * This function return account List
	 * @param string $search
	 * @param number $currentPage
	 * @return mixed[]  list of advertiser and pagination
	 */
	public function getAccountList($search = '', $currentPage = 1, $options = array()) {
		$em = $this->getDoctrine()->getManager();
	
		//$from for entity name (table name)
		$from = "DB\\Bundle\\AppBundle\Entity\\Account account, DB\\Bundle\\AppBundle\Entity\\User user ";
		
		$whereCondition = "account.accountId = user.accountId ";
		
		if(!empty($search)) {
			$whereCondition .= "AND ( account.account LIKE '%" . $search . "%' ";
			$whereCondition .= " OR user.firstName LIKE '%" . $search . "%' ";
			$whereCondition .= " OR user.lastName LIKE '%" . $search . "%' ";
			$whereCondition .= " OR user.email LIKE '%" . $search . "%') ";
		}
		
		
		if(!empty($options['accountStatus'])) {
			if($options['accountStatus'] == '<3') {
				$whereCondition .= "AND account.accountStatus < 3 ";
			} else if($options['accountStatus'] == '<7') {
				$whereCondition .= "AND account.accountStatus < 7 ";
			} else if($options['accountStatus'] == '!8') {
				$whereCondition .= "AND account.accountStatus != 8 ";
			} else {
				$whereCondition .= "AND account.accountStatus = " . $options['accountStatus'] . " ";
			}
		}
		
		//Get count of record available in table
		$count = $this->getCountByWhere("account.accountId", $from, $whereCondition);
	
		//Get paging detail
		$paggingDetails = DBUtil::getPaggingDetails($currentPage, $count, Config::getSParameter('RECORDS_PER_PAGE'));
	
		$sql = "SELECT account.accountId, account.account, account.businessTypeId, " .
				"account.creationDate, account.endDate, account.apiKey, account.accountStatus, " .
				"account.btCustomerId, account.btCardtoken, account.btCreditCardNo, " .
				"account.btExpirationDate, account.btCardType, account.btSubscriptionId, " . 
				"user.firstName, user.lastName, user.email  " .
				"  " .
			"FROM " . $from;
		
		if(!empty($whereCondition)) {
			$sql .= " WHERE " . $whereCondition . " ";
		}
		
		$sql .= "ORDER BY account.accountId DESC ";
	
		//echo $sql;
	
		$query = $em->createQuery($sql);
	
		$result = $query->setFirstResult($paggingDetails['MYSQL_LIMIT1'])->setMaxResults($paggingDetails['MYSQL_LIMIT2']);
		$result = $query->getResult();
		
		//make event type
		if(!empty($result)) {
			for($index = 0; $index < count($result); $index ++) {
				$result[$index]['createdAt'] = $result[$index]['creationDate']->format('Y-m-d');
				$result[$index]['lastUpdatedAt'] = $result[$index]['endDate']->format('Y-m-d');
			}
		}
	
		$accountList = array();
		$accountList['PAGING'] = $paggingDetails;
		$accountList['LIST'] = $result;
	
		return $accountList;
	}
	
	/**
	 * This function remove an account and account all dependancy
	 * @param string $accountId
	 */
	public function removeAccount($accountId = '') {
		if(empty($accountId)) {
			return array();
		}

		/*//Delete Email notification
		$emailHistoryDAO = new EmailHistoryDAO($this->getDoctrine());
		$emailHistoryDAO->removeEmailHistoryByAccount($accountId);
		
		//Delete Email notification
		$emailNotificationDAO = new EmailNotificationDAO($this->getDoctrine());
		$emailNotificationDAO->deleteBy(new EmailNotification(), array('accountId'=>$accountId));
		*/
		//Delete Social post metric
		$socialPostMetricDAO = new SocialPostMetricDAO($this->getDoctrine());
		$socialPostMetricDAO->deleteBy(new SocialPostMetric(), array('accountId'=>$accountId));
		
		//Delete Email Social post
		$socialPostDAO = new SocialPostDAO($this->getDoctrine());
		$socialPostDAO->deleteBy(new SocialPost(), array('accountId'=>$accountId));
		
		//Delete Email Social profile
		$socialProfileDAO = new SocialProfileDAO($this->getDoctrine());
		$socialProfileDAO->deleteBy(new SocialProfile(), array('accountId'=>$accountId));
		
		//Delete user
		$userDAO = new UserDAO($this->getDoctrine());
		$userDAO->deleteBy(new User(), array('accountId'=>$accountId));
		
		//Finaly delete account
		$accountDAO = new AccountDAO($this->getDoctrine());
		$accountDAO->deleteBy(new Account(), array('accountId'=>$accountId));
		
		return array();
	}
	
	/**
	 * This function update account status if accountId and status is not null and empty
	 * @param integer $accountId
	 * @param integer $status
	 */
	public function setAccountStatus($accountId, $status) {
		if(empty($accountId) || empty($status)) {
			return;
		}
		
		$account = new Account();
		$account->setAccountId($accountId);
		
		$this->update($account, array('accountStatus'=>$status));
		
		return array();		
	}
	
	/**
	 * This function add new account by massenger subscription detail
	 * @param array $subscriberDetail
	 */
	public function createAccountByMassenger($subscriberDetail) {
		$response = array();
		$signupForm = array();
		
		$signupForm['account'] = '';
		if(!empty($subscriberDetail['firstName'])) {
			$signupForm['account'] = $subscriberDetail['firstName'];
			$signupForm['firstName'] = $subscriberDetail['firstName'];
		}
		
		if(!empty($subscriberDetail['lastName'])) {
			$signupForm['account'] .= ' ' . $subscriberDetail['lastName'];
			$signupForm['lastName'] = $subscriberDetail['lastName'];
		}
		
		$signupForm['account'] = trim($signupForm['account']);
		
		if(!empty($subscriberDetail['profilePic'])) {
			$signupForm['logo'] = $subscriberDetail['profilePic'];
		}
		
		$accountDetail = $this->addAccount($signupForm);
		
		if(empty($accountDetail)) {
			$response['error'] = 'Problem while creating your account';
			return $response;
		}
		
		$signupForm['accountId'] = $accountDetail['accountId'];
		$signupForm['email'] = '';
		$signupForm['senderId'] = $subscriberDetail['senderId'];
		$signupForm['userType'] = '1';
		$signupForm['password'] = '';
		
		
		$userDAO = new UserDAO($this->getDoctrine());
		$userDetail = $userDAO->addUser($signupForm);
		
		return $userDetail;
	}
	
	/**
	 * This function create new user from facebook profile
	 * @param array $profile
	 */
	public function createAccountByFb($profile) {
		$response = array();
		$signupForm = array();
		
		$signupForm['account'] = '';
		if(!empty($profile['firstName'])) {
			$signupForm['account'] = $profile['firstName'];
			$signupForm['firstName'] = $profile['firstName'];
		}
		
		if(!empty($profile['lastName'])) {
			$signupForm['account'] .= ' ' . $profile['lastName'];
			$signupForm['lastName'] = $profile['lastName'];
		}
		
		$signupForm['account'] = trim($signupForm['account']);
		
		if(!empty($profile['profilePic'])) {
			$signupForm['logo'] = $profile['profilePic'];
		}
		
		$accountDetail = $this->addAccount($signupForm);
		
		if(empty($accountDetail)) {
			$response['error'] = 'Problem while creating your account';
			return $response;
		}
		
		$signupForm['accountId'] = $accountDetail['accountId'];
		$signupForm['email'] = '';
		$signupForm['fbId'] = $profile['id'];
		$signupForm['userType'] = '1';
		$signupForm['password'] = '';
		
		
		$userDAO = new UserDAO($this->getDoctrine());
		$userDetail = $userDAO->addUser($signupForm);
		
		return $userDetail;
	}
	
	/**
	 * This function create new user from facebook profile
	 * @param array $profile
	 */
	public function createAccountByTwitter($profile) {
		$response = array();
		$signupForm = array();
		
		$signupForm['account'] = '';
		if(!empty($profile['name'])) {
			$signupForm['account'] = $profile['name'];
			$signupForm['firstName'] = $profile['name'];
		}
		
		if(!empty($profile['lastName'])) {
			$signupForm['account'] .= ' ' . $profile['name'];
			$signupForm['lastName'] = $profile['name'];
		}
		
		$signupForm['account'] = trim($signupForm['account']);
		
		if(!empty($profile['picture'])) {
			$signupForm['logo'] = $profile['picture'];
		}
		
		$accountDetail = $this->addAccount($signupForm);
		
		if(empty($accountDetail)) {
			$response['error'] = 'Problem while creating your account';
			return $response;
		}
		
		$signupForm['accountId'] = $accountDetail['accountId'];
		$signupForm['email'] = '';
		$signupForm['twId'] = $profile['socialId'];
		$signupForm['userType'] = '1';
		$signupForm['password'] = '';
		
		
		$userDAO = new UserDAO($this->getDoctrine());
		$userDetail = $userDAO->addUser($signupForm);
		
		return $userDetail;
	}
	
	/**
	 * This function return all accounts those are in expected time 
	 * @param string $time
	 * @param string $hourMatch
	 */
	public function getAccountIdsByTimezone($time = '08:00', $hourMatch = '1') {
		$currentDate = new \DateTime();
		$todayDate = DBUtil::format($currentDate, 'Y-m-d');
		
		$time = $time . ":00";
		$currentDate = new \DateTime($todayDate . ' ' . $time);
		$currentDate->modify('1 hour');
		$adjustTime = DBUtil::format($currentDate, 'H:i:s');
		
		$sql = "SELECT DISTINCT accountId FROM `user` " . 
			"WHERE timezone != '' AND " . 
			"CONVERT_TZ(now(), 'UTC', timezone) >  CONCAT('" . $todayDate . "', ' " . $time . "') " . 
			"AND CONVERT_TZ(now(), 'UTC', timezone) <  CONCAT('" . $todayDate . "', ' " . $adjustTime ."')";
		
		//echo $sql . "\r\n\r\n";
		
		return $this->query($sql);
	}
	
	/**
	 * This function return the all available account for sending the email notifications
	 */
	public function getAccountForNotification($noOfAccount = 50) {
		$sql = 'SELECT u.accountId, ah.last_Send ' .
			'FROM `user` u '. 
			'INNER JOIN account a ON u.accountId = a.accountId ' .
			'INNER JOIN notification_settings ns ON u.accountId = ns.accountId ' .
			"INNER JOIN account_frequency af ON u.accountId = af.accountId AND af.category = 'Manual Posts' " .
			'LEFT JOIN ( '. 
				"SELECT DISTINCT accountId, MAX(creationDate) as last_send FROM article_notify_history WHERE notifyType = '" . ArticleNotifyHistory::NOTIFY_TYPE_EMAIL . "' GROUP BY 1) ah " . 
			'ON u.accountId = ah.accountId '.
			"WHERE  a.accountStatus = " . AccountDAO::ACCOUNT_STATUS_PAYMENT_DONE . " AND u.userStatus = " . UserDAO::USER_STATUS_ACTIVE . " AND u.timezone != '' AND ( " .
				"(ah.last_send is null) " .
					"OR ".
				"(((to_days( convert_Tz(NOW(),'UTC',u.timezone)) - to_days(convert_Tz(ah.last_send,'UTC',u.timezone))) >= 1) AND TIME(convert_Tz(now(), 'UTC', u.timezone)) > TIME('" . Config::getSParameter('NOTIFICATION_COMMAND_TIME') . "')))  " .
			"LIMIT 0, $noOfAccount";
		
		//echo $sql;
		return $this->query($sql);
		
		/*$validAccountIds = $this->getAccountIdsByTimezone(Config::getSParameter('NOTIFICATION_COMMAND_TIME'), Config::getSParameter('NOTIFICATION_COMMAND_DELAY_HOUR'));
		
		$accountIdList = '';
		if(!empty($validAccountIds)) {
			foreach($validAccountIds as $accountDetail) {
				if(!empty($accountDetail['accountId'])) {
					if(!empty($accountIdList)) {
						$accountIdList .= ', ';
					}
					$accountIdList .= $accountDetail['accountId'];
				}
			}
		} else {
			//If blank then we dont have any account to send email
			return array();
		}
		
		$em = $this->getDoctrine()->getManager();
		
		//$from for entity name (table name)
		$from = "DB\\Bundle\\AppBundle\Entity\\Account account, DB\\Bundle\\AppBundle\Entity\\NotificationSettings notificationSettings ";
		
		$currentDate = new \DateTime();
		$currentDate->modify('-1 day');
		$todayDate = DBUtil::format($currentDate, 'Y-m-d H:i:s');
		
		$whereCondition = "account.accountId NOT IN (SELECT DISTINCT emailHistory.accountId FROM DB\\Bundle\\AppBundle\Entity\\EmailHistory emailHistory  WHERE emailHistory.creationDate > '" . $todayDate . "') ";
		$whereCondition .= " AND account.accountId = notificationSettings.accountId ";
		
		if($accountIdList != '-1') {
			$whereCondition .= " AND account.accountId IN (" . $accountIdList  . ") ";
		}
		
		$sql = "SELECT account.accountId, account.account, account.businessTypeId, " .
				"account.creationDate, account.endDate, account.apiKey, account.accountStatus, " .
				"account.btCustomerId, account.btCardtoken, account.btCreditCardNo, " .
				"account.btExpirationDate, account.btCardType, account.btSubscriptionId " .
				"FROM " . $from;
		
		if(!empty($whereCondition)) {
			$sql .= " WHERE " . $whereCondition . " ";
		}
		
		$sql .= "ORDER BY account.accountId DESC ";
		
		//echo $sql . "\r\n\r\n";
		
		$query = $em->createQuery($sql);
		
		$result = $query->setFirstResult(0)->setMaxResults($noOfAccount);
		$result = $query->getResult();
		
		return $result;*/
	}
	
	/**
	 * This function return the all available account for sending the email notifications
	 */
	public function getAccountForAutopost($noOfAccount = 50) {
		$sql = 'SELECT u.accountId, ah.last_Send ' .
			'FROM `user` u '. 
			'INNER JOIN account a ON u.accountId = a.accountId ' .
			'INNER JOIN notification_settings ns ON u.accountId = ns.accountId ' .
			"INNER JOIN  account_frequency af ON u.accountId = af.accountId AND af.category = '" . AccountFrequency::ACCOUNT_FREQUENCY_CATEGORY_AUTOPILOT . "' " .
			'LEFT JOIN ( '. 
				"SELECT DISTINCT accountId, MAX(creationDate) as last_send FROM article_notify_history WHERE notifyType = '" . ArticleNotifyHistory::NOTIFY_TYPE_AUTOPOST . "' GROUP BY 1) ah " . 
			'ON u.accountId = ah.accountId '.
			"WHERE  a.accountStatus = " . AccountDAO::ACCOUNT_STATUS_PAYMENT_DONE . " AND u.userStatus = " . UserDAO::USER_STATUS_ACTIVE . " AND u.timezone != '' AND ( " .
				"(ah.last_send is null) " .
                "OR ".
                "(((to_days( convert_Tz(NOW(),'UTC',u.timezone)) - to_days(convert_Tz(ah.last_send,'UTC',u.timezone))) >= 1) AND TIME(convert_Tz(now(), 'UTC', u.timezone)) > TIME('" . Config::getSParameter('NOTIFICATION_COMMAND_TIME') . "')) ) " .
			"LIMIT 0, $noOfAccount";
		
		//echo $sql;
		return $this->query($sql);
		
		/*$validAccountIds = $this->getAccountIdsByTimezone(Config::getSParameter('NOTIFICATION_COMMAND_TIME'), Config::getSParameter('NOTIFICATION_COMMAND_DELAY_HOUR'));
		
		$accountIdList = '';
		if(!empty($validAccountIds)) {
			foreach($validAccountIds as $accountDetail) {
				if(!empty($accountDetail['accountId'])) {
					if(!empty($accountIdList)) {
						$accountIdList .= ', ';
					}
					$accountIdList .= $accountDetail['accountId'];
				}
			}
		} else {
			//If blank then we dont have any account to send email
			return array();
		}
		
		$em = $this->getDoctrine()->getManager();
		
		//$from for entity name (table name)
		$from = "DB\\Bundle\\AppBundle\Entity\\Account account, DB\\Bundle\\AppBundle\Entity\\AccountFrequency accountFrequency ";
		
		$currentDate = new \DateTime();
		$currentDate->modify('-1 day');
		$todayDate = DBUtil::format($currentDate, 'Y-m-d H:i:s');
		
		$whereCondition = "account.accountId NOT IN (SELECT DISTINCT articleNotifyHistory.accountId FROM DB\\Bundle\\AppBundle\Entity\\ArticleNotifyHistory articleNotifyHistory  WHERE articleNotifyHistory.notifyType = '" . ArticleNotifyHistory::NOTIFY_TYPE_AUTOPOST . "' AND articleNotifyHistory.creationDate > '" . $todayDate . "') ";
		$whereCondition .= " AND account.accountId = accountFrequency.accountId ";
		
		if($accountIdList != '-1') {
			$whereCondition .= " AND account.accountId IN (" . $accountIdList  . ") ";
		}
		
		$sql = "SELECT account.accountId, account.account, account.businessTypeId, " .
				"account.creationDate, account.endDate, account.apiKey, account.accountStatus, " .
				"account.btCustomerId, account.btCardtoken, account.btCreditCardNo, " .
				"account.btExpirationDate, account.btCardType, account.btSubscriptionId " .
				"FROM " . $from;
		
		if(!empty($whereCondition)) {
			$sql .= " WHERE " . $whereCondition . " ";
		}
		
		$sql .= "ORDER BY account.accountId DESC ";
		
		//echo $sql . "\r\n\r\n";
		
		$query = $em->createQuery($sql);
		
		$result = $query->setFirstResult(0)->setMaxResults($noOfAccount);
		$result = $query->getResult();
		
		return $result;*/
	}
	
	/**
	 * This funcion cancel the user account
	 * @param integer $accountId
	 */
	public function cancelAccount($accountId) {
		$response = array();
		//Get account detail
		$accountDetail = $this->findSingleDetailBy(new Account(), array('accountId'=>$accountId));
		if(!empty($accountDetail)) {
			//Check if account already canceled
			if($accountDetail['accountStatus'] != AccountDAO::ACCOUNT_STATUS_CANCEL) {
				if(!empty($accountDetail['btSubscriptionId'])) {
					//Calcel subscription at braintree
					$dbBraintreeClient = new DBBraintreeClient(Config::getSParameter('BRAINTREE_ENVIRONMENT'),
							Config::getSParameter('BRAINTREE_MERCHANT_ID'), Config::getSParameter('BRAINTREE_PUBLIC_KEY'),
							Config::getSParameter('BRAINTREE_PRIVATE_KEY'));
					
					$response = $dbBraintreeClient->cancelSubscription($accountDetail['btSubscriptionId']);
					if(isset($response['subscription']['id'])) {
						$response['braintreeStatus'] = true;
					} else {
						$response['braintreeStatus'] = false;
					}
				}
			}
			
			//Set account status status to send confirmation email
			$this->setAccountStatus($accountId, AccountDAO::ACCOUNT_STATUS_CANCEL);
		}
		
		return $response;
	}
}
?>
