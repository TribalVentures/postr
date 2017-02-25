<?php

namespace DB\Bundle\AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use DB\Bundle\AppBundle\DAO\TrendingArticleDAO;
use DB\Bundle\AppBundle\Entity\TrendingArticle;
use DB\Bundle\AppBundle\DAO\CategoryDAO;
use DB\Bundle\AppBundle\DAO\TrendingArticleCategoryDAO;
use DB\Bundle\AppBundle\Entity\TrendingArticleCategory;
use DB\Bundle\AppBundle\DAO\UserDAO;
use DB\Bundle\AppBundle\Entity\User;
use DB\Bundle\AppBundle\Common\Config;

class TrendingArticleController extends DbAppController {
	/**
	 * @Route("/admin/trending-article", name="db_postreach_trending_article")
	 * @Template("DBAppBundle:postrAdmin:trending-article.html.twig")
	 */
	public function profileAction() {
		if(!$this->isAdminSessionExpire()) {
			return $this->sendRequest('db_postreach_admin');
		}
		
		$categoryDAO = new CategoryDAO($this->getDoctrine());
		
		$userTypeList = $categoryDAO->getUserType(0, 1);
		$this->addInResponse('userTypeList', $userTypeList);
		
		$this->addInResponse('defaultUserType', Config::getSParameter('POSTR_DEFAULT_USER_TYPE'));
		
		return $this->getResponse();
	}
	
	
	/**
	 * @Route("/admin/get-trending-article", name="db_postreach_get_trending_article")
	 */
	public function getTrendingArticleAction() {
		if(!$this->isAdminSessionExpire()) {
			return $this->getJSONSessionExpire('db_postreach_admin');
		}

		$currentPage = $this->getRequestParam('currentPage', 1);
		
		$searchCriteria = array();
		$searchCriteria['categoryId'] 	= $this->getRequestParam('categoryId', '');
		$searchCriteria['type'] 		= $this->getRequestParam('type', '');
		$searchCriteria['fromDate'] 	= $this->getRequestParam('fromDate', '');
		$searchCriteria['toDate'] 		= $this->getRequestParam('toDate', '');
		$searchCriteria['approveStatus'] = $this->getRequestParam('approveStatus', '');
		
		$trendingArticleDAO = new TrendingArticleDAO($this->getDoctrine());
		$trendingArticleList = $trendingArticleDAO->getTrendingArticleListByCategoryId($searchCriteria, $currentPage, 0);
		
		$this->addInResponse('trendingArticleList', $trendingArticleList);
	
		return $this->getJsonResponse($this->getResponse());
	}
	
	/**
	 * @Route("/admin/remove-category-trending-article", name="db_postreach_remove_category_trending_article")
	 */
	public function removeCategoryTrendingArticleAction() {
		if(!$this->isAdminSessionExpire()) {
			return $this->getJSONSessionExpire('db_postreach_admin');
		}
		
		$trendinigArticleCategoryDetail = $this->processContent();
		
		$response = array('status'=>'false', 'deletedIdsCategoryList'=>array());
		if(!empty($trendinigArticleCategoryDetail['categoryId']) && !empty($trendinigArticleCategoryDetail['trendingArticleId'])) {
			$trendingArticleId = $trendinigArticleCategoryDetail['trendingArticleId'];
			$categoryId = $trendinigArticleCategoryDetail['categoryId'];
			
			$trendinigArticleCategoryDAO = new TrendingArticleCategoryDAO($this->getDoctrine());
			$result = $trendinigArticleCategoryDAO->deleteBy(new TrendingArticleCategory(), array('trendingArticleId' =>$trendingArticleId , 'categoryId' =>$categoryId));
			
			$response['deletedIdsCategoryList'][] = array('trendingArticleId' =>$trendingArticleId , 'categoryId' =>$categoryId);
			$response['status'] = 'true';
		} else {
			$response['status'] = 'false';
		}
		
		$this->addInResponse('response', $response);
		
		return $this->getJsonResponse($this->getResponse());
	}
	
	/**
	 * @Route("/admin/delete-trending-article", name="db_postreach_delete_trending_article")
	 */
	public function deleteTrendingArticleAction() {
		if(!$this->isAdminSessionExpire()) {
			return $this->getJSONSessionExpire('db_postreach_admin');
		}
		
		$trendinigArticleList = $this->processContent();
		$response = array('status'=>false, 'trendinigArticleList'=>array());
		if(!empty($trendinigArticleList)) {
			$trendingArticleDAO = new TrendingArticleDAO($this->getDoctrine());
			foreach($trendinigArticleList as $trendinigArticleDetail) {
				$trendingArticleDAO->deleteBy(new TrendingArticle(), array('trendingArticleId' => $trendinigArticleDetail['trendingArticleId']));
				$response['trendinigArticleList'][] = $trendinigArticleDetail['trendingArticleId'];
			}
			$response['status']= true;
		}
		
		$this->addInResponse('response', $response);
		
		return $this->getJsonResponse($this->getResponse());
	}
	
