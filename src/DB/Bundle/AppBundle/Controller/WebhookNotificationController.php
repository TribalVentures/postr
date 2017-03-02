<?php

namespace DB\Bundle\AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Braintree\WebhookNotification;
use DB\Bundle\AppBundle\DAO\AccountDAO;
use DB\Bundle\AppBundle\Entity\Account;

class WebhookNotificationController extends DbAppController {
	/**
	 * @Route("/webhook/braintree", name="db_postreach_webhook_braintree")
	 */
	public function webhookBraintreeAction() {
		$signature 	= $this->getRequestParam('bt_signature', '');
		$payload 	= $this->getRequestParam('bt_payload', '');
		
		$response = array('status'=>false, 'message'=>'Problem while cancel subscription');
		if(!empty($signature) && isset($payload)) {
	    	$webhookNotification = WebhookNotification::parse($signature, $payload);
			
	    	if($webhookNotification->kind == WebhookNotification::SUBSCRIPTION_CANCELED) {
	    		if(isset($webhookNotification->subscription->id)) {
	    			//Set account status status to send confirmation email
	    			$accountDAO = new AccountDAO($this->getDoctrine());
	    			
	    			//Get account detail by subscription
	    			$accountDetail = $accountDAO->findSingleDetailBy(new Account(), array('btSubscriptionId'=>$webhookNotification->subscription->id));
	    			
	    			if(!empty($accountDetail['accountId'])) {
	    				$accountDAO->cancelAccount($accountDetail['accountId']);
	    				$response['message'] = 'Subscription remove successfully';
	    			} else {
	    				$response['status'] = false;
	    				$response['message'] = 'No account found for subscription: ' .$webhookNotification->subscription->id;
	    			}
	    		}
	    	}
		}
		
		$this->addInResponse('response', $response);
		
		return $this->getJsonResponse($this->getResponse());
	}
}
?>