<?php

namespace DB\Bundle\AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use DB\Bundle\AppBundle\DAO\UserDAO;
use DB\Bundle\CommonBundle\Util\DBUtil;
use DB\Bundle\AppBundle\DAO\AccountDAO;
use DB\Bundle\AppBundle\Entity\Account;
use DB\Bundle\CommonBundle\Base\BaseController;
use DB\Bundle\AppBundle\DAO\CategoryDAO;
use DB\Bundle\AppBundle\DAO\AccountCategoryDAO;
use DB\Bundle\AppBundle\DAO\SocialProfileDAO;
use DB\Bundle\AppBundle\DAO\AccountFrequencyDAO;
use DB\Bundle\CommonBundle\ApiClient\DBBraintreeClient;
use DB\Bundle\AppBundle\Common\Config;
use DB\Bundle\AppBundle\Entity\SocialProfile;
use DB\Bundle\AppBundle\Entity\User;
use DB\Bundle\CommonBundle\ApiClient\DBSendgridClient;
use DB\Bundle\AppBundle\DAO\SettingDAO;

class IndexController extends DbAppController {
	/**
	 * @Route("/", name="db_postreach")
	 * @Template("DBAppBundle:postr:login.html.twig")
	 */
	public function indexAction() {
		$n = $this->getRequestParam('n', '');
		if(!empty($n) && $n == 'ca') {
			$this->addInResponse('error', 'Your account calcel successfully..');
		}
		
		return $this->getResponse();
	}
	
	/**
	 * @Route("/login", name="db_postreach_login")
	 * @Template("DBAppBundle:postr:login.html.twig")
	 */
	public function loginAction() {
		//Get login detail
		$loginForm = $this->getRequestParam('loginForm', array());
		$errorMesage = 'Invalid email/password. Please try again';
		
		if(empty($loginForm['email']) || empty($loginForm['password'])) {
			$this->addInResponse('error', $errorMesage);
			return $this->getResponse();
		}
		
		$userDAO = new UserDAO($this->getDoctrine());
		$userDetail = $userDAO->login($loginForm['email'], DBUtil::getPassword($loginForm['password']));
		
		if(!empty($userDetail['accountId'])) {
			//Remove password
			unset($userDetail['password']);
			
			//Get account detail
			$accountDAO = new AccountDAO($this->getDoctrine());
			$userDetail['account'] = $accountDAO->findSingleDetailBy(new Account(), array('accountId'=>$userDetail['accountId']));
			
			//check for account calcel
			if(!empty($userDetail['account']['accountStatus']) && $userDetail['account']['accountStatus'] == AccountDAO::ACCOUNT_STATUS_CANCEL) {
				$errorMesage = 'Your account no more active, Please contact to POSTR team to activate..';
				$this->addInResponse('error', $errorMesage);
				return $this->getResponse();
			}
			
			//Get profile image
			$socialProfileDAO = new SocialProfileDAO($this->getDoctrine());
			$fbSocialProfileDetail = $socialProfileDAO->findSingleDetailBy(new SocialProfile(), array('profileType'=>'Facebook', 'accountId'=>$userDetail['accountId']));
				
			if(!empty($fbSocialProfileDetail['picture'])) {
				$userDetail['image'] = $fbSocialProfileDetail['picture'];
			} else {
				$twSocialProfileDetail = $socialProfileDAO->findSingleDetailBy(new SocialProfile(), array('profileType'=>'Twitter', 'accountId'=>$userDetail['accountId']));
				if(!empty($twSocialProfileDetail['picture'])) {
					$userDetail['image'] = $twSocialProfileDetail['picture'];
				}
			}
			
			$this->setUser($userDetail);
			$isValid = $this->isValidUserRequest();
			if(!empty($isValid['nextRoute'])) {
				return $this->sendRequest($isValid['nextRoute']);
			} else {
				return $this->sendRequest('db_postreach_dashboard');
			}
		} else {
			$this->addInResponse('error', $errorMesage);
			return $this->getResponse();
		}
	}
	
	/**
	 * @Route("/logout", name="db_postreach_logout")
	 */
	public function logoutAction() {
		$this->invalidSession();
		$this->removeSession(BaseController::USER_SESSION_KEY);
		$this->get('session')->clear();
		$session = $this->get('session');
		$ses_vars = $session->all();
		foreach ($ses_vars as $key => $value) {
			$session->remove($key);
			$this->removeSession($key);
		}
		
		$n = $this->getRequestParam('n', '');
		$param = array();
		if(!empty($n) && $n == 'ca') {
			$param['n'] = $n;
		}
		
		return $this->sendRequest('db_postreach', $param);
	}
	
