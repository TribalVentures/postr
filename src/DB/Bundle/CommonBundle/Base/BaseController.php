<?php
namespace DB\Bundle\CommonBundle\Base;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use DB\Bundle\CommonBundle\Util\DBUtil;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Cookie;

class BaseController extends Controller {
	/**
	 * This is array to add response variable to
	 *
	 * @var mixed[]
	 */
	private $_response = array();

	private $USER_SESSION_KEY = "PRIMO_POSTREACH_APP_POSTREACH_XYZ_NGINX_SYMFONY_LOGIN_USER";
	
	const USER_ADMI_SESSION_KEY = "PRIMO_POSTREACH_APP_POSTREACH_XYZ_NGINX_SYMFONY_ADMIN";
	
	const FACEBOOK_USER_SESSION_KEY = "FACEBOOK_USER_SESSION_KEY";
	
	const USER_SESSION_KEY = "USER";

	/**
	 * Constructor make object of controller
	 */
	function __construct() {
		//$session = new Session();
		//$session->start();

		/*if ($this->isSessionExpire()) {
		 $user = $this->getUser();
		 $this->setLanguage($user['language']);
		 }*/
	}

	/**
	 * This function return the response collection
	 * @return mixed[] : This function will return the colelction of response variables
	 */
	public function getResponse() {
		return array('response'=>$this->_response);
	}

	/**
	 * This function add new data/object in response array, So we can use that objects on template.
	 *
	 * @param string $key - Key is always unique, If use dublicate then that will overight the values.
	 * @param mixed $value - Value might be object or any simple variables.
	 */
	public function addInResponse($key, $value) {
		if(isset($key) && isset($value)) {
			$this->_response['' . $key] = $value;
		}
		 
		return $value;
	}

	/**
	 * This function add new data/object in response array, So we can use that objects on template.
	 *
	 * @param string $key - Key is always unique, If use dublicate then that will overight the values.
	 * @param mixed $value - Value might be object or any simple variables.
	 */
	public function clearResponse() {
		$this->_response[] = array();
	}


	/**
	 * This function create object of DAO and call function with parameter.
	 *
	 * @param string $daoClass - This is class name
	 * @param stirng $daoFunction - This is function name
	 * @param mixed[] $params - Array of inout elements. Array element should ebe in same sequence as function parameters
	 * @return mixed - Return the output return by function
	 */
	public function callDAOFuntion($daoClass, $daoFunction, $params = array()) {
		$classPath = "DB\\Bundle\\CommonBundle\\DAO\\" . $daoClass;
		if(!is_file($classPath . ".php")) {
			$classPath = "DB\\Bundle\\CommonBundle\\DAO\\" . $daoClass;
		}
		 
		$daoClass = $classPath;
		 
		//Create ibject of dao
		$dao = new $daoClass($this->getDoctrine());
		 
		$value = call_user_func_array(array($dao, $daoFunction), $params);
		 
		return $value;
	}

	/**
	 * This functio route flow to twig template
	 *
	 * @param string $template 	- Full qualified name of twig template
	 * @example Following example shows the sample call from controler class
	 * $this->displayTemplate('AcmeCrmBundle:Page:index.html.twig');
	 * @return mixed 	- Return response after rendering template
	 */
	public function displayTemplate($template) {
		//$this->addInResponse("HTML", new HtmlHelper());
		return $this->render($template, array("response"=>$this->_response));
		 
	}

	/**
	 * This function create a return JSON response
	 * @param mixed[] $data
	 * @return \Symfony\Component\HttpFoundation\JsonResponse
	 */
	public function getJsonResponse($data, $crossdomain = false) {
		$response = new JsonResponse($data);
		if($crossdomain) {
			$response->headers->set('Access-Control-Allow-Origin', '*');
		}
		if(isset($data['code']) && $data['code'] == 404){
			$response->setStatusCode(404);
		}
		$response->setExpires(new \DateTime('2000-01-01'));
		
		return $response;
	}

	/**
	 * This function create a return Text response
	 * @param mixed[] $data
	 * @return \Symfony\Component\HttpFoundation\JsonResponse
	 */
	public function getTextResponse($text, $crossdomain = false) {
		$response = new Response($text);
		if($crossdomain) {
			$response->headers->set('Access-Control-Allow-Origin', '*');
		}
		
		$response->setExpires(new \DateTime('2000-01-01'));
		
		return $response;
	}

	/**
	 * This function redirect request to already defined route entry
	 *
	 * e.g acme_crm_viewCompany:
	 * @param $routePath - Route name
	 * @return Retrun outut of redirec
	 */
	public function sendRequest($routePath, $parameter = array(), $queryString = "") {
		$returnUrl = $this->generateUrl($routePath, $parameter);
		return $this->redirect($returnUrl . $queryString);
	}

	/**
	 * This function return forms parmeters from request
	 *
	 * @param $requestParam - Form parameter name
	 * @return Return parameter value if exist in request otherwise return null
	 */
	public function getRequestParam($requestParam, $default = '') {
		return $this->getRequest()->get($requestParam, $default);
	}