	/**
	 * @Route("/admin/manage-trending-article-status", name="db_postreach_manage_trending_article_status")
	 */
	public function manageTrendingArticleStatusAction() {
		if(!$this->isAdminSessionExpire()) {
			return $this->getJSONSessionExpire('db_postreach_admin');
		}
		
		$trendinigArticleDetailList = $this->processContent();
		
		$response = array('status'=>false, 'articleDetailList'=>array());
		if(!empty($trendinigArticleDetailList)) {
			$trendingArticleDAO = new TrendingArticleDAO($this->getDoctrine());
			for($index = 0; $index < count($trendinigArticleDetailList); $index ++) {
				$response['articleDetailList'][] = $trendingArticleDAO->manageArticleStatus($trendinigArticleDetailList[$index]);
			}
			$response['status'] = true;
		}
		
		$this->addInResponse('response', $response);
		
		return $this->getJsonResponse($this->getResponse());
	}
	
	/**
	 * @Route("/admin/update-caption", name="db_postreach_update_caption")
	 */
	public function updateTrendingArticlCaptioneAction() {
		if(!$this->isAdminSessionExpire()) {
			return $this->getJSONSessionExpire('db_postreach_admin');
		}
		$trendinigArticleDetail = $this->processContent();
		
		if( !empty($trendinigArticleDetail['trendingArticleId']) && !empty($trendinigArticleDetail['caption']) ) {
			$trendingArticleDAO = new TrendingArticleDAO($this->getDoctrine());
			$trendingArticle = new TrendingArticle();
			$trendingArticle->setTrendingArticleId($trendinigArticleDetail['trendingArticleId']);
			$trendingArticleDAO->update($trendingArticle, array('caption' => $trendinigArticleDetail['caption']));
			
			$this->addInResponse('status', true);
		} else {
			$this->addInResponse('status', false);
		}
		return $this->getJsonResponse($this->getResponse());
	}
	
	/**
	 * @Route("/admin/manage-category", name="db_postreach_manage_category")
	 */
	public function manageCategoryAction() {
		if(!$this->isAdminSessionExpire()) {
			return $this->getJSONSessionExpire('db_postreach_admin');
		}
		
		$trendinigArticleDetail = $this->processContent();
		
		$response = array('status'=>'false', 'updatedIdsCategoryList'=>array());
		if(!empty($trendinigArticleDetail['trendingArticleIdList']) && !empty($trendinigArticleDetail['categoryId']) ) {
			$trendingArticleCategoryDAO = new TrendingArticleCategoryDAO($this->getDoctrine());
			for($index = 0; $index < count($trendinigArticleDetail['trendingArticleIdList']); $index ++) {
				foreach($trendinigArticleDetail['categoryId'] as $categoryId) {
					//Check if that category will already applied
					$existingArticleCategoryDetail = $trendingArticleCategoryDAO->findSingleDetailBy(new TrendingArticleCategory(), array('categoryId'=>$categoryId, 'trendingArticleId'=>$trendinigArticleDetail['trendingArticleIdList'][$index]));
					
					if(empty($existingArticleCategoryDetail)) {
						$trendinigArticleCategory = new TrendingArticleCategory();
						$trendinigArticleCategory->setCategoryId($categoryId);
						$trendinigArticleCategory->setTrendingArticleId($trendinigArticleDetail['trendingArticleIdList'][$index]);
						$trendinigArticleCategory->setScore(0);
						$trendingArticleCategoryDAO->save($trendinigArticleCategory);
						
						//Get category list for article
						
						$response['updatedIdsCategoryList'][] = array('trendingArticleId'=>$trendinigArticleDetail['trendingArticleIdList'][$index], 'categoryId'=>$categoryId);
					}
				}
			}
			
			$response['status'] = 'true';
		}
		
		$this->addInResponse('response', $response);
		
		return $this->getJsonResponse($this->getResponse());
	}
	
	/**
	 * @Route("/email", name="db_postreach_email")
	 * @Template("DBAppBundle:email:notification-email.html.twig")
	 */
	public function emailAction() {
		$userDAO = new UserDAO($this->getDoctrine());
		$trendingArticleDAO = new TrendingArticleDAO($this->getDoctrine());
		
		$userDetail = $userDAO->findSingleDetailBy(new User(), array('accountId'=>1));
		$this->addInResponse('userDetail', $userDetail);
		
		$trendingArticleList = $trendingArticleDAO->getTrendingArticleListByAccount(1);
		$this->addInResponse('trendingArticleList', $trendingArticleList["LIST"]);
		
		return $this->getResponse();
	}
}
?>