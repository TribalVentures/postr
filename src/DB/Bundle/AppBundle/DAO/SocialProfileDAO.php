<?php
namespace DB\Bundle\AppBundle\DAO;

use DB\Bundle\CommonBundle\Base\BaseDAO;
use DB\Bundle\AppBundle\Entity\SocialProfile;

/**
 * Class For SocialProfile DAO, This class is responsible for manage database 
 * operation for SocialProfile table/entity
 *
 * @namespace DB\Bundle\AppBundle\DAO
 *
 * @author Dipak Patil
 */
class SocialProfileDAO extends BaseDAO { 
	/**
	 * Always need doctrim object to initilise SocialProfile dao object
	 * @param $_dm - Doctrime object
	 */
	function __construct($_dm) {
		parent :: __construct($_dm);
	}
	
	/**
	 * This function add new Social Profile
	 * @param array $socialProfileDetail
	 */
	public function addSocialProfile($socialProfileDetail = array()) {
		if(empty($socialProfileDetail) || empty($socialProfileDetail['profileType'])) {
			return false;
		}
		
		if($socialProfileDetail['profileType'] == 'Facebook') {
			$socialProfileDetail['oauthToken'] = '';
			$socialProfileDetail['oauthTokenSecret'] = '';
			$socialProfileDetail['oauthToken'] = '';
			
		} else if($socialProfileDetail['profileType'] == 'Twitter') {
			$socialProfileDetail['accessToken'] = '';
		}
		
		if(empty($socialProfileDetail['creationDate'])) {
			$socialProfileDetail['creationDate'] = new \DateTime();
		}
		
		$socialProfile = new SocialProfile();
		
		$socialProfile->setAccountId($socialProfileDetail['accountId']);
		$socialProfile->setProfileType($socialProfileDetail['profileType']);
		
		$socialProfile->setSocialId($socialProfileDetail['socialId']);
		
		$socialProfile->setAccessToken($socialProfileDetail['accessToken']);
		$socialProfile->setOauthToken($socialProfileDetail['oauthToken']);
		$socialProfile->setOauthTokenSecret($socialProfileDetail['oauthTokenSecret']);
		
		$socialProfile->setName($socialProfileDetail['name']);
		$socialProfile->setPicture($socialProfileDetail['picture']);
		$socialProfile->setCategory($socialProfileDetail['category']);
		
		$socialProfile->setCreationDate($socialProfileDetail['creationDate']);
		
		$socialProfile = $this->save($socialProfile);
		
		$newDetail = false;
		if(is_object($socialProfile)) {
			$newDetail = $socialProfile->toArray();
		}
		return $newDetail;
	}
	
	/**
	 * This function manage social profile 
	 * @param array $socialProfileDetail
	 */
	public function manageSocialProfile($socialProfileDetail) {
		if(empty($socialProfileDetail) || empty($socialProfileDetail['profileType'])) {
			return false;
		}
		
		if($socialProfileDetail['profileType'] == SocialProfile::PROFILE_TYPE_FACEBOOK) {
			//Delete existing facebook profile
			$this->deleteBy(new SocialProfile(), array('accountId'=>$socialProfileDetail['accountId'], 'profileType'=>SocialProfile::PROFILE_TYPE_FACEBOOK));
		} else if($socialProfileDetail['profileType'] == SocialProfile::PROFILE_TYPE_TWITTER) {
			//Delete existig twitter profile
			$this->deleteBy(new SocialProfile(), array('accountId'=>$socialProfileDetail['accountId'], 'profileType'=>SocialProfile::PROFILE_TYPE_TWITTER));
		}
		
		return $this->addSocialProfile($socialProfileDetail);
	}
}
?>