<?php

namespace DB\Bundle\AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use DB\Bundle\AppBundle\DAO\SocialPostDAO;

class DashboardController extends DbAppController {
	/**
	 * @Route("/dashboard", name="db_postreach_dashboard")
	 * @Template("DBAppBundle:postr:dashboard.html.twig")
	 */
	public function dashboardAction() {
		$isValid = $this->isValidUserRequest();
		if(!empty($isValid['nextRoute'])) {
			return $this->sendRequest($isValid['nextRoute']);
		}
		
		return $this->getResponse();
	}
	
	/**
	 * @Route("/get-social-post", name="db_postreach_get_social_post")
	 */
	public function getSocialPostAction() {
		$isValid = $this->isValidUserRequest();
		if(!empty($isValid['nextRoute'])) {
			return $this->getJSONSessionExpire($isValid['nextRoute']);
		}
		
		$currentUser = $this->getUser();
		$currentPage = $this->getRequestParam('currentPage', '1');
		
		$socialPostDAO = new SocialPostDAO($this->getDoctrine());
		$socialPostList = $socialPostDAO->getSocialPostList($currentUser['accountId'], $currentPage);
		
		$this->addInResponse('socialPostList', $socialPostList);
		
		return $this->getJsonResponse($this->getResponse());
	}
	
	/**
	 * @Route("/get-summary", name="db_get_summary")
	 */
	public function getSummaryAction() {
		$isValid = $this->isValidUserRequest();
		if(!empty($isValid['nextRoute'])) {
			return $this->getJSONSessionExpire($isValid['nextRoute']);
		}
		
		$summary = $this->getSession('postreach-dashbord-summary');
		if(empty($summary)) {
			$summary = array();
			$summary['lastUpdate'] = strtotime('-1 day', strtotime(date('Y-m-d H:i:s')));
		}
		
		$summary['dif'] = round(abs(strtotime(date('Y-m-d H:i:s')) - $summary['lastUpdate']) / 60,2);
		if($summary['dif'] > 15) {
			$currentUser = $this->getUser();
			$socialPostDAO = new SocialPostDAO($this->getDoctrine());
			$socialPostSummaryDetail = $socialPostDAO->getTotalSocialPost($currentUser['accountId']);
			
			$date = date('Y-m-d H:i:s', strtotime(date('Y-m-d 00:00:01')));
			$TodaysSocialPostSummaryDetail = $socialPostDAO->getTotalSocialPost($currentUser['accountId'], $date);
			
			if(empty($socialPostSummaryDetail['totalPost'])) {
				$socialPostSummaryDetail['totalPost'] = '0';
			}
			if(empty($socialPostSummaryDetail['totalSocial'])) {
				$socialPostSummaryDetail['totalSocial'] = '0';
			}
			
			if(empty($TodaysSocialPostSummaryDetail['totalPost'])) {
				$TodaysSocialPostSummaryDetail['totalPost'] = '0';
			}
			if(empty($TodaysSocialPostSummaryDetail['totalSocial'])) {
				$TodaysSocialPostSummaryDetail['totalSocial'] = '0';
			}
			
			$summary['post'] = $socialPostSummaryDetail['totalPost'];
			$summary['postIn'] = $TodaysSocialPostSummaryDetail['totalPost'];
			
			$summary['reach'] = $socialPostSummaryDetail['totalSocial'];
			$summary['reachIn'] = $TodaysSocialPostSummaryDetail['totalSocial'];
			
			$summary['share'] = '0';
			$summary['shareIn'] = '0';
			
			$summary['visit'] = '0';
			$summary['visitIn'] = '0';
			
			$summary['lastUpdate'] = strtotime(date('Y-m-d H:i:s'));
		}
		
		$this->setSession('postreach-dashbord-summary', $summary);
		$this->addInResponse('summary', $summary);
	
		return $this->getJsonResponse($this->getResponse());
	}
}
?>