<?php
namespace DB\Bundle\CommonBundle\ApiClient;

use Braintree\Configuration;
use Braintree\Plan;
use Braintree\Customer;
use Braintree\ClientToken;
use Braintree\Subscription;
use Braintree\WebhookNotification;
use Braintree\Transaction;
use Braintree\PaymentMethodNonce;
use Braintree\PaymentMethod;
use DB\Bundle\CommonBundle\Util\DBUtil;
use Braintree\TransactionSearch;
/**
 * This class is call braintree API and return response as it is
 * @author patildipakr
 *
 */
class DBBraintreeClient {
	private $environment = 'sandbox';
	private $merchantId = '';
	private $publicKey = '';
	private $privateKey = '';
	
	/**
	 * This method create braintree client object with initialise the environment
	 * @param string $environment
	 * @param string $merchantId
	 * @param string $publicKey
	 * @param string $privateKey
	 */
	function __construct($environment, $merchantId, $publicKey, $privateKey) {
		//Assign variable and then initialise the environment
		$this->environment = $environment;
		$this->merchantId = $merchantId;
		$this->publicKey = $publicKey;
		$this->privateKey = $privateKey;
		
		Configuration::environment($this->environment);
		Configuration::merchantId($this->merchantId );
		Configuration::publicKey($this->publicKey);
		Configuration::privateKey($this->privateKey);
	}
	
	/**
	 * This function create customer in brain tree and 
	 * return customer id and credit card id if success
	 * oterwise return the error messages array
	 * @param mixed[] $customerDetail
	 */
	public function createCustomer($customerDetail) {
		if(empty($customerDetail)) {
			return array();
		}
		
		$param = array();
		//Set customer detail
		//$param['id'] 		= $customerDetail['id'];
		
		if(!empty($customerDetail['firstName'])) {
			$param['firstName'] = $customerDetail['firstName'];
		}
		
		if(!empty($customerDetail['lastName'])) {
			$param['lastName'] 	= $customerDetail['lastName'];
		}
		
		if(!empty($customerDetail['email'])) {
			$param['email'] 	= $customerDetail['email'];
		}
		
		if(isset($customerDetail['contact'])) {
			$param['phone'] = $customerDetail['contact'];
		}
		
		//Set customer card details
		$param['creditCard'] = array();
		$param['paymentMethodNonce'] = $customerDetail['paymentMethodNonce'];
		$result = Customer::create($param);
		
		$response = array();
		if ($result->success) {
			$customer = array();
			$customer['id'] 			= $result->customer->id;
			$customer['cardToken'] 		= $result->customer->creditCards[0]->token;
			$customer['creditCardNo'] 	= $result->customer->paymentMethods[0]->maskedNumber;
			$customer['expirationDate'] = $result->customer->paymentMethods[0]->expirationDate;
			$customer['cardType'] 		= $result->customer->paymentMethods[0]->cardType;
			
			$response['customer'] 	= $customer;
		} else {
			$errorDetail = array();
			foreach ($result->errors->deepAll() as $error) {
				$errorDetail[] = $error->message;
			}
			$response['error'] = $errorDetail;
		}
		return $response;
	}
	
	/**
	 * This function create customer in brain tree and 
	 * return customer id and credit card id if success
	 * oterwise return the error messages array
	 * @param mixed[] $customerDetail
	 */
	public function updateCustomer($customerDetail) {
		if(empty($customerDetail)) {
			return array();
		}
		if(!isset($customerDetail['btCustomerId']) || empty($customerDetail['btCustomerId'])) {
			return array();
		}
		/* if(!isset($customerDetail['billingZipCode']) || empty($customerDetail['billingZipCode'])) {
			return array();
		} */

		$param = array();
		if(!empty($customerDetail['billingZipCode'])) {
			$param['creditCard']['billingAddress']['postalCode'] 	= $customerDetail['billingZipCode'];
		}
		
		if(isset($customerDetail['paymentMethodNonce']) && !empty($customerDetail['paymentMethodNonce'])) {
			$param['paymentMethodNonce'] = $customerDetail['paymentMethodNonce'];
		}
		
		$response = array();
		if(!empty($param)) {
			$result = Customer::update($customerDetail['btCustomerId'], $param);
			if ($result->success) {
				$customer = array();
				$customer['id'] 			= $result->customer->id;
				$customer['cardToken'] 		= $result->customer->creditCards[0]->token;
				$customer['creditCardNo'] 	= $result->customer->paymentMethods[0]->maskedNumber;
				$customer['expirationDate'] = $result->customer->paymentMethods[0]->expirationDate;
				$customer['cardType'] 		= $result->customer->paymentMethods[0]->cardType;
				
				$response['customer'] 		= $customer;
			} else {
				$errorDetail = array();
				foreach ($result->errors->deepAll() as $error) {
					$errorDetail[] = $error->message;
				}
				$response['error'] = $errorDetail;
			}
		}
		return $response;
	}
	