	/**
	 * @Route("/forgot-password", name="db_postreach_forgot_password")
	 * @Template("DBAppBundle:postr:forgot-password.html.twig")
	 */
	public function forgotPasswordAction() {
		if($this->getRequest()->isMethod('POST')) {
			$loginForm = $this->getRequestParam('loginForm', array());
			if(!empty($loginForm['email'])) {
				//Get user detial form DB
				$userDAO = new UserDAO($this->getDoctrine());
				$userDetail = $userDAO->findSingleDetailBy(new User(), array('email'=>$loginForm['email']));
				if(!empty($userDetail)) {
					$resetUserDetail = array();
					$resetUserDetail['userId'] = $userDetail['userId'];
					$resetUserDetail['uniqueToken'] = DBUtil::getUniqueKey();
					$resetUserDetail['tokenValidDate'] = new \DateTime();
					$userDetail = $userDAO->editUser($resetUserDetail);
					//Logic to send the forgot password email
					$this->sendRestPasswordEmail($userDetail);
					return $this->sendRequest('db_postreach_forgot_password', array('is'=>1));
				} else {
					$this->addInResponse('error', 'Invalid email or account not found');
				}
			} else {
				$this->addInResponse('error', 'Email should not blank');
			}
		}
		
		$isSend = $this->getRequestParam('is', '');
		$this->addInResponse('isSend', $isSend);
		
		return $this->getResponse();
	}
	
	/**
	 * @Route("/reset-password/{uniqueToken}", name="db_postreach_reset_password")
	 * @Template("DBAppBundle:postr:reset-password.html.twig")
	 */
	public function resetPasswordAction($uniqueToken = '') {
		//Handle invalid token logic
		$error = 'Invalid request/Expire link, Please try again';
		if(empty($uniqueToken)) {
			$this->addInResponse('error', $error);
			return $this->getResponse();
		}
		
		$isSend = $this->getRequestParam('is', '');
		if(!empty($isSend)) {
			$this->addInResponse('isSend', $isSend);
			return $this->getResponse();
		}
		
		//Get unique key and check token is valid
		$userDAO = new UserDAO($this->getDoctrine());
		$userDetail = $userDAO->findSingleDetailBy(new User(), array('uniqueToken'=>$uniqueToken));
		if(!empty($userDetail)) {
			$this->addInResponse('userDetail', $userDetail);
		} else {
			$this->addInResponse('error', $error);
			return $this->getResponse();
		}
		
		if($this->getRequest()->isMethod('POST')) {
			//Handle reset password
			$loginForm = $this->getRequestParam('loginForm', array());
			if(!empty($loginForm['email']) && !empty($loginForm['newPassword']) && !empty($loginForm['confirmPassword']) && $loginForm['newPassword'] == $loginForm['confirmPassword']) {
				$userDetail = $userDAO->findSingleDetailBy(new User(), array('uniqueToken'=>$uniqueToken, 'email'=>$loginForm['email']));
				if(!empty($userDetail)) {
					//Reset password in DB
					$param = array();
					$param['userId'] = $userDetail['userId'];
					$param['password'] = $loginForm['newPassword'];
					$param['uniqueToken'] = DBUtil::getUniqueKey();
					$param['tokenValidDate'] = new \DateTime();
					
					$userDAO->editUser($param);
					
					return $this->sendRequest('db_postreach_reset_password', array('uniqueToken'=>$userDetail['uniqueToken'], 'is'=>'1'));
				}
			} else {
				$this->addInResponse('error', 'Invalid parameter, Please try again');
			}
		}
		
		return $this->getResponse();
	}
	
