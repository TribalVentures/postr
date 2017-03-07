<?php

namespace DB\Bundle\AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use DB\Bundle\CommonBundle\Base\BaseController;
use DB\Bundle\AppBundle\DAO\CategoryDAO;
use DB\Bundle\AppBundle\Entity\Category;
use Facebook\Facebook;
use DB\Bundle\AppBundle\Common\Config;
use Abraham\TwitterOAuth\TwitterOAuth;
use DB\Bundle\AppBundle\Entity\SocialProfile;
use DB\Bundle\AppBundle\DAO\UserDAO;
use DB\Bundle\AppBundle\Entity\User;
use DB\Bundle\AppBundle\DAO\AccountDAO;
use DB\Bundle\AppBundle\Entity\Account;
use DB\Bundle\CommonBundle\ApiClient\DBSendgridClient;
use DB\Bundle\AppBundle\DAO\AccountParamDAO;

class DbAppController extends BaseController {
	/**
	 * This functi8on return account slug detail of current account
	 */
	public function getAccountSlugDetail() {
		return $this->getSession('accountSlugAccountDetail');
	}
	
	/**
	 * This function return curent accoutn slug
	 */
	public function getAccountSlug() {
		$accountSlugAccountDetail = $this->getSession('accountSlugAccountDetail');
		return $accountSlugAccountDetail['accountSlug'];
	}
	
	/**
	 * This function rediorect to another request with account slug
	 * @param string $routePath
	 * @param string $queryString
	 */
	public function sendRequestToSlug($routePath, $queryString = '') {
		$param = array('accountSlug'=>$this->getAccountSlug());
		return $this->sendRequest($routePath,$param, $queryString);
	}
	
	/**
	 * This function initalise customer session
	 * @param mixed $customerDetail
	 */
	public function setCustomer($customerDetail) {
		$accoutSlugDetail = $this->getAccountSlugDetail();
		$this->setUser($customerDetail, 'CUSTOMER_' . $accoutSlugDetail['accountId'] . '_');
	}
	
	/**
	 * This fucntion return current registered customer
	 */
	public function getCustomer() {
		$accoutSlugDetail = $this->getAccountSlugDetail();
		return $this->getUser('CUSTOMER_' . $accoutSlugDetail['accountId'] . '_');
	}
	
	/**
	 * This function will be check frontend user session and return true if exist 
	 * otherwise return false
	 */
	public function isFrontEndSessionExpire() {
		$accoutSlugDetail = $this->getAccountSlugDetail();
		return $this->isSessionExpire('CUSTOMER_' . $accoutSlugDetail['accountId'] . '_');
	}
	
	/**
	 * This function remove customer session
	 */
	public function removeCustomer() {
		$accoutSlugDetail = $this->getAccountSlugDetail();
		return $this->removeSession('CUSTOMER_' . $accoutSlugDetail['accountId'] . '_');
	}
	
	/**
	 * This function will be find the categories and add that into response
	 */
	public function addCategories() {
		$accountSlugAccountDetail = $this->getAccountSlugDetail();
		
		$CategoryDAO = new CategoryDAO($this->getDoctrine());
		$categoryDetailList = $CategoryDAO->findDetailBy(new Category(), array('categoryStatus'=>'0', 'parentCategoryId'=>'0', 'accountId'=>$accountSlugAccountDetail['accountId']));
	
		$this->addInResponse('categoryDetailList', $categoryDetailList);
	}
	
	/**
	 * This function make a list of ticket status and add into response
	 */
	public function addTicketStatus() {
		$statusList = array();
		$statusList[] = array('id'=>'1', 'text'=>'Open');
		$statusList[] = array('id'=>'3', 'text'=>'InProgress');
		$statusList[] = array('id'=>'4', 'text'=>'Resolve');
		$statusList[] = array('id'=>'5', 'text'=>'Reject');
		$statusList[] = array('id'=>'2', 'text'=>'Close');
		
		$this->addInResponse('statusList', $statusList);
	}
	
	/**
	 * This function make a list of ticket status and add into response
	 */
	public function addTicketPriority() {
		$priorityList = array();
		$priorityList[] = array('id'=>'3', 'text'=>'very Heigh');
		$priorityList[] = array('id'=>'3', 'text'=>'Heigh');
		$priorityList[] = array('id'=>'2', 'text'=>'Mediun');
		$priorityList[] = array('id'=>'1', 'text'=>'Low');
		
		$this->addInResponse('priorityList', $priorityList);
	}
	