	/**
	 * This function make oayment method default
	 * @param string $btCardtoken
	 */
	public function makePaymentMethodDefault($btCardtoken) {
		if(empty($btCardtoken)) {
			return array();
		}
		
		return $this->updatePaymentMethod($btCardtoken, array('options'=>array('makeDefault'=>true)));
	}
	
	/**
	 * This function update payment method in brain tree
	 * @param string $paymentMethodToken
	 * @return mixed[] Return the response of payment method
	 */
	public function updatePaymentMethod($paymentMethodToken, $param = array()) {
		if(empty($paymentMethodToken) || empty($param)) {
			return array();
		}
		$response = array();
		
		try {
			$result = PaymentMethod::update($paymentMethodToken, $param);
			
			if ($result->success) {
				$paymentMethodDetail = array();
				$paymentMethodDetail['cardToken'] 		= $result->paymentMethod->token;
				$paymentMethodDetail['customerId'] 		= $result->paymentMethod->customerId;
				$paymentMethodDetail['creditCardNo'] 	= $result->paymentMethod->maskedNumber;
				$paymentMethodDetail['expirationDate'] 	= $result->paymentMethod->expirationDate;
				
				$response['paymentMethodDetail'] = $paymentMethodDetail;
			} else {
				$errorDetail = array();
				foreach ($result->errors->deepAll() as $error) {
					$errorDetail[] = $error->message;
				}
				$response['error'] = $errorDetail;
			}
		} catch(Exception $e) {
			$response['error'] = $e->getMessage();
		}
		
		return $response;
	}
	
	/**
	 * This dunction will return all plans exist in you account
	 */
	public function getPlanList() {
		$fieldList = 'id,merchantId,billingDayOfMonth,billingFrequency,currencyIsoCode,description,name,numberOfBillingCycles,price,trialDuration,trialDurationUnit,trialPeriod,createdAt,updatedAt,addOns,discounts,plans';
		$fieldList = explode(',', $fieldList);
		$planDetailList = array();
		
		$planList = Plan::all();
		if(!empty($planList)) {
			foreach($planList as $plan) {
				$record = array();
				foreach($fieldList as $field) {
					$field = trim($field);
					
					if(isset($plan->$field)) {
						$record[$field] = $plan->$field;
					}
				}
				if(!empty($record)) {
					$planDetailList[] = $record;
				}
			}
		}
		return $planDetailList;
	}
	
	/**
	 * This function will returnt he plan by id
	 * @param stirng $planId
	 */
	public function getPlan($planId) {
		$planList = $this->getPlanList();
		
		$planDetail = array();
		if(!empty($planList)) {
			foreach($planList as $plan) {
				if($planId == $plan['id']) {
					$planDetail = $plan;
					break;
				}
			}
		}
		
		return $planDetail;
	}
	
	/**
	 * This function generate the Client token
	 * @return string Return client token
	 */
	public function getClientToken() {
		$clientToken = ClientToken::generate();
		return $clientToken;
	}
	