	/**
	 * This function will return get all request parameter
	 */
	public function getAllRequestParam() {
		return $this->getRequest()->request->all();
	}

	/**
	 * This function return value defined in cofig.yml file using param name
	 * @param String $paramName
	 * @return String It return the value associated with param name
	 */
	public function getParameter($paramName) {
		return $this->container->getParameter($paramName);
	}

	/**
	 * This method return object from session
	 *
	 * @param string $key 	- This is key index that will search in session
	 * @return mixed 		- Return object from session, otherwise return null
	 **/
	public function getSession($key) {
		$session = $this->getRequest()->getSession();
		$value = $session->get($key);
		if(isset($value)) {
			return $value;
		}
		return null;
	}

	/**
	 * This method set value into session.
	 *
	 * @param string $key 	- This is key that will used for session index
	 * @param mixed $value	- This is object to set in session against key
	 **/
	public function setSession($key, $value) {
		$session = $this->getRequest()->getSession();
		$session->set($key, $value);
	}

	/**
	 * This method remove value from session.
	 *
	 * @param string $key 	- This is key that will remove from session
	 * @return void
	 **/
	public function removeSession($key) {
		$session = $this->getRequest()->getSession();
		$session->remove($key);
	}

	/**
	 * This function unset the sesstion
	 *
	 * @return void
	 */
	public function invalidSession($userPostFix = '') {
		$session = $this->get("request")->getSession($userPostFix);
		$session->clear();
		$session->invalidate();
		$this->get('session')->clear();
		
		$session = $this->get('session');
		$ses_vars = $session->all();
		foreach ($ses_vars as $key => $value) {
			$session->remove($key);
		}
	}

	/**
	 * this function check sesstion exist or not
	 *
	 * @return void
	 */
	public function isSessionExpire($userPostFix = '') {
		$user = $this->getUser($userPostFix);
		if($user != null) {
			return true;
		} else {
			return false;
		}
	}
	
	/**
	 * This function return the expire session json response
	 * @param string $route - This is route name defined controller
	 * @param string $status
	 * @param string $message
	 */
	public function getJSONSessionExpire($route, $status = '404', $message = 'session is expire, please login again') {
		$error = array();
		$error['status'] = $status;
		$error['message'] = $message;
		$error['action'] = $this->generateUrl($route);
		
		$this->addInResponse('error', $error);
		
		return $this->getJsonResponse($this->getResponse());
	}

	/**
	 * this function check sesstion exist or not
	 *
	 * @return void
	 */
	public function isAdminSessionExpire($userPostFix = BaseController::USER_ADMI_SESSION_KEY) {
		return $this->isSessionExpire($userPostFix);
	}
	
	/**
	 * This function check session expire based on access token
	 */
	public function isFacebookSessionExpire($key = 'accessToken', $userPostFix = '') {
		if($this->isSessionExpire($userPostFix)) {
			$currentuser = $this->getUser($userPostFix);
			if(!empty($currentuser[$key])) {
				return true;
			} else {
				return false;
			}
		} else {
			return false;
		}
	}

	/**
	 * This function redirect to page
	 *
	 * @return - If session expired then it redirect to default page
	 */
	public function isValidateRequest($userPostFix = '') {
		if(!$this->isSessionExpire($userPostFix)) {
			return $this->sendRequest('DB_index');
		} else {
			$user = $this->getUser($userPostFix);
			if(isset($user['userLanguage'])) {
				$this->setLanguage($user['userLanguage']);
			}
		}
		return false;
	}
	
	
	/**
	 * This function return session message  
	 *
	 * @return -  Return session message 
	 */
	public function sessionMessage() {
		$message = "Your session is expire";
		
		return $message;
	}
	

	/**
	 * This function set user detail array into sesion
	 *
	 * @param Array $user - User details
	 * @return void
	 */
	public function setUser($user, $userPostFix = '') {
		$this->setSession($this->USER_SESSION_KEY . $userPostFix, $user);
	}

	/**
	 * This method return user details from session
	 *
	 * @return - Return user details array
	 */
	public function getUser($userPostFix = '') {
		return $this->getSession($this->USER_SESSION_KEY . $userPostFix);
	}
	

	/**
	 * This function set user detail array into sesion
	 *
	 * @param Array $user - User details
	 * @return void
	 */
	public function setAdminUser($user, $userPostFix = BaseController::USER_ADMI_SESSION_KEY) {
		$this->setUser($user, $userPostFix);
	}

	/**
	 * This method return user details from session
	 *
	 * @return - Return user details array
	 */
	public function getAdminUser($userPostFix = BaseController::USER_ADMI_SESSION_KEY) {
		return $this->getUser($userPostFix);
	}

	/**
	 * This function send mail using swift mailer
	 */
	public function btMail($subject, $body, $from = array(), $to = array(), $cc = array(), $bcc = array()) {
		// Create a message
		$message = \Swift_Message::newInstance($subject)
		->setFrom(array($from[0] => 'Article Treding'))
		->setTo($to)
		->setBody($body, 'text/html');

		$response =  $this->get('mailer')->send($message);
		 
		return $response;
	}

