<?php

namespace DB\Bundle\AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use DB\Bundle\AppBundle\DAO\CategoryDAO;
use DB\Bundle\AppBundle\DAO\AccountCategoryDAO;
use DB\Bundle\AppBundle\DAO\AccountDAO;
use DB\Bundle\AppBundle\DAO\AccountFrequencyDAO;
use DB\Bundle\AppBundle\DAO\SocialProfileDAO;
use DB\Bundle\CommonBundle\Util\DBUtil;
use DB\Bundle\AppBundle\Common\Config;
use DB\Bundle\AppBundle\Entity\SocialProfile;
use DB\Bundle\AppBundle\DAO\UserDAO;

class PostPreferenceController extends DbAppController {
	/**
	 * @Route("/pf/categories", name="db_postreach_post_preference_category")
	 * @Template("DBAppBundle:postr:category.html.twig")
	 */
	public function categoryAction() {
		$isValid = $this->isValidUserRequest();
		if(!empty($isValid['nextRoute'])) {
			return $this->sendRequest($isValid['nextRoute']);
		}
		
		$currentUser = $this->getUser();
		
		if($this->getRequest()->isMethod('POST')) {
			$categoryForm = $this->getRequestParam('categoryForm', array());
				
			//Assign category to account
			if(!empty($categoryForm['categoryId'])) {
				$accountCategoryDAO = new AccountCategoryDAO($this->getDoctrine());
				$accountCategoryDAO->addCategoryList($currentUser['accountId'], $categoryForm['categoryId']);
				
				$this->setSession('status', '1');
				return $this->sendRequest('db_postreach_post_preference_category');
			} else {
				$this->addInResponse('error', 'Category shold not blank, Please select atleast one category');
			}
		}
		
		//Get existing selected categories
		$accountCategoryDAO = new AccountCategoryDAO($this->getDoctrine());
		$existingCategoryList = $accountCategoryDAO->getAccountCategoryList($currentUser['accountId']);
		
		$categoryDAO = new CategoryDAO($this->getDoctrine());
		$categoryList = $categoryDAO->getAllDefaultCategoryList();
		if(!empty($existingCategoryList) && !empty($categoryList)) {
			for($index = 0; $index < count($categoryList); $index ++) {
				for($jIndex = 0; $jIndex < count($existingCategoryList); $jIndex ++) {
					if($categoryList[$index]['categoryId'] == $existingCategoryList[$jIndex]['categoryId']) {
						$categoryList[$index]['selected'] = true;
					}
				}
			}
		}
		
		$this->addInResponse('categoryList', $categoryList);
		
		$status = $this->getSession('status');
		$this->removeSession('status');
		if($status == '1') {
			$this->addInResponse('message', 'Category updated successfully');
		}
		
		$this->addInResponse('CDN_URL', Config::getSParameter('CDN_URL'));
		
		return $this->getResponse();
	}

