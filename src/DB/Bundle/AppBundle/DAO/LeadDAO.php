<?php
namespace DB\Bundle\AppBundle\DAO;

use DB\Bundle\CommonBundle\Base\BaseDAO;
use DB\Bundle\AppBundle\Entity\User;
use DB\Bundle\CommonBundle\Util\DBUtil;
use DB\Bundle\CommonBundle\ApiClient\DBSendgridClient;
use DB\Bundle\AppBundle\Common\Config;
use DB\Bundle\AppBundle\Entity\Account;
use DB\Bundle\AppBundle\Entity\Lead;

/**
 * Class For Account DAO, This class is responsible for manage database 
 * operation for account table/entity
 *
 * @namespace DB\Bundle\AppBundle\DAO
 *
 * @author Dipak Patil
 */
class LeadDAO extends BaseDAO { 
	const LEAD_STATUS_SCRAP_BASIC = '0';
	const LEAD_STATUS_SCRAP_DONE = '1';
	
	/**
	 * Always need doctrim object to initilise User dao object
	 * @param $_dm - Doctrime object
	 */
	function __construct($_dm) {
		parent :: __construct($_dm);
	}
	
	/**
	 * This function add lead list into DB
	 * @param unknown $leadDetailList
	 */
	public function addLeadList($leadDetailList) {
		if(!isset($leadDetailList)) {
			return array();
		}
		
		$objectIdList = array();
		$leadMap = array();
		foreach($leadDetailList as $leadDetail) {
			$objectIdList[] = $leadDetail['objectId'];
			$leadMap[$leadDetail['objectId']] = $leadDetail;
		}
		
		$existLeadList = $this->getLeadList($objectIdList);
		if(!empty($existLeadList)) {
			foreach($existLeadList as $existLead) {
				if(isset($leadMap[$existLead['objectId']])) {
					unset($leadMap[$existLead['objectId']]);
				}
			}
		}
		
		//Eclude all leads those are already exist
		$leadList = array();
		foreach($leadMap as $leadDetail) {
			$leadList[] = $this->addLead($leadDetail, true);
		}
		
		return $this->saveBatch($leadList);
	}
	
	/**
	 * This function add new user
	 * @param array $leadDetail
	 */
	public function addLead($leadDetail = array(), $isObject = false) {
		if(empty($leadDetail)) {
			return false;
		}
		
		if(empty($leadDetail['objectId'])) {
			$leadDetail['objectId'] = '';
		}
		
		if(empty($leadDetail['company'])) {
			$leadDetail['company'] = '';
		}
		
		if(empty($leadDetail['contactPerson'])) {
			$leadDetail['contactPerson'] = '';
		}
		
		if(empty($leadDetail['phone'])) {
			$leadDetail['phone'] = '';
		}
		
		if(empty($leadDetail['email'])) {
			$leadDetail['email'] = '';
		}
		
		if(empty($leadDetail['streetAddress'])) {
			$leadDetail['streetAddress'] = '';
		}
		
		if(empty($leadDetail['locality'])) {
			$leadDetail['locality'] = '';
		}
		
		if(empty($leadDetail['region'])) {
			$leadDetail['region'] = '';
		}
		
		if(empty($leadDetail['postalCode'])) {
			$leadDetail['postalCode'] = '';
		}
		
		if(empty($leadDetail['country'])) {
			$leadDetail['country'] = '';
		}
		
		if(empty($leadDetail['url'])) {
			$leadDetail['url'] = '';
		}
		
		if(empty($leadDetail['houzzUrl'])) {
			$leadDetail['houzzUrl'] = '';
		}
		
		if(empty($leadDetail['leadStatus'])) {
			$leadDetail['leadStatus'] = self::LEAD_STATUS_SCRAP_BASIC;
		}
		
		$lead = new Lead();
		
		$lead->setObjectId($leadDetail['objectId']);
		$lead->setCompany($leadDetail['company']);
		$lead->setContactPerson($leadDetail['contactPerson']);
		
		$lead->setPhone($leadDetail['phone']);
		$lead->setEmail($leadDetail['email']);
		
		$lead->setStreetAddress($leadDetail['streetAddress']);
		$lead->setLocality($leadDetail['locality']);
		$lead->setRegion($leadDetail['region']);
		$lead->setPostalCode($leadDetail['postalCode']);
		$lead->setCountry($leadDetail['country']);
		
		$lead->setUrl($leadDetail['url']);
		$lead->setHouzzUrl($leadDetail['houzzUrl']);
		
		$lead->setLeadStatus($leadDetail['leadStatus']);
		
		if($isObject) {
			return $lead;
		}
		
		$lead = $this->save($lead);
		
		$newDetail = false;
		if(is_object($lead)) {
			$newDetail = $lead->toArray();
		}
		return $newDetail;
	}
	