	/**
	 * @Route("/register", name="db_postreach_register")
	 * @Template("DBAppBundle:postr:register.html.twig")
	 */
	public function ragisterAction() {
		if($this->getRequest()->isMethod('POST')) {
			$accountForm = $this->getRequestParam('accountForm');
			$planId = $this->getRequestParam('pp', '');
			if(!empty($planId)) {
				$accountForm['btPlanId'] = $planId;
			}
			
			if(empty($accountForm['account']) || empty($accountForm['email']) || empty($accountForm['password'])) {
				$this->addInResponse('error', 'Please enter all required detail');
				
				return $this->getResponse();
			} else {
				//Check if user email is already exist, If exist then showmessage
				$userDAO = new UserDAO($this->getDoctrine());
				$userDetail = $userDAO->findSingleDetailBy(new User(), array('email'=>$accountForm['email']));
				
				if(!empty($userDetail)) {
					$this->addInResponse('alreadyExist', 'It looks like you may have already registered');
					return $this->getResponse();
				}
				
				//Create account
				$accountDAO = new AccountDAO($this->getDoctrine());
				$accountDetail = $accountDAO->addAccount($accountForm);
				
				//Create session
				if(!empty($accountDetail)) {
					$userDetail = $accountDetail['userDetail'];
					unset($accountDetail['userDetail']);
					
					$userDetail['account'] = $accountDetail;
					
					//Send confrmation email 
					$subject = 'Your Interior Postr account email verification';
					$this->sendConfirmationEmail($subject, $userDetail);
					
					//Set account status status to send confirmation email
					$accountDAO = new AccountDAO($this->getDoctrine());
					$accountDAO->setAccountStatus($userDetail['accountId'], AccountDAO::ACCOUNT_STATUS_CONFIRM_EMAIL_SEND);
					
					$this->setUser($userDetail);
					
					//Handle account parameters
					$this->handleAccountParam();
					
					//Send notification to internal team
					$this->sendRegistrationNotification($userDetail);
					
					return $this->sendRequest('db_postreach_register_confirm_account');
				} else {
					$this->addInResponse('error', 'Problem while creating the accont');
					return $this->getResponse();
				}
			}
		}
		
		//Check for plan id parameter
		$planId = $this->getRequestParam('pp', '');
		if(!empty($planId)) {
			$this->setSession('pp', $planId);
		}
		
		$isRedirect = $this->manageAccountParam();
		if($isRedirect == true) {
			return $this->sendRequest('db_postreach_register');
		}
		
		$planId = $this->getSession('pp');
		if(isset($planId)) {
			$this->addInResponse('planId', $planId);
		}
	
		return $this->getResponse();
	}

	/**
	 * @Route("/register/confirm-account", name="db_postreach_register_confirm_account")
	 * @Template("DBAppBundle:postr:confirm-account.html.twig")
	 */
	public function confirmAccountAction() {
		$isValid = $this->isValidUserRequest();
		if(!empty($isValid['nextRoute'])) {
			return $this->sendRequest($isValid['nextRoute']);
		}
		
		$currentUser = $this->getUser();
		
		if($this->getRequest()->isMethod('POST')) {
			//Resend the confirmation email
			$k = $this->getRequestParam('k', '');
			$tempUniqueKey = $this->getSession('tempUniqueKey', '');
			
			if(!empty($tempUniqueKey) && !empty($k) && $tempUniqueKey == $k) {
				$uniqueKey = DBUtil::getUniqueKey();
				$userDAO = new UserDAO($this->getDoctrine());
				
				if(!empty($currentUser['userId'])) {
					$userDAO->editUser(array('userId'=>$currentUser['userId'], 'uniqueKey'=>$uniqueKey));
					
					$userDetail = $userDAO->findSingleDetailBy(new User(), array('userId'=>$currentUser['userId']));
					$userDetail['account'] = $currentUser['account'];
					
					$this->setUser($userDetail);

					//Send confrmation email
					$subject = 'Your Interior Postr account email verification';
					$this->sendConfirmationEmail($subject, $userDetail);
					
					return $this->sendRequest('db_postreach_register_confirm_account', array('s'=>'1'));
				}
			}
			
			return $this->sendRequest('db_postreach_register_confirm_account', array('s'=>'2'));
		}
		
		$tempUniqueKey = DBUtil::getUniqueKey();
		$this->setSession('tempUniqueKey', $tempUniqueKey);
		$this->addInResponse('tempUniqueKey', $tempUniqueKey);
		
		$this->addInResponse('userDetail', $currentUser);
		
		return $this->getResponse();
	}

