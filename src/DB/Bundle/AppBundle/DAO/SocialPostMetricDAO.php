<?php
namespace DB\Bundle\AppBundle\DAO;

use DB\Bundle\CommonBundle\Base\BaseDAO;
use DB\Bundle\AppBundle\Entity\SocialPostMetric;
use DB\Bundle\CommonBundle\Util\DBUtil;

/**
 * Class For SocialPostMetric DAO, This class is responsible for manage database 
 * operation for SocialPostMetric table/entity
 *
 * @namespace DB\Bundle\AppBundle\DAO
 *
 * @author Dipak Patil
 */
class SocialPostMetricDAO extends BaseDAO { 
	/**
	 * Always need doctrim object to initilise SocialPostMetric dao object
	 * @param $_dm - Doctrime object
	 */
	function __construct($_dm) {
		parent :: __construct($_dm);
	}
	
	/**
	 * This function add new facebook page
	 * @param array $socialPostMetricDetail
	 */
	public function addSocialPostMetric($socialPostMetricDetail = array()) {
		if(empty($socialPostMetricDetail)) {
			return false;
		}
		
		if(empty($socialPostMetricDetail['fbSocial'])) {
			$socialPostMetricDetail['fbSocial'] = '0';
		}
		
		if(empty($socialPostMetricDetail['fbLike'])) {
			$socialPostMetricDetail['fbLike'] = '0';
		}
		
		if(empty($socialPostMetricDetail['fbShare'])) {
			$socialPostMetricDetail['fbShare'] = '0';
		}
		
		if(empty($socialPostMetricDetail['fbComment'])) {
			$socialPostMetricDetail['fbComment'] = '0';
		}
		
		if(empty($socialPostMetricDetail['twLike'])) {
			$socialPostMetricDetail['twLike'] = '0';
		}
		
		if(empty($socialPostMetricDetail['socialIncrease'])) {
			$socialPostMetricDetail['socialIncrease'] = '0';
		}
		
		$socialPostMetricDetail['social'] = $socialPostMetricDetail['fbSocial'] + $socialPostMetricDetail['fbLike'] + $socialPostMetricDetail['fbShare'] + $socialPostMetricDetail['fbComment'] +  $socialPostMetricDetail['twLike'];
		
		if(empty($socialPostMetricDetail['creationDate'])) {
			$socialPostMetricDetail['creationDate'] = new \DateTime();
		}
		
		$socialPostMetric = new SocialPostMetric();
		
		$socialPostMetric->setAccountId($socialPostMetricDetail['accountId']);
		$socialPostMetric->setSocialPostId($socialPostMetricDetail['socialPostId']);
		
		$socialPostMetric->setSocial($socialPostMetricDetail['social']);
		$socialPostMetric->setSocialIncrease($socialPostMetricDetail['socialIncrease']);
		
		$socialPostMetric->setFbSocial($socialPostMetricDetail['fbSocial']);
		$socialPostMetric->setFbLike($socialPostMetricDetail['fbLike']);
		$socialPostMetric->setFbShare($socialPostMetricDetail['fbShare']);
		$socialPostMetric->setFbComment($socialPostMetricDetail['fbComment']);
		
		$socialPostMetric->setTwLike($socialPostMetricDetail['twLike']);
		
		$socialPostMetric->setCreationDate($socialPostMetricDetail['creationDate']);
		
		$socialPostMetric = $this->save($socialPostMetric);
		
		$newDetail = false;
		if(is_object($socialPostMetric)) {
			$newDetail = $socialPostMetric->toArray();
		}
		return $newDetail;
	}
	