	/**
	 * This function edit accoun detail
	 * @param array $leadDetail
	 */
	public function editLead($leadDetail = array()) {
		if(empty($leadDetail)) {
			return false;
		}
		
		$record = array();
		
		if(!empty($leadDetail['objectId'])) {
			$record['objectId'] = $leadDetail['objectId'];
		}

		if(!empty($leadDetail['company'])) {
			$record['company'] = $leadDetail['company'];
		}

		if(!empty($leadDetail['contactPerson'])) {
			$record['contactPerson'] = $leadDetail['contactPerson'];
		}

		if(!empty($leadDetail['phone'])) {
			$record['phone'] = $leadDetail['phone'];
		}

		if(!empty($leadDetail['email'])) {
			$record['email'] = $leadDetail['email'];
		}

		if(!empty($leadDetail['streetAddress'])) {
			$record['streetAddress'] = $leadDetail['streetAddress'];
		}

		if(!empty($leadDetail['locality'])) {
			$record['locality'] = $leadDetail['locality'];
		}

		if(!empty($leadDetail['region'])) {
			$record['region'] = $leadDetail['region'];
		}

		if(!empty($leadDetail['postalCode'])) {
			$record['postalCode'] = $leadDetail['postalCode'];
		}

		if(!empty($leadDetail['country'])) {
			$record['country'] = $leadDetail['country'];
		}

		if(!empty($leadDetail['url'])) {
			$record['url'] = $leadDetail['url'];
		}

		if(!empty($leadDetail['houzzUrl'])) {
			$record['houzzUrl'] = $leadDetail['houzzUrl'];
		}

		if(!empty($leadDetail['leadStatus'])) {
			$record['leadStatus'] = $leadDetail['leadStatus'];
		}
		
		$updatedDetail = array();
		if(!empty($record)) {
			$lead = new Lead();
			$lead->setId($leadDetail['id']);
			
			$lead = $this->update($lead, $record);
			
			if(is_object($lead)) {
				$updatedDetail = $lead->toArray();
			}
		}
		
		return $updatedDetail;
	}
	
	/**
	 * This function return lead detail by object id
	 * @param unknown $objectId
	 */
	public function getLeadBYObjectId($objectId) {
		return $this->findSingleDetailBy(new Lead(), array('objectId'=>$objectId));
	}
	
	/**
	 * This function return all account category list
	 * @param interger $accountId
	 */
	public function getLeadList($objectIdList = array(), $leadStatus = '', $maxRecord = 500) {
		$em = $this->getDoctrine()->getManager();
		
		//$from for entity name (table name)
		$from = "DB\\Bundle\\AppBundle\Entity\\Lead lead ";
		
		$whereCondition = "";
		if(isset($leadStatus) && ($leadStatus == '1' || $leadStatus == '0')) {
			$whereCondition = ' lead.leadStatus = ' . $leadStatus;
		}
		
		if(!empty($objectIdList)) {
			$maxRecord = count($objectIdList) + 1;
			$objectIdCondition = '';
			foreach($objectIdList as $objectId) {
				if(!empty($objectIdCondition)) {
					$objectIdCondition .= ', ';
				}
				
				$objectIdCondition .= "'" . $objectId . "'";
			}
			
			if(!empty($objectIdCondition)) {
				if(!empty($whereCondition)) {
					$whereCondition .= ' AND ';
				}
				
				$whereCondition .= ' lead.objectId IN (' . $objectIdCondition . ')';
			}
		}
		
		$sql = "SELECT lead.id, lead.objectId, lead.company, lead.contactPerson, lead.phone, " .
				"lead.email, lead.streetAddress, lead.locality, lead.region, " .
				"lead.postalCode, lead.country, lead.url, lead.houzzUrl, lead.leadStatus " .
				"FROM " . $from;
		
		if(!empty($whereCondition)) {
			$sql .= " WHERE " . $whereCondition . " ";
		}
		
		$sql .= "ORDER BY lead.id DESC ";
		
		//echo $sql;
		
		$query = $em->createQuery($sql);
		$result = $query->setFirstResult(0)->setMaxResults($maxRecord);
		$result = $query->getResult();
		
		return $result;
	}
}
?>