	/**
	 * This function create subscription in brain tree and 
	 * @param mixed[] $subscriptionDetail
	 * @return mixed[] Return the response of subscription
	 */
	public function createSubscription($subscriptionDetail) {
		if(empty($subscriptionDetail)) {
			return array();
		}
		
		$param = array();
		
		//Set subscription data
		$param['paymentMethodToken'] = $subscriptionDetail['paymentMethodToken'];
		$param['planId'] = $subscriptionDetail['planId'];
		
		if(isset($subscriptionDetail['firstBillingDate']) && !empty($subscriptionDetail['firstBillingDate'])) {
			$param['firstBillingDate'] = $subscriptionDetail['firstBillingDate'];
		}
		
		if(isset($subscriptionDetail['addOnProductList']) && !empty($subscriptionDetail['addOnProductList'])) {
			$param['addOns'] = $subscriptionDetail['addOnProductList'];
		}
		
		$result = Subscription::create($param);
		//print_r($result);
		$response = array();
		if ($result->success) {
			$sucription = array();
			$sucription['id'] 		= $result->subscription->id;
			$response['subscription'] 	= $sucription;
		} else {
			$errorDetail = array();
			foreach ($result->errors->deepAll() as $error) {
				$errorDetail[] = $error->message;
			}
			$response['error'] = $errorDetail;
		}
		
		return $response;
	}
	
	/**
	 * This function create subscription in brain tree and 
	 * @param mixed[] $subscriptionDetail
	 * @return mixed[] Return the response of subscription
	 */
	public function updateSubscription($subscriptionDetail) {
		if(empty($subscriptionDetail)) {
			return array();
		}
		
		$param = array();
		//Set subscription data
		if(isset($subscriptionDetail['addOnProductList']) && !empty($subscriptionDetail['addOnProductList'])) {
			$param['addOns'] = $subscriptionDetail['addOnProductList'];
		}
			
		//print_r($param);exit;
		$result = Subscription::update($subscriptionDetail['subscriptionId'], $param);
		//print_r($result);
		$response = array();
		if ($result->success) {
			$sucription = array();
			$sucription['id'] 		= $result->subscription->id;
			$response['subscription'] 	= $sucription;
		} else {
			$errorDetail = array();
			foreach ($result->errors->deepAll() as $error) {
				$errorDetail[] = $error->message;
			}
			$response['error'] = $errorDetail;
		}
		//print_r($response);
		return $response;
	}
	
	/**
	 * This function create subscription in brain tree and 
	 * @param mixed[] $subscriptionDetail
	 * @return mixed[] Return the response of subscription
	 */
	public function updateSubscriptionPlan($subscriptionDetail) {
		if(empty($subscriptionDetail)) {
			return array();
		}
		
		$subscriptionId = $subscriptionDetail['subscriptionId'];
		unset($subscriptionDetail['subscriptionId']);
		
		//print_r($param);exit;
		$result = Subscription::update($subscriptionId, $subscriptionDetail);
		//print_r($result);
		$response = array();
		if ($result->success) {
			$sucription = array();
			$sucription['id'] 		= $result->subscription->id;
			$response['subscription'] 	= $sucription;
		} else {
			$errorDetail = array();
			foreach ($result->errors->deepAll() as $error) {
				$errorDetail[] = $error->message;
			}
			$response['error'] = $errorDetail;
		}
		//print_r($response);
		return $response;
	}
	
	/**
	 * This function cancel subscription in brain tree and 
	 * @param mixed[] $subscriptionDetail
	 * @return mixed[] Return the response of subscription
	 */
	public function cancelSubscription($subscriptionId) {
		if(empty($subscriptionId)) {
			return array();
		}
		try {
			$result = Subscription::cancel($subscriptionId);
			//print_r($result);exit;
			$response = array();
			if ($result->success) {
				$sucription = array();
				$sucription['id'] 		= $result->subscription->id;
				$response['subscription'] 	= $sucription;
			} else {
				$errorDetail = array();
				foreach ($result->errors->deepAll() as $error) {
					$errorDetail[] = $error->message;
				}
				$response['error'] = $errorDetail;
			}
		} catch(Exception $e) {
			$response['error'] = $e->getMessage();
		}
		//print_r($response);
		return $response;
	}
	