	/**
     * This function is used to get asset path
     * 
     * @return String Return the web directory path without host
     */
    public function getAssetPath($isDev = false) {
		$path = $this->getRequest()->getBasePath();
    	
		if($isDev) {
	    	$path = str_replace("app.php/", '', $path);
	    	$path = str_replace("app_dev.php/", '', $path);
	    	$path = str_replace("//", '/', $path);
		}
    	
    	return $path; 
    }

    /**
     * This function will return the facebook login URL
     */
    public function getFacebookLoginURL($route, $parameter = '') {
    	if (session_status() == PHP_SESSION_NONE) {
    		session_start();
    	}
    	
    	$facebook = new Facebook([
    			'app_id' => Config::getSParameter('POSTREACH_FACEBOOK_APP_ID'),
    			'app_secret' => Config::getSParameter('POSTREACH_FACEBOOK_APP_SECRET'),
    			'default_graph_version' => Config::getSParameter('POSTREACH_FACEBOOK_DEFAULT_VERSION'),
    	]);
    
    	$helper = $facebook->getRedirectLoginHelper();
    
    	$returnURL = $this->generateUrl($route, array(), true);
    	
    	if(!empty($parameter)) {
    		$returnURL = $returnURL . '?' . $parameter;
    	}
    	$permissions = ['email', 'manage_pages', 'publish_actions', 'read_insights', 'publish_pages']; // optional
    	return $helper->getLoginUrl($returnURL, $permissions);
    }
	
	/**
	 * This function reutrn the all facebook pages of account
	 */
	private function getFacebookPages() {
		$facebookDetail = array();
		
		$facebook = new Facebook([
				'app_id' => Config::getSParameter('POSTREACH_FACEBOOK_APP_ID'),
				'app_secret' => Config::getSParameter('POSTREACH_FACEBOOK_APP_SECRET'),
				'default_graph_version' => Config::getSParameter('POSTREACH_FACEBOOK_DEFAULT_VERSION'),
		]);
		
		$helper = $facebook->getRedirectLoginHelper();
		
		$_SESSION['FBRLH_state']=$_GET['state'];
		
		try {
			$accessToken = $helper->getAccessToken();
			if(!isset($accessToken) || $helper->getError()) {
				$facebookDetail['error'] = 'User click on cancel request';
				return $facebookDetail;
			}
		} catch(Facebook\Exceptions\FacebookResponseException $e) {
			// When Graph returns an error
			$facebookDetail['error'] = 'Graph returned an error: ' . $e->getMessage();
			return $facebookDetail;
		} catch(Facebook\Exceptions\FacebookSDKException $e) {
			// When validation fails or other local issues
			$facebookDetail['error'] = 'Facebook SDK returned an error: ' . $e->getMessage();
			return $facebookDetail;
		} catch(\Exception $e) {
			$facebookDetail['error'] = 'Facebook SDK returned an error: ' . $e->getMessage();
			return $facebookDetail;
		}
		
		// OAuth 2.0 client handler
		$oAuth2Client = $facebook->getOAuth2Client();
		
		// Exchanges a short-lived access token for a long-lived one
		$longLivedAccessToken = $oAuth2Client->getLongLivedAccessToken((string) $accessToken);
		
		$facebook->setDefaultAccessToken((string) $longLivedAccessToken);
		
		// getting basic info about user
		$facebookPageList = null;
		try {
			$pageRequest = $facebook->get('/me/accounts?fields=name,id,picture,category,access_token');
			$facebookPageList = $pageRequest->getGraphEdge();
		} catch(Facebook\Exceptions\FacebookResponseException $e) {
			// When Graph returns an error
			$facebookDetail['error'] = 'Graph returned an error: ' . $e->getMessage();
			return $facebookDetail;
		} catch(Facebook\Exceptions\FacebookSDKException $e) {
			// When validation fails or other local issues
			$facebookDetail['error'] = 'Facebook SDK returned an error: ' . $e->getMessage();
			return $facebookDetail;
		}
		
		try {
			$profileRequest = $facebook->get('/me?fields=name,id,picture');
			$loginUserProfile = $profileRequest->getGraphNode();
		} catch(Facebook\Exceptions\FacebookResponseException $e) {
			// When Graph returns an error
			$facebookDetail['error'] = 'Graph returned an error: ' . $e->getMessage();
			return $facebookDetail;
		} catch(Facebook\Exceptions\FacebookSDKException $e) {
			// When validation fails or other local issues
			$facebookDetail['error'] = 'Facebook SDK returned an error: ' . $e->getMessage();
			return $facebookDetail;
		}
		
		$facebookDetail['pageList'] = array();
		
		//Add personal profile into pages
		if($facebookPageList != null && isset($facebookPageList)) {
			$profile = array();
			$profile['socialId'] 	= $loginUserProfile['id'];
			$profile['profileType'] = SocialProfile::PROFILE_TYPE_FACEBOOK;
			$profile['accessToken'] = (string) $accessToken;
			$profile['picture']		= $loginUserProfile['picture']['url'];
			$profile['name'] 		= $loginUserProfile['name'];
			$profile['category'] 	= Config::POSTREACH_FACEBOOK_PROFILE_CATEGORY;
			
			$facebookDetail['pageList'][] = $profile;
		}
		
		if($facebookPageList != null && isset($facebookPageList)) {
			//Add pages
			foreach($facebookPageList as $key):
				$pagerecord = array();
				$pagerecord['socialId'] = $key['id'];
				$pagerecord['profileType'] = SocialProfile::PROFILE_TYPE_FACEBOOK;
				$pagerecord['accessToken'] = $key['access_token'];
				$pagerecord['picture'] = $key['picture']['url'];
				$pagerecord['name'] = $key['name'];
				$pagerecord['category'] = $key['category'];
				
				$facebookDetail['pageList'][] = $pagerecord;
			endforeach;
		}
		
		
		try {
			$profileRequest = $facebook->get('/me?fields=name,id,picture,first_name,last_name');
			$loginUserProfile = $profileRequest->getGraphNode();
			//add user profile
			$facebookDetail['profile'] = array();
			$facebookDetail['profile']['url'] = $loginUserProfile['picture']['url'];
			$facebookDetail['profile']['name'] = $loginUserProfile['name'];
			$facebookDetail['profile']['id'] = $loginUserProfile['id'];
			$facebookDetail['profile']['firstName'] = $loginUserProfile['first_name'];
			$facebookDetail['profile']['lastName'] = $loginUserProfile['last_name'];
			
		} catch(Facebook\Exceptions\FacebookResponseException $e) {
			// When Graph returns an error
			$facebookDetail['error'] = 'Graph returned an error: ' . $e->getMessage();
			return $facebookDetail;
		} catch(Facebook\Exceptions\FacebookSDKException $e) {
			// When validation fails or other local issues
			$facebookDetail['error'] = 'Facebook SDK returned an error: ' . $e->getMessage();
			return $facebookDetail;
		}
		
		return $facebookDetail;
	}

