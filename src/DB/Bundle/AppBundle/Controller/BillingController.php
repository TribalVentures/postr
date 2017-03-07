<?php

namespace DB\Bundle\AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use DB\Bundle\AppBundle\DAO\TransactionDAO;
use DB\Bundle\CommonBundle\ApiClient\DBBraintreeClient;
use DB\Bundle\AppBundle\Common\Config;
use DB\Bundle\AppBundle\DAO\AccountDAO;
use DB\Bundle\AppBundle\Entity\Account;

class BillingController extends DbAppController {
	/**
	 * @Route("/billing", name="db_postreach_billing")
	 * @Template("DBAppBundle:postr:billing.html.twig")
	 */
	public function billingAction() {
		$isValid = $this->isValidUserRequest();
		if(!empty($isValid['nextRoute'])) {
			return $this->sendRequest($isValid['nextRoute']);
		}
		
		$currentUser = $this->getUser();
		//Get customer billing detail
		if(!empty($currentUser['account'])) {
			$this->addInResponse('accountDetail', $currentUser['account']);
		}
		
		$transactionDAO = new TransactionDAO($this->getDoctrine());
		$transactionList = $transactionDAO->getTransactionList($currentUser['accountId']);
		$this->addInResponse('transactionList', $transactionList);
		
		//Create obejct of DBBraintreeClient
		$dbBraintreeClient = new DBBraintreeClient(Config::getSParameter('BRAINTREE_ENVIRONMENT'),
				Config::getSParameter('BRAINTREE_MERCHANT_ID'), Config::getSParameter('BRAINTREE_PUBLIC_KEY'),
				Config::getSParameter('BRAINTREE_PRIVATE_KEY'));
		
		$clientToken = $dbBraintreeClient->getClientToken();
		
		$this->addInResponse('clientToken', $clientToken);
		
		//Get plan detail
		$isPlanNotExist = false;
		if(!isset($currentUser['planDetail'])) {
			//Get plan detail from account
			$planId = Config::getSParameter('BRAINTREE_PLAN_ID');
			if(!empty($currentUser['account']['btPlanId'])) {
				$planId = $currentUser['account']['btPlanId'];
			} else {
				$isPlanNotExist = true;
			}
			
			$planDetail = $dbBraintreeClient->getPlan($planId);
			if(!empty($planDetail['id'])) {
				$currentUser['planDetail'] = $planDetail;
			}
		
			$this->setUser($currentUser);
		}
		
		if(!isset($currentUser['transactionList']) && !empty($currentUser['account']['btCustomerId'])) {
			//Get account detail
			$result = $dbBraintreeClient->searchTransaction($currentUser['account']['btCustomerId']);
			if(empty($result['error'])) {
				$currentUser['transactionList'] = $result['transactionList'];
			}
				
			$this->setUser($currentUser);
		}
		
		if(!isset($currentUser['subscription']) && !empty($currentUser['account']['btSubscriptionId'])) {
			$subscription = $dbBraintreeClient->findSubscription($currentUser['account']['btSubscriptionId']);
			if(empty($subscription['error'])) {
				$currentUser['subscription'] = $subscription;
				
				//Update current planId into DB
				if($isPlanNotExist && !empty($subscription['planId'])) {
					$currentUser['account']['btPlanId'] = $subscription['planId'];
					$accountDAO = new AccountDAO($this->getDoctrine());
					$accountDAO->update((new Account())->setAccountId($currentUser['account']['accountId']), array('btPlanId'=>$subscription['planId']));
				}
			}
			
			$this->setUser($currentUser);
		}
		
		if(isset($currentUser['transactionList'])) {
			$this->addInResponse('transactionList', $currentUser['transactionList']);
		}
		
		if(isset($currentUser['subscription'])) {
			$this->addInResponse('subscription', $currentUser['subscription']);
		}
		
		if(isset($currentUser['planDetail'])) {
			$this->addInResponse('planDetail', $currentUser['planDetail']);
		}
		
		return $this->getResponse();
	}
	
	/**
	 * @Route("/update-payment-method", name="db_postreach_update_payment_method")
	 */
	public function updatePaymentMethodAction() {
		$isValid = $this->isValidUserRequest();
		if(!empty($isValid['nextRoute'])) {
			return $this->getJSONSessionExpire($isValid['nextRoute']);
		}
		
		$currentUser = $this->getUser();
		
		if($this->getRequest()->isMethod('POST')) {
			$paymentMethodNonce = $this->getRequestParam('paymentMethodNonce', '');
			
			if(!empty($paymentMethodNonce)) {
				$accountDAO = new AccountDAO($this->getDoctrine());
				$accountDetail = $accountDAO->findSingleDetailBy(new Account(), array('accountId'=>$currentUser['accountId']));
				
				if(!empty($accountDetail)) {
					$accountDetail['paymentMethodNonce'] = $paymentMethodNonce;
					$accountDetail = $accountDAO->updatePaymentMethod($accountDetail);
					
					if(!empty($accountDetail['error'])) {
						$this->addInResponse('error', 'Error while saving card detail, Please contact to POSTR team');
					} else {
						//Update account detail in session
						$accountDetail = $accountDAO->findSingleDetailBy(new Account(), array('accountId'=>$currentUser['accountId']));
						$currentUser['account'] = $accountDetail;
						$this->setUser($currentUser);
						
						$this->addInResponse('message', 'Your card detail updated successfully');
					}
				}
			} else {
				$this->addInResponse('error', 'Error while saving card detail, Please contact to POSTR team');
			}
		}
		
		return $this->getJsonResponse($this->getResponse());
	}
}
?>