<?php

namespace DB\Bundle\AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use DB\Bundle\AppBundle\DAO\TrendingArticleDAO;
use DB\Bundle\AppBundle\DAO\SocialProfileDAO;
use DB\Bundle\AppBundle\Entity\SocialProfile;
use DB\Bundle\AppBundle\Entity\TrendingArticle;
use DB\Bundle\AppBundle\DAO\SocialPostDAO;
use DB\Bundle\AppBundle\Entity\SocialPost;
use DB\Bundle\AppBundle\DAO\ArticleNotifyHistoryDAO;
use DB\Bundle\AppBundle\Entity\ArticleNotifyHistory;
use DB\Bundle\AppBundle\DAO\UserDAO;
use DB\Bundle\AppBundle\Entity\User;
use DB\Bundle\AppBundle\DAO\TrendingArticleCategoryDAO;
use DB\Bundle\CommonBundle\Util\DBUtil;

class PostController extends DbAppController {
	/**
	 * @Route("/new-post", name="db_postreach_new_post")
	 * @Template("DBAppBundle:postr:new-post.html.twig")
	 */
	public function newPostAction() {
		$isValid = $this->isValidUserRequest();
		if(!empty($isValid['nextRoute'])) {
			return $this->sendRequest($isValid['nextRoute']);
		}
		
		$currentUser = $this->getUser();
		
		$socialProfileDAO = new SocialProfileDAO($this->getDoctrine());
		$fbSocialProfileDetail = $socialProfileDAO->findSingleDetailBy(new SocialProfile(), array('profileType'=>'Facebook', 'accountId'=>$currentUser['accountId']));
		$this->addInResponse('fbSocialProfileDetail', $fbSocialProfileDetail);
		
		$twSocialProfileDetail = $socialProfileDAO->findSingleDetailBy(new SocialProfile(), array('profileType'=>'Twitter', 'accountId'=>$currentUser['accountId']));
		$this->addInResponse('twSocialProfileDetail', $twSocialProfileDetail);
		
		return $this->getResponse();
	}

	/**
	 * @Route("/get-post", name="db_postreach_get_post")
	 */
	public function getAction() {
		$isValid = $this->isValidUserRequest();
		if(!empty($isValid['nextRoute'])) {
			return $this->getJSONSessionExpire($isValid['nextRoute']);
		}
		
		$currentUser = $this->getUser();
		$currentPage = $this->getRequestParam('currentPage', '1');
		
		$trendingArticleDAO = new TrendingArticleDAO($this->getDoctrine());
		$trendingArticleList = $trendingArticleDAO->getTrendingArticleListByAccount($currentUser['accountId'], $currentPage, '');
		
		$this->addInResponse('trendingArticleList', $trendingArticleList);
	
		return $this->getJsonResponse($this->getResponse());
	}

	/**
	 * @Route("/hide-post", name="db_postreach_hide_post")
	 */
	public function hidePostAction() {
		$isValid = $this->isValidUserRequest();
		if(!empty($isValid['nextRoute'])) {
			return $this->getJSONSessionExpire($isValid['nextRoute']);
		}
		
		$currentUser = $this->getUser();
		$trendingArticleDetail = $this->processContent();
		
		$status = false;
		if(!empty($trendingArticleDetail['trendingArticleId'])) {
			$articleNotifyHistory = array();
			$articleNotifyHistory['accountId'] = $currentUser['accountId'];
			$articleNotifyHistory['trendingArticleId'] = $trendingArticleDetail['trendingArticleId'];
			$articleNotifyHistory['notifyType'] = ArticleNotifyHistory::NOTIFY_TYPE_SUGGESTED;

			$articleNotifyHistoryDAO = new ArticleNotifyHistoryDAO($this->getDoctrine());
			$articleNotifyHistory = $articleNotifyHistoryDAO->addArticleNotifyHistory($articleNotifyHistory);
			
			$status = true;
		}
	
		$this->addInResponse('status', $status);
		
		return $this->getJsonResponse($this->getResponse());
	}