	/**
	 * @Route("/facebook/handle-profile", name="db_postreach_handle_profile")
	 */
	public function handleFacebookProfileAction() {
		if (session_status() == PHP_SESSION_NONE) {
			session_start();
		}
		
		$facebookDetail = $this->getFacebookPages();
		$this->setSession('facebookDetail', $facebookDetail);
	
		$action = $this->getRequestParam('action', '');
		if('register' == $action) {
			return $this->sendRequest('db_postreach_register_select_platform');
		} else if('preferences' == $action) {
			return $this->sendRequest('db_postreach_post_preference_social_networks');
		} else {
			return $this->sendRequest('db_postreach');
		}
	}

	/**
	 * @Route("/twitter/connect", name="db_postreach_twitter_connect")
	 */
	public function twitterConnect() {
		$action = $this->getRequestParam('q', '');
	
		$returnURL = $this->generateUrl('db_postreach_twitter_handler', array('action'=>$action), true);
	
		$tw = new TwitterOAuth(Config::getSParameter('POSTREACH_TWITTER_API_KEY'), Config::getSParameter('POSTREACH_TWITTER_API_SECRET'));
		$content = $tw->get("account/verify_credentials");
	
		$requestToken = $tw->oauth("oauth/request_token", array("oauth_callback" =>$returnURL));
	
		$this->setSession('oauthToken', $requestToken['oauth_token']);
		$this->setSession('oauthTokenSecret', $requestToken['oauth_token_secret']);
	
		/* Build authorize URL and redirect user to Twitter. */
		$url = $tw->url("oauth/authorize", array("oauth_token" => $requestToken['oauth_token']));
	
		header('Location: ' . $url);
		exit;
	}