	/**
	 * @Route("/pf/frequency-settings", name="db_postreach_post_preference_frequency_settings")
	 * @Template("DBAppBundle:postr:frequency-settings.html.twig")
	 */
	public function frequencySettingsAction() {
		$isValid = $this->isValidUserRequest();
		if(!empty($isValid['nextRoute'])) {
			return $this->sendRequest($isValid['nextRoute']);
		}
		
		$currentUser = $this->getUser();
		$accountFrequencyDAO = new AccountFrequencyDAO($this->getDoctrine());
		
		if($this->getRequest()->isMethod('POST')) {
			$accountFrequencyForm = $this->getRequestParam('accountFrequencyForm', array());
				
			//Make frequency string
			$accountFrequencyForm = $accountFrequencyDAO->getFrequencyDetail($accountFrequencyForm);
			$accountFrequencyForm['accountId'] = $currentUser['accountId'];
				
			$accountFrequencyDAO->manageAccountFrequency($accountFrequencyForm);
				
			//Update timezone in for user
			$accountFrequencyForm['userId'] = $currentUser['userId'];
				
			$userDAO = new UserDAO($this->getDoctrine());
			$userDAO->manageUserTimezone($accountFrequencyForm);
			
			$this->setSession('status', '1');
			return $this->sendRequest('db_postreach_post_preference_frequency_settings');
		}
		
		$accountFrequencyDetail = $accountFrequencyDAO->getAccountFrequencyDetail($currentUser['accountId']);
		if(!empty($accountFrequencyDetail)) {
			$accountFrequencyDetail = $accountFrequencyDAO->setFrequencyDetail($accountFrequencyDetail);
				
			$this->addInResponse('accountFrequencyDetail', $accountFrequencyDetail);
		}
		
		$timezoneList = Config::getSupportedTimezoneList(); //DBUtil::getTimeZoneList();
		$this->addInResponse('timezoneList', $timezoneList);
		
		$status = $this->getSession('status');
		$this->removeSession('status');
		if($status == '1') {
			$this->addInResponse('message', 'Frequency and setting save successfuly');
		}
		
		return $this->getResponse();
	}
	
