<?php
namespace DB\Bundle\CommonBundle\ApiClient;

use Facebook\Facebook;
/**
* This class use facebook apis tocommunicate with  there system
*/
class DBFacebookClient {
	/**
	 * This function create the facebook login URl and return
	 * @param string $permission
	 * @param string $returnURL
	 * @param string $appId
	 * @param string $appSecret
	 * @param string $version
	 */
	public static function getLoginURL($permission, $returnURL, $appId, $appSecret, $version = 'v2.2') {
		$fb = new Facebook([
				'app_id' => $appId,
				'app_secret' => $appSecret,
				'default_graph_version' => $version,
		]);
		
		$helper = $fb->getRedirectLoginHelper();
		
		$permissions = [$permission];
		return $helper->getLoginUrl($returnURL, $permissions);
	}
	
	/**
	 * This function return the access token 
	 * @param string $appId
	 * @param string $appSecret
	 * @param string $version
	 */
	public static function getAccessToken($appId, $appSecret, $version = 'v2.2') {
		$response = array();
		$fb = new Facebook([
				'app_id' => $appId, // Replace {app-id} with your app id
				'app_secret' => $appSecret,
				'default_graph_version' => 'v2.2',
		]);
		
		$helper = $fb->getRedirectLoginHelper();
		
		try {
			$accessToken = $helper->getAccessToken();
		} catch(Facebook\Exceptions\FacebookResponseException $e) {
			// When Graph returns an error
			$response['error'] = 'Graph returned an error: ' . $e->getMessage();
			
			return $response;
		} catch(Facebook\Exceptions\FacebookSDKException $e) {
			// When validation fails or other local issues
			$response['error'] =  'Facebook SDK returned an error: ' . $e->getMessage();
			return $response;
		}
		
		if (! isset($accessToken)) {
			if ($helper->getError()) {
				$response['error'] = $helper->getError() . ', ' . $helper->getErrorCode() . ', ' . $helper->getErrorReason() . ', ' . $helper->getErrorDescription();
			} else {
				$response['error'] = 'Bad request';
			}
			return $response;
		}
		
		// Logged in
		$response['shortAccessToken'] = $accessToken->getValue();
		
		// The OAuth 2.0 client handler helps us manage access tokens
		$oAuth2Client = $fb->getOAuth2Client();
		
		// Get the access token metadata from /debug_token
		$tokenMetadata = $oAuth2Client->debugToken($response['shortAccessToken']);
		$response['metadata'] = $tokenMetadata;
		
		// Validation (these will throw FacebookSDKException's when they fail)
		$tokenMetadata->validateAppId($appId); // Replace {app-id} with your app id
		
		// If you know the user ID this access token belongs to, you can validate it here
		//$tokenMetadata->validateUserId('123');
		$tokenMetadata->validateExpiration();
		
		if (! $accessToken->isLongLived()) {
			// Exchanges a short-lived access token for a long-lived one
			try {
				$response['longAccessToken']  = $oAuth2Client->getLongLivedAccessToken($accessToken);
			} catch (Facebook\Exceptions\FacebookSDKException $e) {
				$response['longAccessTokenError'] =  "Error getting long-lived access token: " . $helper->getMessage();
				return $response;
			}
		
			echo '<h3>Long-lived</h3>';
			var_dump($accessToken->getValue());
		}
		
		return $response;
	}
	
	public static function getUserdetail($accessToken, $appId, $appSecret, $user = '/me??fields=email,name,firstName,lastname,id', $version = 'v2.2') {
		$response = array();
		$fb = new Facebook([
				'app_id' => $appId, // Replace {app-id} with your app id
				'app_secret' => $appSecret,
				'default_graph_version' => 'v2.2',
		]);
		
		try {
			// Get the Facebook\GraphNodes\GraphUser object for the current user.
			// If you provided a 'default_access_token', the '{access-token}' is optional.
			$fbResponse = $fb->get('/'. $user, $accessToken);
		} catch(Facebook\Exceptions\FacebookResponseException $e) {
			// When Graph returns an error
			$response['error'] = 'Graph returned an error: ' . $e->getMessage();
			return $response;
		} catch(Facebook\Exceptions\FacebookSDKException $e) {
			// When validation fails or other local issues
			$response['error'] =  'Facebook SDK returned an error: ' . $e->getMessage();
			return $response;
		}
		
		$userDetail = $fbResponse->getGraphUser();
		$response['user'] = $userDetail;
		
		return $response;
	}
}
?>