	/**
	 * @Route("/twitter/handler", name="db_postreach_twitter_handler")
	 */
	public function postreachTwitterHandlerAction() {
		$route = '';
		$requestParam = array();
		
		$action = $this->getRequestParam('action', '');
		if('register' == $action) {
			$route = 'db_postreach_register_select_platform';
		} else if('preferences' == $action) {
			$route = 'db_postreach_post_preference_social_networks';
		}
		
		//Handle condition for denied
		$denied = $this->getRequestParam('denied', '');
		
		if(!empty($denied)) {
			$this->setSession('twitterProfileDetail', array('error'=>$denied));
			$requestParam['denied'] = $denied;
			return $this->sendRequest($route, $requestParam, '');
		}
	
		$oauthToken = $this->getSession('oauthToken');
		$oauthTokenSecret = $this->getSession('oauthTokenSecret');
		$oauthVerifier = $this->getRequestParam('oauth_verifier');
	
		if(!empty($route) && (!isset($oauthToken) || !isset($oauthTokenSecret)|| !isset($oauthVerifier))) {
			$this->setSession('twitterProfileDetail', array('error'=>'Auth detail is not in session'));
			return $this->sendRequest($route);
		} else {
			if(!isset($oauthToken) || !isset($oauthTokenSecret)|| !isset($oauthVerifier)) {
				return $this->sendRequest('db_postreach');
			}
		}
	
		$tw = new TwitterOAuth(Config::getSParameter('POSTREACH_TWITTER_API_KEY'), Config::getSParameter('POSTREACH_TWITTER_API_SECRET'), $oauthToken, $oauthTokenSecret);
		$accessToken = $tw->oauth("oauth/access_token", array("oauth_verifier" => $oauthVerifier));
	
		if(!empty($accessToken)) {
			$twitterProfileDetail = array();
			$twitterProfileDetail['profileType'] = SocialProfile::PROFILE_TYPE_TWITTER;
	
			$twitterProfileDetail['socialId'] = $accessToken['user_id'];
			$twitterProfileDetail['oauthToken'] = $accessToken['oauth_token'];
			$twitterProfileDetail['oauthTokenSecret'] = $accessToken['oauth_token_secret'];
				
			$twitterProfileDetail['picture'] = 'https://twitter.com/' . $accessToken['screen_name'] . '/profile_image?size=normal';
			$twitterProfileDetail['name'] = $accessToken['screen_name'];
			$twitterProfileDetail['category'] = '';
			
			$this->setSession('twitterProfileDetail', array('twPageList'=>array($twitterProfileDetail)));
		}
		
		return $this->sendRequest($route);
	}
	
	/**
	 * This function check account current 
	 */
	public function getAccountStatusRoute() {
		$currentUser = $this->getUser();
		if(empty($currentUser)) {
			return 'db_postreach';
		}
		
		//Reinitiaise the session
		$userDAO = new UserDAO($this->getDoctrine());
		$userDetail = $userDAO->findSingleDetailBy(new User(), array('userId'=>$currentUser['userId']));
		
		if(!empty($userDetail['accountId'])) {
			//Remove password
			unset($userDetail['password']);
		
			//Get account detail
			$accountDAO = new AccountDAO($this->getDoctrine());
			$userDetail['account'] = $accountDAO->findSingleDetailBy(new Account(), array('accountId'=>$userDetail['accountId']));
		
			$this->setUser($userDetail);
		
			//Check for user finish all registration process, if not then redirect to steps to finish
			if(isset($userDetail['account']['accountStatus'])) {
				$accountStatus = $userDetail['account']['accountStatus'];
				if($accountStatus == 0 || $accountStatus == 1) { //Send to topic selection
					return 'db_postreach_register_select_topic';
				} else if($accountStatus == 3) {
					return 'db_postreach_register_select_platform';
				} else  if($accountStatus == 4) {
					return 'db_postreach_register_select_frequency';
				} else  if($accountStatus == 5) {
					return 'db_postreach_register_select_payment_method';
				} 
			}
			
			//Redirect to new version
			return '';
		}
	}
	