	/**
	 * @Route("/register/confirm/{uniqueKey}", name="db_postreach_register_confirm")
	 * @Template("DBAppBundle:postr:confirm-email.html.twig")
	 */
	public function confirmEmailAction($uniqueKey = '') {
		$errorMesage =  'Invalid confirmation link, Please login and resend the confirmation link';
		if(empty($uniqueKey)) {
			$this->addInResponse('error', $errorMesage);
			return $this->getResponse();
		}
		
		//login user by 
		$userDAO = new UserDAO($this->getDoctrine());
		$userDetail = $userDAO->loginByUniqueKey($uniqueKey);
		
		if(!empty($userDetail['accountId'])) {
			//Remove password
			unset($userDetail['password']);
				
			if($userDetail['userStatus'] == UserDAO::USER_STATUS_INACTIVE) {
				//Set account as confirm the email
				$userDAO->update(new User(), array('userId'=>$userDetail['userId'], 'userStatus'=>UserDAO::USER_STATUS_ACTIVE));
				//Set account status status to send confirmation email
				$accountDAO = new AccountDAO($this->getDoctrine());
				$accountDAO->setAccountStatus($userDetail['accountId'], AccountDAO::ACCOUNT_STATUS_CONFIRM_DONE);
				
				$this->addInResponse('message', 'Your account has been successfully confirmed. Please login and let us get started for you!');
			} else if($userDetail['userStatus'] == UserDAO::USER_STATUS_ACTIVE) {
				$this->addInResponse('message', 'Your account is already confirmed, Please login and happy posting');
			}
		} else {
			$this->addInResponse('error', $errorMesage);
		}
		
		return $this->getResponse();
	}
	
	/**
	 * @Route("/register/select-topics", name="db_postreach_register_select_topic")
	 * @Template("DBAppBundle:postr:select-topic.html.twig")
	 */
	public function selectTopicsAction() {
		$isValid = $this->isValidUserRequest();
		if(!empty($isValid['nextRoute'])) {
			return $this->sendRequest($isValid['nextRoute']);
		}
		
		$currentUser = $this->getUser();
		
		if($this->getRequest()->isMethod('POST')) {
			$categoryForm = $this->getRequestParam('categoryForm', array());
			
			//Assign category to account
			if(!empty($categoryForm['categoryId'])) {
				$accountCategoryDAO = new AccountCategoryDAO($this->getDoctrine());
				$accountCategoryDAO->addCategoryList($currentUser['accountId'], $categoryForm['categoryId']);
			
				//Set account status as set category
				$accountDAO = new AccountDAO($this->getDoctrine());
				$accountDAO->setAccountStatus($currentUser['accountId'], AccountDAO::ACCOUNT_STATUS_TOPIC_SETTING);
				
				//Set sttus in session
				if(isset($currentUser['account']['accountStatus'])) {
					$currentUser['account'] = $accountDAO->findSingleDetailBy(new Account(), array('accountId'=>$currentUser['accountId']));
					$this->setUser($currentUser);
				}
				
				return $this->sendRequest('db_postreach_register_select_platform');
			} else {
				$this->addInResponse('error', 'Category shold not blank, Please select atleast one category');
			}
		}
		
		//Get existing selected categories
		$accountCategoryDAO = new AccountCategoryDAO($this->getDoctrine());
		$existingCategoryList = $accountCategoryDAO->getAccountCategoryList($currentUser['accountId']);
		
		$categoryDAO = new CategoryDAO($this->getDoctrine());
		$categoryList = $categoryDAO->getAllDefaultCategoryList();
		if(!empty($existingCategoryList) && !empty($categoryList)) {
			for($index = 0; $index < count($categoryList); $index ++) {
				for($jIndex = 0; $jIndex < count($existingCategoryList); $jIndex ++) {
					if($categoryList[$index]['categoryId'] == $existingCategoryList[$jIndex]['categoryId']) {
						$categoryList[$index]['selected'] = true;
					}
				}
			}
		} else if(empty($existingCategoryList) && !empty($categoryList)) {
			for($index = 0; $index < count($categoryList); $index ++) {
				$categoryList[$index]['selected'] = true;
			}
		}
		
		$this->addInResponse('categoryList', $categoryList);
		
		return $this->getResponse();
	}
	