	/**
	 * This function cancel subscription in brain tree and
	 * @param mixed[] $subscriptionDetail
	 * @return mixed[] Return the response of subscription
	 */
	public function webhookNotificationDecode($bt_signature, $bt_payload) {
		$webhookNotification = WebhookNotification::parse($bt_signature, $bt_payload);

		return $webhookNotification;
	}
	
	/**
	 * This function create subscription in brain tree and 
	 * @param mixed[] $subscriptionDetail
	 * @return mixed[] Return the response of subscription
	 */
	public function findSubscription($subscriptionId) {
		if(empty($subscriptionId)) {
			return array();
		}
		
		$subscription = array();
		try {
			$result = Subscription::find($subscriptionId);
			
			if (is_object($result)) {
				$subscription['id'] = $result->id;
				$subscription['nextBillingPeriodAmount'] = $result->nextBillingPeriodAmount;
				$subscription['planId'] = $result->planId;
				$subscription['status'] = $result->status;
				$subscription['nextBillingDate'] = DBUtil::format($result->nextBillingDate, 'M d, Y');
			}
		} catch(Exception $e) {
			$subscription['error'] = $e->getMessage();
		}
		
		return $subscription;
	}
	
	/**
	 * This function create payment method nonce in brain tree and 
	 * @param string $paymentMethodToken
	 * @return mixed[] Return the response of payment method nonce
	 */
	public function createPaymentMethodNonce($paymentMethodToken) {
		if(empty($paymentMethodToken)) {
			return array();
		}
		
		try {
			$result = PaymentMethodNonce::create($paymentMethodToken);
			//print_r($result);exit;
			$response = array();
			if ($result->success) {
				$paymentMethodNonceDetail = array();
				$paymentMethodNonceDetail['id'] = $result->paymentMethodNonce->nonce;
				$response['paymentMethodNonce'] = $paymentMethodNonceDetail;
			} else {
				$errorDetail = array();
				foreach ($result->errors->deepAll() as $error) {
					$errorDetail[] = $error->message;
				}
				$response['error'] = $errorDetail;
			}
		} catch(Exception $e) {
			print_r($e->getMessage());
		}
		//print_r($response);
		return $response;
	}
	
	/**
	 * This function create sale transaction in braintree 
	 * @param mixed[] $subscriptionDetail
	 * @return mixed[] Return the response of sale transaction response
	 */
	public function saleTransaction($saleDetail) {
		if(empty($saleDetail)) {
			return array();
		}
		
		try {
			$result = Transaction::sale($saleDetail);
			//print_r($result);exit;
			$response = array();
			if ($result->success) {
				$saleDetailDetail = array();
				$saleDetailDetail['id'] = $result->transaction->id;
				$response['saleTransaction'] = $saleDetailDetail;
			} else {
				$errorDetail = array();
				foreach ($result->errors->deepAll() as $error) {
					$errorDetail[] = $error->message;
				}
				$response['error'] = $errorDetail;
			}
		} catch(Exception $e) {
			print_r($e->getMessage());
		}
		
		return $response;
	}
	
	/**
	 * This will search the transaction in braintree and return
	 * @param integer $customerId
	 * @param array $criteria
	 * @return mixed
	 */
	public function searchTransaction($customerId, $criteria = array()) {
		if(empty($customerId)) {
			return array();
		}
		$result = array();
		try {
			$transactionList = Transaction::search([
					TransactionSearch::customerId()->is($customerId)
			]);
			
			$result['transactionList'] = array();
			foreach($transactionList as $transaction) {
				$record = array();
				$record['id'] 						= $transaction->id;
				$record['amount'] 					= $transaction->amount;
				$record['status'] 					= $transaction->status;
				$record['billingPeriodStartDate'] 	= DBUtil::format($transaction->subscriptionDetails->billingPeriodStartDate, 'M d, Y');
				$record['billingPeriodEndDate'] 	= DBUtil::format($transaction->subscriptionDetails->billingPeriodEndDate, 'M d, Y');
				$record['createdAt'] 				= DBUtil::format($transaction->createdAt, 'M d, Y');
				
				$result['transactionList'][] 		= $record;
			}
			
		} catch(Exception $e) {
			$result['error'] = $e->getMessage();
		}
		
		return $result;
	}
}
?>