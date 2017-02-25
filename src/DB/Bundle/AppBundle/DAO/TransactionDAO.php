<?php
namespace DB\Bundle\AppBundle\DAO;

use DB\Bundle\CommonBundle\Base\BaseDAO;
use DB\Bundle\AppBundle\Entity\Transaction;
use DB\Bundle\CommonBundle\Util\DBUtil;
/**
 * Class For Account Frequency DAO, This class is responsible for manage database 
 * operation for transaction table/entity
 *
 * @namespace DB\Bundle\AppBundle\DAO
 *
 * @author Dipak Patil
 */
class TransactionDAO extends BaseDAO {
	
	/**
	 * Always need doctrim object to initilise Transaction dao object
	 * @param $_dm - Doctrime object
	 */
	function __construct($_dm) {
		parent :: __construct($_dm);
	}
	
	/**
	 * This function return all transaction of account
	 * @param integer $accountId
	 */
	public function getTransactionList($accountId) {
		$transactionList = $this->findBy(new Transaction(), array('accountId'=>$accountId));
		
		$transactionDetailList = array();
		if(!empty($transactionList)) {
			for($index = 0; $index < count($transactionList); $index ++) {
				$record = $transactionList[$index]->toArray();
				
				$record['creationDateAt'] = DBUtil::format($transactionList[$index]->getCreationDate(), 'd/m/Y');
				
				$transactionDetailList[] = $record;
			}
		}
		
		return $transactionDetailList;
	}
}
?>