	/**
	 * @Route("/register/select-platform", name="db_postreach_register_select_platform")
	 * @Template("DBAppBundle:postr:select-platform.html.twig")
	 */
	public function selectPlatformAction() {
		$isValid = $this->isValidUserRequest();
		if(!empty($isValid['nextRoute'])) {
			return $this->sendRequest($isValid['nextRoute']);
		}
		
		//Handle slection of social profile
		if($this->getRequest()->isMethod('POST')) {
			$socialProfileForm = $this->getRequestParam('socialProfileForm', array());
			
			$isUpdate = false;
			$currentUser = $this->getUser();
			
			//Update facebook social profile
			if(!empty($socialProfileForm['facebook'])) {
				$socialProfileDAO = new SocialProfileDAO($this->getDoctrine());
				$pageList = $this->getSession('pageList'); 
				if(!empty($pageList)) {
					foreach($pageList as $pageDetail) {
						if($pageDetail['socialId'] == $socialProfileForm['facebook']) {
							$pageDetail['accountId'] = $currentUser['accountId'];
							$socialProfileDAO->manageSocialProfile($pageDetail);
							$isUpdate = true;
							break;
						}
					}
				}
			}
			
			//Update twitter profile
			if(!empty($socialProfileForm['twitter'])) {
				$socialProfileDAO = new SocialProfileDAO($this->getDoctrine());
				$twPageList = $this->getSession('twPageList');
				if(!empty($twPageList)) {
					foreach($twPageList as $pageDetail) {
						if($pageDetail['socialId'] == $socialProfileForm['twitter']) {
							$pageDetail['accountId'] = $currentUser['accountId'];
							$socialProfileDAO->manageSocialProfile($pageDetail);
							$isUpdate = true;
							break;
						}
					}
				}
			}
			
			if($isUpdate == true) {
				//Remove facebook and twitter profilelist from session
				$this->removeSession('pageList');
				$this->removeSession('twPageList');
				
				//Set account status as set category
				$accountDAO = new AccountDAO($this->getDoctrine());
				$accountDAO->setAccountStatus($currentUser['accountId'], AccountDAO::ACCOUNT_STATUS_SOCIAL_SETTING);
				
				//Set sttus in session
				if(isset($currentUser['account']['accountStatus'])) {
					$currentUser['account'] = $accountDAO->findSingleDetailBy(new Account(), array('accountId'=>$currentUser['accountId']));
					$this->setUser($currentUser);
				}
				
				//Redirect to next page
				return $this->sendRequest('db_postreach_register_select_frequency');
			}
			
			$this->addInResponse('error', 'Please select at least one social profile.');
		}
		
		//Handle facebook callback action here
		$facebookDetail = $this->getSession('facebookDetail');
		$this->removeSession('facebookDetail');
		if(!empty($facebookDetail)) {
			//Check for error
			if(!empty($facebookDetail['error'])) {
				$this->addInResponse('error', 'Problem while process your facebook request');
			}
			
			//Get pages list
			if(!empty($facebookDetail['pageList'])) {
				$this->setSession('pageList', $facebookDetail['pageList']);
			}
		}
		
		//Hanlde twitter callback action
		$twitterProfileDetail = $this->getSession('twitterProfileDetail');
		$this->removeSession('twitterProfileDetail');
		if(!empty($twitterProfileDetail)) {
			//Check for error
			if(!empty($twitterProfileDetail['error'])) {
				$this->addInResponse('error', 'Problem while process your twitter request');
			}
				
			//Get pages list
			if(!empty($twitterProfileDetail['twPageList'])) {
				$this->setSession('twPageList', $twitterProfileDetail['twPageList']);
			}
		}
		
		$disconnect = $this->getRequestParam('disconnect', '');
		if('fbdisconnect' == $disconnect) {
			//Handle facebook disconnect
			$this->removeSession('pageList');
		} else if('twdisconnect' == $disconnect) {
			//Handle twitter disconnect
			$this->removeSession('twPageList');
		}
		
		$pageList = $this->getSession('pageList');
		if(!empty($pageList)) {
			$this->addInResponse('pageList', $pageList);
		} else {
			if (session_status() == PHP_SESSION_NONE) {
				session_start();
			}
			//Generate facebook and Twitter login URls
			$this->addInResponse('facebookURL', $this->getFacebookLoginURL('db_postreach_handle_profile', 'action=register'));
		}
		
		$twPageList = $this->getSession('twPageList');
		if(!empty($twPageList)) {
			$this->addInResponse('twPageList', $twPageList);
		}
		
		return $this->getResponse();
	}
	