	/**
	 * This function cehck is request is valid
	 */
	public function isValidUserRequest() {
		$response = array();
		$response['currentRoute'] = $this->getRequest()->get('_route');
		$response['nextRoute'] = '';
		if(!$this->isSessionExpire()) {
			$response['nextRoute'] = 'db_postreach';
		}
		
		$currentUser = $this->getUser();
		
		//Check for user finish all registration process, if not then redirect to steps to finish
		if(isset($currentUser['account']['accountStatus'])) {
			$accountStatus = $currentUser['account']['accountStatus'];
			if($accountStatus == AccountDAO::ACCOUNT_STATUS_SIGNUP || $accountStatus == AccountDAO::ACCOUNT_STATUS_USER_CREATED || $accountStatus == AccountDAO::ACCOUNT_STATUS_CONFIRM_EMAIL_SEND) { //Send to email confirmation message page
				$response['nextRoute'] = 'db_postreach_register_confirm_account';
			}else  if($accountStatus == AccountDAO::ACCOUNT_STATUS_CONFIRM_DONE) {
				$response['nextRoute'] = 'db_postreach_register_select_topic';
			} else  if($accountStatus == AccountDAO::ACCOUNT_STATUS_TOPIC_SETTING) {
				$response['nextRoute'] = 'db_postreach_register_select_platform';
			} else  if($accountStatus == AccountDAO::ACCOUNT_STATUS_SOCIAL_SETTING) {
				$response['nextRoute'] = 'db_postreach_register_select_frequency';
			} else  if($accountStatus == AccountDAO::ACCOUNT_STATUS_FREQUENCY_SETTING) {
				$response['nextRoute'] = 'db_postreach_register_select_payment_method';
			} else  if($accountStatus == AccountDAO::ACCOUNT_STATUS_PAYMENT_DONE) {
				$response['nextRoute'] = '';
			}  else  if($accountStatus == AccountDAO::ACCOUNT_STATUS_CANCEL) {
				$response['nextRoute'] = 'db_postreach';
			} else {
				$response['nextRoute'] = '';
			}
		}
		
		if($response['currentRoute'] == $response['nextRoute']) {
			$response['nextRoute'] = '';
		}
		
		return $response;
	}
	
	/**
	 * This function will be check account parameters
	 */
	public function manageAccountParam() {
		$isRedirect = false;
		//Check if account parameters already exist on machin
		$sid = $this->getRequestParam('sid', '');
		if(empty($sid)) {
			$sid = $this->getCookie(Config::COOKIE_KEY_SID);
			if(empty($sid)) {
				$sid = $this->getSession("" . Config::COOKIE_KEY_SID);
			}
		} else {
			$this->setCookie(Config::COOKIE_KEY_SID, $sid);
			$this->setSession(Config::COOKIE_KEY_SID, $sid);
			
			$isRedirect= true;
		}
		
		if(!empty($sid)) {
			//Add sid in respnse
			$this->addInResponse('sid', $sid);
		}
		
		$discountCoupon = $this->getRequestParam('dc', '');
		if(empty($discountCoupon)) {
			$discountCoupon = $this->getCookie(Config::COOKIE_KEY_DISCOUNT_COUPON);
			if(empty($discountCoupon)) {
				$discountCoupon = $this->getSession(Config::COOKIE_KEY_DISCOUNT_COUPON);
			}
		} else {
			$this->setCookie(Config::COOKIE_KEY_DISCOUNT_COUPON, $discountCoupon);
			$this->setSession(Config::COOKIE_KEY_DISCOUNT_COUPON, $discountCoupon);
			
			$isRedirect= true;
		}
		
		if(!empty($discountCoupon)) {
			//Add sid in respnse
			$this->addInResponse('discountCoupon', $discountCoupon);
		}
		
		return $isRedirect;
	}
	
	/**
	 * This function handle account parameters in DB
	 */
	public function handleAccountParam() {
		/*$isValid = $this->isValidUserRequest();
		if(!empty($isValid['nextRoute'])) {
			return false;
		}*/
		
		$accountParam = array();
		
		$sid = $this->getCookie(Config::COOKIE_KEY_SID);
		if(!empty($sid)) {
			$accountParam['sid'] = $sid;
		} else {
			$sid = $this->getSession(Config::COOKIE_KEY_SID);
			if(!empty($sid)) {
				$accountParam['sid'] = $sid;
			}
		}
		
		$this->removeCookie(Config::COOKIE_KEY_SID);
		$this->removeSession(Config::COOKIE_KEY_SID);
		
		$discountCode = $this->getCookie(Config::COOKIE_KEY_DISCOUNT_COUPON);
		if(!empty($sid)) {
			$accountParam['discountCode'] = $discountCode;
		} else {
			$discountCode = $this->getSession(Config::COOKIE_KEY_DISCOUNT_COUPON);
			if(!empty($sid)) {
				$accountParam['discountCode'] = $discountCode;
			}
		}
		
		$this->removeCookie(Config::COOKIE_KEY_DISCOUNT_COUPON);
		$this->removeSession(Config::COOKIE_KEY_DISCOUNT_COUPON);
		
		if(!empty($accountParam)) {
			$currentUser = $this->getUser();
			$accountParam['accountId'] = $currentUser['accountId'];
			
			$accountParamDAO = new AccountParamDAO($this->getDoctrine());
			$accountParamDAO->manageAccountParameter($accountParam);
		}
		
		return true;
	}
}
?>