	/**
	 * This fucntion is used to get unique key for uploading image
	 *
	 * @return $uniqueKey
	 */
	public function getUniqueKey($len = 3) {
		$base = 'ABCDEFGHKLMNOPQRSTWXYZabcdefghjkmnpqrstwxyz123456789';
		$max = strlen($base)-1;
		$uniqueKey = '';
		while (strlen($uniqueKey)<$len+1) {
			$uniqueKey.=$base{mt_rand(0,$max)};
		}

		$uniqueKey = date("YmdHisu") . $uniqueKey;

		return $uniqueKey;
	}

	/**
	 * This function set language
	 * @param string $language - Language name e.g en, fr, nl
	 */
	public function setLanguage($language = "en") {
		if(empty($language)) {
			$language = "en";
		}
		//echo $language;
		$request = $this->getRequest()->setLocale($language);
	}

	/**
	 * This function set current user language
	 */
	public function setUserLanguage() {
		$user = $this->getUser();
			
		if(isset($user['language'])) {
			$this->setLanguage($user['language']);
		}
	}
	
	public function processContent() {
		$request = $this->getRequest();
	
		if (0 === strpos($request->headers->get('Content-Type'), 'application/json')) {
			$data = json_decode($request->getContent(), true);
			return $data;
			$request->request->replace(is_array($data) ? $data : array());
		} 
		
	}
	
	/**
	 * This function uplod file
	 * @param string $fileName
	 * @param strgin $tempName
	 * @param string $source
	 */
	public function uploadFile($fileName, $tempName, $source) {
		if(!file_exists($source)){
    		if (!mkdir($source, 0777, true)) {
    			return false;
    		}
		}
		
		//change file name to parameter name with extenstion
		if(empty($fileName)) {
			return false;
		}

		$name = DBUtil::getUniqueKey();
	
		$existingFilAttributeList = explode('.', $fileName);
		$name = $name . '.' . $existingFilAttributeList[count($existingFilAttributeList) - 1];
	
		if(!move_uploaded_file($tempName, $source . DIRECTORY_SEPARATOR . $name)) {
			return false;
		}
		
		return $source . DIRECTORY_SEPARATOR . $name;
	}
	
	/**
	 * This function send data to url using post method
	 * @param string $url
	 * @param array $post
	 */
	public function postAPIV1($url, $post = array()) {
		//================= start curl ===================
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
		curl_setopt($ch, CURLOPT_URL,$url);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_TIMEOUT, 30);
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS,$post);
	
		curl_setopt($ch,CURLOPT_USERAGENT,'Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.1.13) Gecko/20080311 Firefox/2.0.0.13');
	
		$result = curl_exec($ch);
		curl_close($ch);
		//================= end curl ===================
		//echo "response: ";
		return $result;
	}
	
	/**
	 * This function log message to log file
	 * @param unknown $message
	 * @param unknown $type
	 */
	public function log($message, $type = 'INFO') {
		$logger = $this->get('logger');
		
		if($type == 'DEGUB') {
		} else if($type == 'INFO') {
			$logger->info('[APP INFO] ' . $message);
		} else if($type == 'ERROR') {
			$logger->error('[APP ERROR] ' . $message);
		}
	}
	
	/**
	 * This function set and get cookie
	 * @param string $key
	 * @param string $defaultValue
	 * @return string Return the cookie value
	 */
	public function getCookie($key = '_db', $defaultValue = '', $cookieTime = '', $isset = true) {
		if(empty($key)) {
			return $this->getRequest()->cookies->all();
		} 
		
		$value = $this->getRequest()->cookies->get($key, '');
		if(empty($value) && $isset == true) {
			$value = $defaultValue;
			$response = new Response();
			
			if(empty($cookieTime)) {
				$cookieTime = time() + 3600 * 24 * 7;
			}
			$cookie = new Cookie($key, $value, $cookieTime, '/', null, false, false);
			$response->headers->setCookie($cookie);
			$response->sendHeaders();
		}
		
		return $value;
	}
	
	/**
	 * This function set cookie
	 * @param string $key
	 * @param string $value
	 * @return string Return the cookie value
	 */
	public function setCookie($key = '_db', $value = '', $cookieTime = '') {
		$response = new Response();
		
		if(empty($cookieTime)) {
			$cookieTime = time() + 3600 * 24 * 7;
		}
		
		$cookie = new Cookie($key, $value, $cookieTime, '/', null, false, false);
		$response->headers->setCookie($cookie);
		$response->sendHeaders();
	
		return $value;
	}
	
	/**
	 * This function delete the cookie by cookie name
	 * @param string $key
	 */
	public function removeCookie($key = '_db') {
		$value = $this->getRequest()->cookies->get($key, '');

		if(!empty($value)) {
			$response = new Response();
			$response->headers->clearCookie($key);
			$response->send();
		}
	}
}
?>