	/**
	 * @Route("/share-post", name="db_postreach_share_post")
	 */
	public function sharePostAction() {
		$isValid = $this->isValidUserRequest();
		if(!empty($isValid['nextRoute'])) {
			return $this->getJSONSessionExpire($isValid['nextRoute']);
		}
		
		$currentUser = $this->getUser();
		
		$articleForm = $this->processContent();
		
		$response = array();
		$socialPostDAO = new SocialPostDAO($this->getDoctrine());
		
		$criteria = array();
		if(!empty($articleForm['trendingArticleId'])) {
			$criteria['validSocial'] = array();
			
			if(!empty($articleForm['fbProfile'])) {
				$record	= array();
				$record['trendingArticleId'] = $articleForm['trendingArticleId'];
				$record['profileType'] = 'Facebook';
				
				$criteria['validSocial'][] = $record;
			}
			
			
			if(!empty($articleForm['twProfile'])) {
				$record	= array();
				$record['trendingArticleId'] = $articleForm['trendingArticleId'];
				$record['profileType'] = 'Twitter';
				
				$criteria['validSocial'][] = $record;
			}
			
			$trendingArticleDAO = new TrendingArticleDAO($this->getDoctrine());
			$trendingArticleDetail = $trendingArticleDAO->findSingleDetailBy(new TrendingArticle(), array('trendingArticleId'=>$articleForm['trendingArticleId']));
			
			if(!empty($trendingArticleDetail)) {
				$socialPostDetail = array();
				$socialPostDetail['accountId'] = $currentUser['accountId'];
				
				//Send message to facebook
				$socialPostDetail['message'] = '';
				if(!empty($articleForm['caption'])) {
					$socialPostDetail['message'] = $articleForm['caption'];
				}
				$socialPostDetail['link'] = '';
				if(!empty($trendingArticleDetail['url'])) {
					$socialPostDetail['link'] = $trendingArticleDetail['url'];
				}
				
				$socialPostDetail['trendingArticleId'] = '';
				if(!empty($trendingArticleDetail['trendingArticleId'])) {
					$socialPostDetail['trendingArticleId'] = $trendingArticleDetail['trendingArticleId'];
					$response['trendingArticleId'] = $trendingArticleDetail['trendingArticleId'];
				}
				
				if(isset($socialPostDetail['message']) && !empty($socialPostDetail['link'])) {
					$existingSocialPostDetail = $socialPostDAO->findSingleDetailBy(new SocialPost(), array("message"=>$socialPostDetail['message'], "link"=>$socialPostDetail['link'], 'accountId'=>$socialPostDetail['accountId']));
					if(empty($existingSocialPostDetail)) {
						$socialPostDAO->addSocialPost($socialPostDetail);
						$response['message'] = "Your Post is successfully post";
					} else {
						$response['error'] = "Post is already posted on your wall";
					}
				}
			}
		}
		
		$socialPostresponse = $socialPostDAO->postSocialmessage($currentUser['accountId'], $criteria);
		$response['socialPostresponse'] = $socialPostresponse;
		
		$this->addInResponse('response', $response);
		return $this->getJsonResponse($this->getResponse());
	}

