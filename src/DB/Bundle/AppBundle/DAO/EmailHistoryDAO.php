<?php
namespace DB\Bundle\AppBundle\DAO;

use DB\Bundle\CommonBundle\Base\BaseDAO;
use DB\Bundle\AppBundle\Entity\ArticleNotifyHistory;
use DB\Bundle\AppBundle\Entity\EmailHistory;

/**
 * Class For ArticleNotifyHistoryDAO DAO, This class is responsible for manage database 
 * operation for account table/entity
 *
 * @namespace DB\Bundle\AppBundle\DAO
 *
 * @author Dipak Patil
 */
class EmailHistoryDAO extends BaseDAO { 
	/**
	 * Always need doctrim object to initilise ArticleNotifyHistoryDAO dao object
	 * @param $_dm - Doctrime object
	 */
	function __construct($_dm) {
		parent :: __construct($_dm);
	}
	
	/**
	 * This function add new email history
	 * @param array $trendingArticleDetail
	 */
	public function addEmailHistory($emailHistoryDetail = array()) {
		if(empty($emailHistoryDetail) || empty($emailHistoryDetail['accountId'])) {
			return array();
		}
		
		//Set defautl data
		if(empty($emailHistoryDetail['creationDate'])) {
			$emailHistoryDetail['creationDate'] = new \DateTime();
		}
		
		$emailHistory = new EmailHistory();
		
		$emailHistory->setAccountId($emailHistoryDetail['accountId']);
		$emailHistory->setCreationDate($emailHistoryDetail['creationDate']);
		
		$emailHistory = $this->save($emailHistory);
		
		$newDetail = false;
		if(is_object($emailHistory)) {
			$newDetail = $emailHistory->toArray();
		}
		
		return $newDetail;
	}
}
?>