	/**
	 * This function update facebook page
	 * @param array $socialPostMetricDetail
	 */
	public function editSocialPostMetric($socialPostMetricDetail = array()) {
		if(empty($socialPostMetricDetail)) {
			return false;
		}
		$record = array();
		
		if(!empty($socialPostMetricDetail['fbSocial'])) {
			$record['fbSocial'] = $socialPostMetricDetail['fbSocial'];
		} else {
			$record['fbSocial'] = 0;
		}
		
		if(!empty($socialPostMetricDetail['fbLike'])) {
			$record['fbLike'] = $socialPostMetricDetail['fbLike'];
		} else {
			$record['fbLike'] = 0;
		}
		
		if(!empty($socialPostMetricDetail['fbShare'])) {
			$record['fbShare'] = $socialPostMetricDetail['fbShare'];
		} else {
			$record['fbShare'] = '0';
		}
		
		if(!empty($socialPostMetricDetail['fbComment'])) {
			$record['fbComment'] = $socialPostMetricDetail['fbComment'];
		} else {
			$record['fbComment'] = '0';
		}
		
		if(!empty($socialPostMetricDetail['twLike'])) {
			$record['twLike'] = $socialPostMetricDetail['twLike'];
		} else {
			$record['twLike'] = '0';
		}
		
		$record['social'] = $socialPostMetricDetail['fbSocial'] + $socialPostMetricDetail['fbLike'] + $socialPostMetricDetail['fbShare'] + $socialPostMetricDetail['fbComment'] +  $socialPostMetricDetail['twLike'];
		
		$updateDetail = false;
		if(!empty($record)) {
			$SocialPostMetric = new SocialPostMetric();
			$SocialPostMetric->setSocialPostMetricId($socialPostMetricDetail['socialPostMetricId']);
			
			$socialPostMetric = $this->update($SocialPostMetric, $record);
			if(is_object($socialPostMetric)) {
				$updateDetail = $socialPostMetric->toArray();
			}
		}
		
		return $updateDetail;
	}
	
	/**
	 * This function manage the social metric and maintain one recrods per day
	 * @param array $socialPostMetricDetail
	 */
	public function manageSocialMetric($socialPostMetricDetail = array()) {
		if(empty($socialPostMetricDetail) || empty($socialPostMetricDetail['socialPostId'])) {
			return;
		}
		
		$currentDate = new \DateTime();
		
		$socialPostMetric = $this->findSingleDetailBy(new SocialPostMetric(), array('socialPostId'=>$socialPostMetricDetail['socialPostId'], 'creationDate'=>$currentDate));
	
		if(empty($socialPostMetric)) {
			//Get previous date records
			$endDate = date('Y-m-d', strtotime('-' . 1 . ' day', strtotime(date('Y-m-d H:i:s'))));
			
			$yesterdayDate = new \DateTime($endDate);
			
			$lastSocialPostMetric = $this->findSingleDetailBy(new SocialPostMetric(), array('socialPostId'=>$socialPostMetricDetail['socialPostId'], 'creationDate'=>$yesterdayDate));
			if(!empty($lastSocialPostMetric)) {
				$socialPostMetricDetail['socialIncrease'] = $lastSocialPostMetric['social'];
			}
			
			//This function will add new social metric in DB
			$this->addSocialPostMetric($socialPostMetricDetail);
		} else {
			//This will update the social metric in DB
			if(isset($socialPostMetricDetail['fbSocial'])) {
				$socialPostMetric['fbSocial'] = $socialPostMetricDetail['fbSocial'];
			}
			
			if(isset($socialPostMetricDetail['fbLike'])) {
				$socialPostMetric['fbLike'] = $socialPostMetricDetail['fbLike'];
			}
			
			if(isset($socialPostMetricDetail['fbShare'])) {
				$socialPostMetric['fbShare'] = $socialPostMetricDetail['fbShare'];
			}
			
			if(isset($socialPostMetricDetail['fbComment'])) {
				$socialPostMetric['fbComment'] = $socialPostMetricDetail['fbComment'];
			}
			
			unset($socialPostMetric['creationDate']);
			unset($socialPostMetric['accountId']);
			unset($socialPostMetric['socialPostId']);
			$this->editSocialPostMetric($socialPostMetric);
		}
	}
	