	/**
	 * @Route("/post/{accountId}/{uniqueKey}", name="db_postreach_post")
	 * @Template("DBAppBundle:postr:post-message.html.twig")
	 */
	public function postAction($accountId='', $uniqueKey = '') {
		if(empty($accountId) || empty($uniqueKey)) {
			$this->addInResponse('error', 'Invalid post action.');
			return $this->getResponse();
		}
		
		$social = $this->getRequestParam('s', '');
		
		$userDAO = new UserDAO($this->getDoctrine());
		$userDetail = $userDAO->findSingleDetailBy(new User(), array('accountId'=>$accountId));
		if(!empty($userDetail)) {
			unset($userDetail['password']);
			$this->addInResponse('userDetail', $userDetail);
		}
		
		//Get article detail
		$trendingArticleCategoryDAO = new TrendingArticleCategoryDAO($this->getDoctrine());
		$trendingArticleCategoryDetail = $trendingArticleCategoryDAO->findSingleDetailBy(new TrendingArticle(), array('postId'=>$uniqueKey));
		
		$criteria = array();
		if(!empty($trendingArticleCategoryDetail)) {
			$criteria['validSocial'] = array();
				
			if(!empty($social) && $social == 'f') {
				$record	= array();
				$record['trendingArticleId'] = $trendingArticleCategoryDetail['trendingArticleId'];
				$record['profileType'] = 'Facebook';
			
				$criteria['validSocial'][] = $record;
			}
				
				
			if(!empty($social) && $social == 't') {
				$record	= array();
				$record['trendingArticleId'] = $trendingArticleCategoryDetail['trendingArticleId'];
				$record['profileType'] = 'Twitter';
			
				$criteria['validSocial'][] = $record;
			}
			
			$trendingArticleCategoryDetail['domain'] = DBUtil::getDomain($trendingArticleCategoryDetail['url']);
			$this->addInResponse('trendingArticleCategoryDetail', $trendingArticleCategoryDetail);
			
			//Check article is already post
			$socialPostDAO = new SocialPostDAO($this->getDoctrine());
			$socialPostDetail = $socialPostDAO->findSingleDetailBy(new SocialPost(), array('accountId'=>$accountId, 'trendingArticleId'=>$trendingArticleCategoryDetail['trendingArticleId']));
			if(!empty($socialPostDetail)) {
				if(!empty($social) && ($social == 'f' || $social == 't') && (empty($socialPostDetail['facebookPostId']) || empty($socialPostDetail['twitterPostId']))) {
					$criteria['socialPost'] = $socialPostDetail;
				} else {
					$this->addInResponse('error', 'Article is already posted on your wall');
					$this->addInResponse('socialPostDetail', $socialPostDetail);
					return $this->getResponse();
				}
			}
			
			//Post article to social feed
			$socialPostDetail = array();
			$socialPostDetail['accountId'] = $accountId;
			
			//Send message to facebook
			$socialPostDetail['message'] = '';
			if(!empty($trendingArticleCategoryDetail['caption'])) {
				$socialPostDetail['message'] = $trendingArticleCategoryDetail['caption'];
			}
			$socialPostDetail['link'] = '';
			if(!empty($trendingArticleCategoryDetail['url'])) {
				$socialPostDetail['link'] = $trendingArticleCategoryDetail['url'];
			}
			
			$socialPostDetail['trendingArticleId'] = '';
			if(!empty($trendingArticleCategoryDetail['trendingArticleId'])) {
				$socialPostDetail['trendingArticleId'] = $trendingArticleCategoryDetail['trendingArticleId'];
			}
			
			if(!empty($socialPostDetail['message']) || !empty($socialPostDetail['link'])) {
				$existingSocialPostDetail = $socialPostDAO->findSingleDetailBy(new SocialPost(), array("message"=>$socialPostDetail['message'], "link"=>$socialPostDetail['link']));
				if(empty($existingSocialPostDetail)) {
					$socialPostDAO->addSocialPost($socialPostDetail);
				} else {
					
				}
			}
			
			$socialPostDAO->postSocialmessage($accountId, $criteria);
			
			//Get the social detail and add URL to to got wall
			$socialPostDetail = $socialPostDAO->findSingleDetailBy(new SocialPost(), array('accountId'=>$accountId, 'trendingArticleId'=>$trendingArticleCategoryDetail['trendingArticleId']));
			if(!empty($socialPostDetail)) {
				$this->addInResponse('socialPostDetail', $socialPostDetail);
			}
			
			$this->addInResponse('message', 'The article has been posted to your wall.');
		}
		
		return $this->getResponse();
	}
}
?>