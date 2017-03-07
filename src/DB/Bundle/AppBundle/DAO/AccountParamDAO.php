<?php
namespace DB\Bundle\AppBundle\DAO;

use DB\Bundle\CommonBundle\Base\BaseDAO;
use DB\Bundle\AppBundle\Entity\AccountParam;

/**
 * Class For Account Param DAO, This class is responsible for manage database 
 * operation for account_frequency table/entity
 *
 * @namespace DB\Bundle\AppBundle\DAO
 *
 * @author Dipak Patil
 */
class AccountParamDAO extends BaseDAO {
	
	/**
	 * Always need doctrim object to initilise Account frequency dao object
	 * @param $_dm - Doctrime object
	 */
	function __construct($_dm) {
		parent :: __construct($_dm);
	}
	
	/**
	 * This function add account extra parameters in DB
	 * @param array $accountParam
	 * @return boolean
	 */
	public function addAccountParam($accountParamDetail = array()) {
		if(empty($accountParamDetail) || empty($accountParamDetail['accountId'])) {
			return array();	
		}
		
		if(empty($accountParamDetail['discountCode'])) {
			$accountParamDetail['discountCode'] = '';
		}
		
		if(empty($accountParamDetail['sid'])) {
			$accountParamDetail['sid'] = '';
		}
		
		if(empty($accountParamDetail['lastUpdate'])) {
			$accountParamDetail['lastUpdate'] = new \DateTime();
		}
		
		$accountParam = new AccountParam();
		
		$accountParam->setAccountId($accountParamDetail['accountId']);
		
		$accountParam->setDiscountCode($accountParamDetail['discountCode']);
		$accountParam->setSid($accountParamDetail['sid']);
		
		$accountParam->setLastUpdate($accountParamDetail['lastUpdate']);
		
		$accountParam = $this->save($accountParam);
		
		$newDetail = false;
		if(is_object($accountParam)) {
			$newDetail = $accountParam->toArray();
		}
		return $newDetail;
	}
	
	/**
	 * This function edit account parameters
	 * @param array $accountParam
	 */
	public function editAccontParam($accountParam = array()) {
		if(empty($accountParam)) {
			return false;
		}
		
		$record = array();
		
		//Set defautl data
		if(!empty($accountParam['discountCode'])) {
			$record['discountCode'] = $accountParam['discountCode'];
		}
		
		if(!empty($accountParam['sid'])) {
			$record['sid'] = $accountParam['sid'];
		}
		
		if(!empty($accountParam['lastUpdate'])) {
			$record['lastUpdate'] = $accountParam['lastUpdate'];
		} else {
			$record['lastUpdate'] = new \DateTime();
		}
		
		$updateDetail = array();
		if(!empty($record)) {
			$accountParam = new AccountParam();
			$accountParam->setId($accountParam['id']);
				
			$accountParam = $this->update($accountParam, $record);
				
			if(is_object($accountParam)) {
				$updateDetail = $accountParam->toArray();
			}
		}
		
		return $updateDetail;
	}
	
	/**
	 * This functio manage the account parameters, If param exist then update otherwise add new
	 * @param unknown $accountParam
	 */
	public function manageAccountParameter($accountParam) {
		if(empty($accountParam) || empty($accountParam['accountId'])) {
			return array();
		}
		
		$accountParamDetal = $this->findSingleDetailBy(new AccountParam(), array('accountId'=>$accountParam['accountId']));
		if(!empty($accountParamDetal['id'])) {
			$accountParam['id'] = $accountParamDetal['id'];
			return $this->editAccontParam($accountParam);
		} else {
			return $this->addAccountParam($accountParam);
		}
	}
}
?>