	/**
	 * @Route("/register/select-frequency", name="db_postreach_register_select_frequency")
	 * @Template("DBAppBundle:postr:select-frequency.html.twig")
	 */
	public function selectFrequencyAction() {
		$isValid = $this->isValidUserRequest();
		if(!empty($isValid['nextRoute'])) {
			return $this->sendRequest($isValid['nextRoute']);
		}
		
		$currentUser = $this->getUser();
		$accountFrequencyDAO = new AccountFrequencyDAO($this->getDoctrine());
		
		if($this->getRequest()->isMethod('POST')) {
			$accountFrequencyForm = $this->getRequestParam('accountFrequencyForm', array());
			
			//Make frequency string
			$accountFrequencyForm = $accountFrequencyDAO->getFrequencyDetail($accountFrequencyForm);
			$accountFrequencyForm['accountId'] = $currentUser['accountId'];
			
			$accountFrequencyDAO->manageAccountFrequency($accountFrequencyForm);
			
			//Update timezone in for user
			$accountFrequencyForm['userId'] = $currentUser['userId'];
			
			$userDAO = new UserDAO($this->getDoctrine());
			$userDAO->manageUserTimezone($accountFrequencyForm);
			
			//Set account status as set category
			$accountDAO = new AccountDAO($this->getDoctrine());
			$accountDAO->setAccountStatus($currentUser['accountId'], AccountDAO::ACCOUNT_STATUS_FREQUENCY_SETTING);
			
			//Set sttus in session
			if(isset($currentUser['account']['accountStatus'])) {
				$currentUser['account'] = $accountDAO->findSingleDetailBy(new Account(), array('accountId'=>$currentUser['accountId']));
				$this->setUser($currentUser);
			}
			
			return $this->sendRequest('db_postreach_register_select_payment_method');
		}
		
		$accountFrequencyDetail = $accountFrequencyDAO->getAccountFrequencyDetail($currentUser['accountId']);
		if(!empty($accountFrequencyDetail)) {
			$accountFrequencyDetail = $accountFrequencyDAO->setFrequencyDetail($accountFrequencyDetail);
			
			$this->addInResponse('accountFrequencyDetail', $accountFrequencyDetail);
		}
		
		$timezoneList = Config::getSupportedTimezoneList(); //DBUtil::getTimeZoneList();
		$this->addInResponse('timezoneList', $timezoneList);
		
		return $this->getResponse();
	}
	
	/**
	 * @Route("/register/select-payment-method", name="db_postreach_register_select_payment_method")
	 * @Template("DBAppBundle:postr:select-payment-method.html.twig")
	 */
	public function selectPaymentMethodAction() {
		$isValid = $this->isValidUserRequest();
		if(!empty($isValid['nextRoute'])) {
			return $this->sendRequest($isValid['nextRoute']);
		}

		$route = $this->getAccountStatusRoute();
		if(!empty($route) && 'db_postreach_register_select_payment_method' != $route) {
			return $this->sendRequest($route);
		}
		
		$currentUser = $this->getUser();
		
		if($this->getRequest()->isMethod('POST')) {
			$paymentMethodForm = $this->getRequestParam('paymentMethodForm', array());
			
			if(!empty($paymentMethodForm['paymentMethodNonce'])) {
				$accountDAO = new AccountDAO($this->getDoctrine());
				$accountDetail = $accountDAO->findSingleDetailBy(new Account(), array('accountId'=>$currentUser['accountId']));
				
				if(!empty($accountDetail)) {
					$accountDetail['paymentMethodNonce'] = $paymentMethodForm['paymentMethodNonce'];
					$accountDetail = $accountDAO->updatePaymentMethod($accountDetail);
					
					if(!empty($accountDetail['error'])) {
						$this->addInResponse('error', 'Error while saving card detail, Please contact to POSTR team');
					} else {
						//Redirect to thankyou page
			
						//Set account status as set category
						$accountDAO = new AccountDAO($this->getDoctrine());
						$accountDAO->setAccountStatus($currentUser['accountId'], AccountDAO::ACCOUNT_STATUS_PAYMENT_DONE);
						
						//Set status in session
						if(isset($currentUser['account']['accountStatus'])) {
							$currentUser['account'] = $accountDAO->findSingleDetailBy(new Account(), array('accountId'=>$currentUser['accountId']));
							$this->setUser($currentUser);
						}
						
						//Send notification to internal team
						$userDetail = $this->getUser();
						$this->sendRegistrationNotification($userDetail);
						
						return $this->sendRequest('db_postreach_register_thank_you');
					}
				}
			} else {
				$this->addInResponse('error', 'Error while saving card detail, Please contact to POSTR team');
			}
		}
		
		//Create obejct of DBBraintreeClient
		$dbBraintreeClient = new DBBraintreeClient(Config::getSParameter('BRAINTREE_ENVIRONMENT'),
				Config::getSParameter('BRAINTREE_MERCHANT_ID'), Config::getSParameter('BRAINTREE_PUBLIC_KEY'),
				Config::getSParameter('BRAINTREE_PRIVATE_KEY'));
		
		$clientToken = $dbBraintreeClient->getClientToken();
		
		$this->addInResponse('clientToken', $clientToken);
		
		//Get plan detail
		$planDetail = array();
		if(!empty($currentUser['account']['btPlanId'])) {
			$planDetail = $dbBraintreeClient->getPlan($currentUser['account']['btPlanId']);
			if(empty($planDetail['id'])) {
				$planDetail = $dbBraintreeClient->getPlan(Config::getSParameter('BRAINTREE_PLAN_ID'));
			}
		} else {
			$planDetail = $dbBraintreeClient->getPlan(Config::getSParameter('BRAINTREE_PLAN_ID'));
		}
		
		if(!empty($planDetail['id'])) {
			$this->addInResponse('planDetail', $planDetail);
		}
		
		return $this->getResponse();
	}
	