	/**
	 * This function return all todays social metric for all or specific account
	 * @param string $accountId
	 * @param string $startDate
	 */
	public function getSocialMetricList($accountId = '', $startDate = '', $isMap = true) {
		$em = $this->getDoctrine()->getManager();
		
		//$from for entity name (table name)
		$from = "DB\\Bundle\\AppBundle\Entity\\SocialPostMetric socialPostMetric";
		
		if(empty($startDate)) {
			$startDate = date('Y-m-d');
		}
		
		$whereCondition = " socialPostMetric.creationDate = '" . $startDate . "' ";
		
		if(!empty($accountId)) {
			$whereCondition .= 'AND socialPostMetric.accountId = ' . $accountId;
		}
		
		$sql = "SELECT socialPostMetric.socialPostMetricId, socialPostMetric.accountId, socialPostMetric.socialPostId, socialPostMetric.social, socialPostMetric.socialIncrease, socialPostMetric.fbSocial, " .
				"socialPostMetric.fbLike, socialPostMetric.fbShare, socialPostMetric.fbComment, socialPostMetric.twLike, socialPostMetric.creationDate " .
				"FROM " . $from;
		
		if(!empty($whereCondition)) {
			$sql .= " WHERE " . $whereCondition . " ";
		}
		
		$sql .= "ORDER BY socialPostMetric.socialPostMetricId DESC ";
		
		//echo $sql;
		
		$query = $em->createQuery($sql);
		$result = $query->getResult();
		
		if($isMap == true && !empty($result)) {
			$map = array();
			foreach($result as $socialPostMetricDetail) {
				if(!isset($map[$socialPostMetricDetail['accountId']])) {
					$map[$socialPostMetricDetail['accountId']] = array();
				}
				$map[$socialPostMetricDetail['accountId']][] = $socialPostMetricDetail;
			}
			
			return $map;
		}
		
		return $result;
	}
	
	/**
	 * This function return report for account
	 * @param string $accountId
	 */
	public function getReports($accountId = '') {
		$socialPostMetricMap = $this->getSocialMetricList($accountId, '', true);
		
		/* 
		 $noOfDay= 5;
		 if(!empty($accountId) && empty($socialPostMetricMap[$accountId])) {
			for($index = 1; $index <= $noOfDay; $index) { 
				$startDate = date('Y-m-d', strtotime('-' . $index . ' day', strtotime(date('Y-m-d H:i:s'))));
				$socialPostMetricMap = $this->getSocialMetricList($accountId, $startDate, true);
				if(!empty($socialPostMetricMap[$accountId])) {
					break;
				}
			}
		} */
		
		$reportList = array();
		if(!empty($socialPostMetricMap)) {
			foreach($socialPostMetricMap as $accountId=>$socialPostMetricList) {
				if(empty($socialPostMetricList)) {
					continue;
				}
				$reportList[$accountId] = array();
				
				$reportList[$accountId]['increaseValue'] = 0;
				for($index = 0; $index < count($socialPostMetricList); $index++) {
					$socialPostMetric = $socialPostMetricList[$index];
					if($socialPostMetric['socialIncrease'] == 0) {
						$socialPostMetric['increaseRatio'] = $socialPostMetric['social'];
					} else {
						$socialPostMetric['increaseRatio'] = (($socialPostMetric['social'] - $socialPostMetric['socialIncrease'])/ $socialPostMetric['socialIncrease']) * 100;
					}
					
					$reportList[$accountId]['increaseValue'] += $socialPostMetric['increaseRatio']; 
				}
				
				$reportList[$accountId]['postCount'] = count($socialPostMetricList);
				$reportList[$accountId]['increaseValue'] = $reportList[$accountId]['increaseValue'] / count($socialPostMetricList);
			}
		} 
		
		return $reportList;
	}
}
?>