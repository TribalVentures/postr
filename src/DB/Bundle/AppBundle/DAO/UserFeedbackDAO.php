<?php
namespace DB\Bundle\AppBundle\DAO;

use DB\Bundle\CommonBundle\Base\BaseDAO;
use DB\Bundle\AppBundle\Entity\UserFeedback;
use DB\Bundle\CommonBundle\Util\DBUtil;
use DB\Bundle\AppBundle\Common\Config;

/**
 * Class For UserFeedbackDAO, This class is responsible for manage database 
 * operation for UserFeedback table/entity
 *
 * @namespace DB\Bundle\AppBundle\DAO
 *
 * @author Dipak Patil
 */
class UserFeedbackDAO extends BaseDAO { 
	/**
	 * Always need doctrim object to initilise User dao object
	 * @param $_dm - Doctrime object
	 */
	function __construct($_dm) {
		parent :: __construct($_dm);
	}
	
	/**
	 * This function add new feedback
	 * @param array $feedbackDetail
	 */
	public function addFeedback($feedbackDetail = array()) {
		if(empty($feedbackDetail)) {
			return false;
		}
		
		if(empty($feedbackDetail['accountId'])) {
			$feedbackDetail['accountId'] = '0';
		}
		
		if(empty($feedbackDetail['userId'])) {
			$feedbackDetail['userId'] = '0';
		}
		
		if(empty($feedbackDetail['note'])) {
			$feedbackDetail['note'] = '';
		}
		
		if(empty($feedbackDetail['comment'])) {
			$feedbackDetail['comment'] = '';
		}
		
		if(empty($feedbackDetail['creationDate'])) {
			$feedbackDetail['creationDate'] = new \DateTime();
		}
		
		$userFeedback = new UserFeedback();
		
		$userFeedback->setAccountId($feedbackDetail['accountId']);
		$userFeedback->setUserId($feedbackDetail['userId']);
		
		$userFeedback->setSubject($feedbackDetail['subject']);
		$userFeedback->setFeedback($feedbackDetail['feedback']);
		
		$userFeedback->setNote($feedbackDetail['note']);
		$userFeedback->setComment($feedbackDetail['comment']);
		
		$userFeedback->setCreationDate($feedbackDetail['creationDate']);
		
		$userFeedback = $this->save($userFeedback);
		
		$newDetail = false;
		if(is_object($userFeedback)) {
			$newDetail = $userFeedback->toArray();
		}
		return $newDetail;
	}
	
	/**
	 * This function edit accoun detail
	 * @param array $feedbackDetail
	 */
	public function editFeedback($feedbackDetail = array()) {
		if(empty($feedbackDetail)) {
			return false;
		}
		
		$record = array();
		
		//Set defautl data		
		if(!empty($feedbackDetail['subject'])) {
			$record['subject'] = $feedbackDetail['subject'];
		}
		
		if(!empty($feedbackDetail['feedback'])) {
			$record['feedback'] = $feedbackDetail['feedback'];
		}

		if(!empty($feedbackDetail['note'])) {
			$record['note'] = $feedbackDetail['note'];
		}

		if(!empty($feedbackDetail['comment'])) {
			$record['comment'] = $feedbackDetail['comment'];
		}
		
		$updatedDetail = array();
		if(!empty($record)) {
			$userFeedback = new UserFeedback();
			$userFeedback->setFeedbackId($feedbackDetail['feedbackId']);
			
			$userFeedback = $this->update($userFeedback, $record);
			
			if(is_object($userFeedback)) {
				$updatedDetail = $userFeedback->toArray();
			}
		}
		
		return $updatedDetail;
	}
	
	/**
	 * This function return all email notification detail list
	 * @param string $period
	 */
	public function getuserFeedback($startDate, $endDate, $currentPage = 1) {
		$em = $this->getDoctrine()->getManager();
	
		//$from for entity name (table name)
		$from = "DB\\Bundle\\AppBundle\Entity\\UserFeedback userFeedback ";
	
		if(empty($startDate)) {
			$startDate = date('Y-m-d') . ' 00:00:01';
		}
		
		if(empty($endDate)) {
			$endDate = date('Y-m-d') . ' 23:59:59';
		}
		
		$whereCondition = "userFeedback.creationDate >= '" . $startDate . "'  AND userFeedback.creationDate <= '" . $endDate . "'";

		//Get count of record available in table
		$count = $this->getCountByWhere("userFeedback.feedbackId", $from, $whereCondition);
		
		//Get paging detail
		$paggingDetails = DBUtil::getPaggingDetails($currentPage, $count, Config::getSParameter('RECORDS_PER_PAGE'));
		
		$sql = "SELECT userFeedback.feedbackId, userFeedback.accountId, userFeedback.userId, userFeedback.subject, userFeedback.feedback, " .
				"userFeedback.note, userFeedback.comment, userFeedback.creationDate, " .
				'user.firstName, user.lastName, user.email ' . 
		"FROM " . $from . ' ' . 
		'LEFT JOIN DB\\Bundle\\AppBundle\Entity\\User user ' . 
		'WITH userFeedback.userId = user.userId ';
		'';
	
		if(!empty($whereCondition)) {
			$sql .= " WHERE " . $whereCondition . " ";
		}
	
		$sql .= "ORDER BY userFeedback.feedbackId DESC ";
	
		//echo $sql . "\r\n\r\n";
	
		$query = $em->createQuery($sql);
		$result = $query->setFirstResult($paggingDetails['MYSQL_LIMIT1'])->setMaxResults($paggingDetails['MYSQL_LIMIT2']);
		$result = $query->getResult();
		
		//Format dates
		if(!empty($result)) {
			for($index = 0; $index < count($result); $index ++) {
				if(!empty($result[$index]['creationDate'])) {
					$result[$index]['creationDateAt'] = $result[$index]['creationDate']->format('Y-m-d H:i:s');
				}
			}
		}
	
		$list = array();
		$list['PAGING'] = $paggingDetails;
		$list['LIST'] = $result;
	
		return $list;
	}
}
?>