	/**
	 * @Route("/register/thank-you", name="db_postreach_register_thank_you")
	 * @Template("DBAppBundle:postr:thank-you.html.twig")
	 */
	public function thankyoudAction() {
		$isValid = $this->isValidUserRequest();
		if(!empty($isValid['nextRoute'])) {
			return $this->sendRequest($isValid['nextRoute']);
		}
		
		$currentUser = $this->getUser();
		if(!empty($currentUser['account']['accountStatus']) && $currentUser['account']['accountStatus'] < AccountDAO::ACCOUNT_STATUS_PAYMENT_DONE) {
			$this->sendRequest('db_postreach_logout');
		}
		
		return $this->getResponse();
	}

	/**
	 * This function send confirmation email to user
	 * @param unknown $userDetail
	 */
	public function sendConfirmationEmail($subject, $userDetail) {
		if(empty($userDetail)) {
			return false;
		}
	
		$this->addInResponse('serverUrl', Config::getSParameter('SERVER_APP_PATH', ''));
		$this->addInResponse('userDetail', $userDetail);
		$html = $this->renderView('DBAppBundle:email:confirm.html.twig', $this->getResponse());
	
		$emailDetail = array();
	
		$emailDetail['from'] = Config::getSParameter('FROM_EMAIL');
		$emailDetail['to'] = $userDetail['email'];
		$emailDetail['bcc'] = array(Config::getSParameter('BCC_EMAIL'));
		$emailDetail['subject'] = $subject;
	
		$emailDetail['body'] = $html;
	
		$dbSendgridClient = new DBSendgridClient(Config::getSParameter('SENDGRID_API_KEY_GENERATE_TOKEN'));
		return $dbSendgridClient->sendMail($emailDetail);
	}
	