	/**
	 * @Route("/pf/social-networks", name="db_postreach_post_preference_social_networks")
	 * @Template("DBAppBundle:postr:social-networks.html.twig")
	 */
	public function socialNetworksAction() {
		$isValid = $this->isValidUserRequest();
		if(!empty($isValid['nextRoute'])) {
			return $this->sendRequest($isValid['nextRoute']);
		}
		
		$currentUser = $this->getUser();
		$socialProfileDAO = new SocialProfileDAO($this->getDoctrine());

		//Handle slection of social profile
		if($this->getRequest()->isMethod('POST')) {
			$socialProfileForm = $this->getRequestParam('socialProfileForm', array());
			
			$isUpdate = false;
			//Update facebook social profile
			if(!empty($socialProfileForm['facebook'])) {
				$pageList = $this->getSession('pageList');
				if(!empty($pageList)) {
					foreach($pageList as $pageDetail) {
						if($pageDetail['socialId'] == $socialProfileForm['facebook']) {
							$pageDetail['accountId'] = $currentUser['accountId'];
							$socialProfileDAO->manageSocialProfile($pageDetail);
							$isUpdate = true;
							break;
						}
					}
				}
			}
				
			//Update twitter profile
			if(!empty($socialProfileForm['twitter'])) {
				$twPageList = $this->getSession('twPageList');
				if(!empty($twPageList)) {
					foreach($twPageList as $pageDetail) {
						if($pageDetail['socialId'] == $socialProfileForm['twitter']) {
							$pageDetail['accountId'] = $currentUser['accountId'];
							$socialProfileDAO->manageSocialProfile($pageDetail);
							$isUpdate = true;
							break;
						}
					}
				}
			}
				
			if($isUpdate == true) {
				//Remove facebook and twitter profilelist from session
				$this->removeSession('pageList');
				$this->removeSession('twPageList');
				
				$this->setSession('status', '1');
				//Redirect to next page
				return $this->sendRequest('db_postreach_post_preference_social_networks');
			}
				
			//$this->addInResponse('error', 'Please select at least one social profile.');
		}

		$disconnect = $this->getRequestParam('disconnect', '');
		if('fbdisconnect' == $disconnect) {
			//Handle facebook disconnect
			$this->removeSession('pageList');
				
			//also remove from DB
			//$socialProfileDAO->deleteBy(new SocialProfile(), array('profileType'=>'Facebook', 'accountId'=>$currentUser['accountId']));
			
			$twSocialProfileDetail = $socialProfileDAO->findSingleDetailBy(new SocialProfile(), array('profileType'=>'Twitter', 'accountId'=>$currentUser['accountId']));
			$this->addInResponse('twSocialProfileDetail', $twSocialProfileDetail);
		} else if('twdisconnect' == $disconnect) {
			//Handle twitter disconnect
			$this->removeSession('twPageList');
				
			//also remove from DB
			//$socialProfileDAO->deleteBy(new SocialProfile(), array('profileType'=>'Twitter', 'accountId'=>$currentUser['accountId']));
		
			$fbSocialProfileDetail = $socialProfileDAO->findSingleDetailBy(new SocialProfile(), array('profileType'=>'Facebook', 'accountId'=>$currentUser['accountId']));
			$this->addInResponse('fbSocialProfileDetail', $fbSocialProfileDetail);
		} else if('fbcalcel' == $disconnect) {
			//Handle facebook disconnect
			$this->removeSession('pageList');
			return $this->sendRequest('db_postreach_post_preference_social_networks');
		} else if('twcancel' == $disconnect) {
			//Handle twitter disconnect
			$this->removeSession('twPageList');
			return $this->sendRequest('db_postreach_post_preference_social_networks');
		} else if('fbdisconnectsocial' == $disconnect) {
			//Handle twitter disconnect
			$socialProfileDAO->deleteBy(new SocialProfile(), array('profileType'=>'Facebook', 'accountId'=>$currentUser['accountId']));
			return $this->sendRequest('db_postreach_post_preference_social_networks');
		} else if('twdisconnectsocial' == $disconnect) {
			//Handle twitter disconnect
			$socialProfileDAO->deleteBy(new SocialProfile(), array('profileType'=>'Twitter', 'accountId'=>$currentUser['accountId']));
			return $this->sendRequest('db_postreach_post_preference_social_networks');
		}
		
		$fbSocialProfileDetail = $socialProfileDAO->findSingleDetailBy(new SocialProfile(), array('profileType'=>'Facebook', 'accountId'=>$currentUser['accountId']));
		$this->addInResponse('fbSocialProfileDetail', $fbSocialProfileDetail);
			
		$twSocialProfileDetail = $socialProfileDAO->findSingleDetailBy(new SocialProfile(), array('profileType'=>'Twitter', 'accountId'=>$currentUser['accountId']));
		$this->addInResponse('twSocialProfileDetail', $twSocialProfileDetail);
		
		//Handle facebook callback action here
		$facebookDetail = $this->getSession('facebookDetail');
		$this->removeSession('facebookDetail');
		if(!empty($facebookDetail)) {
			//Check for error
			if(!empty($facebookDetail['error'])) {
				$this->addInResponse('error', 'Problem while process your facebook request');
			}
				
			//Get pages list
			if(!empty($facebookDetail['pageList'])) {
				$this->setSession('pageList', $facebookDetail['pageList']);
			}
		}
		
		//Hanlde twitter callback action
		$twitterProfileDetail = $this->getSession('twitterProfileDetail');
		$this->removeSession('twitterProfileDetail');
		if(!empty($twitterProfileDetail)) {
			//Check for error
			if(!empty($twitterProfileDetail['error'])) {
				$this->addInResponse('error', 'Problem while process your twitter request');
			}
		
			//Get pages list
			if(!empty($twitterProfileDetail['twPageList'])) {
				$this->setSession('twPageList', $twitterProfileDetail['twPageList']);
			}
		}
		
		$pageList = $this->getSession('pageList');
		if(!empty($pageList)) {
			$this->addInResponse('pageList', $pageList);
		} else {
			if (session_status() == PHP_SESSION_NONE) {
				session_start();
			}
			//Generate facebook and Twitter login URls
			$this->addInResponse('facebookURL', $this->getFacebookLoginURL('db_postreach_handle_profile', 'action=preferences'));
		}
		
		$twPageList = $this->getSession('twPageList');
		if(!empty($twPageList)) {
			$this->addInResponse('twPageList', $twPageList);
		}
		
		$status = $this->getSession('status');
		$this->removeSession('status');
		if($status == '1') {
			$this->addInResponse('message', 'Social profile save successfuly');
		}
		
		return $this->getResponse();
	}
}
?>