	/**
	 * This function send notification based on user action
	 * @param array $userDetail
	 */
	public function sendRegistrationNotification($userDetail) {
		if(empty($userDetail)) {
			return array('error'=>'Invalid user detail');
		}
		
		$settingDAO = new SettingDAO($this->getDoctrine());
		$settingMap = $settingDAO->getEnailNotificationSettingMap();
		
		if(empty($settingMap) 
				|| !isset($settingMap[SettingDAO::SETTING_KEY_NOTIFICATION_ENABLE_NOTIFICATION]) 
				|| $settingMap[SettingDAO::SETTING_KEY_NOTIFICATION_ENABLE_NOTIFICATION]['settingValue'] != '1'
		) {
			return array('error'=>'Notification setting disable');;
		}
		
		$action = "";
		if(isset($settingMap[SettingDAO::SETTING_KEY_NOTIFICATION_EMAIL_ENABLE_SIGNUP_NOTIFICATION]) 
				&& $settingMap[SettingDAO::SETTING_KEY_NOTIFICATION_EMAIL_ENABLE_SIGNUP_NOTIFICATION]['settingValue'] == '1'
		) {
			if(isset($userDetail['account']['accountStatus']) && ($userDetail['account']['accountStatus'] == AccountDAO::ACCOUNT_STATUS_SIGNUP || $userDetail['account']['accountStatus'] == AccountDAO::ACCOUNT_STATUS_USER_CREATED)) {
				$action = "New Customer signup";
			}
		}

		if(isset($settingMap[SettingDAO::SETTING_KEY_NOTIFICATION_EMAIL_ENABLE_SIGNUP_COMPLETE_NOTIFICATION])
		&& $settingMap[SettingDAO::SETTING_KEY_NOTIFICATION_EMAIL_ENABLE_SIGNUP_COMPLETE_NOTIFICATION]['settingValue'] == '1'
		) {
			if(!empty($userDetail['account']['accountStatus']) && ($userDetail['account']['accountStatus'] == AccountDAO::ACCOUNT_STATUS_PAYMENT_DONE)) {
				$action = "Customer complete registation with payment method";
			}
		}
		
		if(isset($settingMap[SettingDAO::SETTING_KEY_NOTIFICATION_EMAIL_ENABLE_ACCOUNT_CANCEL])
		&& $settingMap[SettingDAO::SETTING_KEY_NOTIFICATION_EMAIL_ENABLE_ACCOUNT_CANCEL]['settingValue'] == '1'
		) {
			if(!empty($userDetail['account']['accountStatus']) && ($userDetail['account']['accountStatus'] == AccountDAO::ACCOUNT_STATUS_CANCEL)) {
				$action = "Existing customer cancel his account";
			}
		}
		
		if(!empty($userDetail['account']['creationDate'])) {
			$userDetail['createdAt'] = DBUtil::format($userDetail['account']['creationDate']);
		}
		
		$email = '';
		if(!empty($settingMap[SettingDAO::SETTING_KEY_NOTIFICATION_EMAIL]['settingValue'])) {
			$email = $settingMap[SettingDAO::SETTING_KEY_NOTIFICATION_EMAIL]['settingValue'];
		}
		
		if(empty($action)) {
			return array('error'=>"No action available");
		}
		
		if(empty($email)) {
			return array('error'=>"No email available");
		}
	
		$this->addInResponse('action', $action);
		$this->addInResponse('userDetail', $userDetail);
		$html = $this->renderView('DBAppBundle:email:account-notification.html.twig', $this->getResponse());
		
		$emailDetail = array();
	
		if(!is_array($email)) {
			$email = split(',', $email);
			for($index = 0; $index < count($email); $index ++) {
				$email[$index] = trim($email[$index]);
			}
		}
		
		$emailDetail['from'] = Config::getSParameter('FROM_EMAIL');
		$emailDetail['to'] = $email;
		$emailDetail['bcc'] = array(Config::getSParameter('BCC_EMAIL'));
		$emailDetail['subject'] = 'ACTION REQUIRED: ' . $action;
	
		$emailDetail['body'] = $html;
	
		$dbSendgridClient = new DBSendgridClient(Config::getSParameter('SENDGRID_API_KEY_GENERATE_TOKEN'));
		return $dbSendgridClient->sendMail($emailDetail);
	}

	/**
	 * This function send reset password email to user
	 * @param unknown $userDetail
	 */
	public function sendRestPasswordEmail($userDetail) {
		if(empty($userDetail)) {
			return false;
		}
	
		$this->addInResponse('serverUrl', Config::getSParameter('SERVER_APP_PATH', ''));
		$this->addInResponse('userDetail', $userDetail);
		$html = $this->renderView('DBAppBundle:email:forgot-passsword.html.twig', $this->getResponse());
	
		$emailDetail = array();
	
		$emailDetail['from'] = Config::getSParameter('FROM_EMAIL');
		$emailDetail['to'] = $userDetail['email'];
		$emailDetail['bcc'] = array(Config::getSParameter('BCC_EMAIL'));
		$emailDetail['subject'] = 'POSTR: Forgot your password, Instruction to reset your password';
	
		$emailDetail['body'] = $html;
	
		$dbSendgridClient = new DBSendgridClient(Config::getSParameter('SENDGRID_API_KEY_GENERATE_TOKEN'));
		return $dbSendgridClient->sendMail($emailDetail);
	}
}
?>