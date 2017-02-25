<?php

namespace DB\Bundle\AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use DB\Bundle\AppBundle\DAO\CategoryDAO;
use DB\Bundle\AppBundle\Common\Config;
use DB\Bundle\AppBundle\Common\SpikeConfig;
use DB\Bundle\AppBundle\Entity\Category;
use DB\Bundle\CommonBundle\Util\DBUtil;

class CategoryController extends DbAppController {
	/**
	 * @Route("/admin/category", name="db_postreach_admin_category")
	 * @Template("DBAppBundle:postrAdmin:category.html.twig")
	 */
	public function categoryAction() {
		if(!$this->isAdminSessionExpire()) {
			return $this->sendRequest('db_postreach_admin');
		}
		
		$categoryDAO = new CategoryDAO($this->getDoctrine());
		
		$userTypeList = $categoryDAO->getUserType(0, 1);
		$this->addInResponse('userTypeList', $userTypeList);
		
		//Get page number
		$categoryForm = $this->getRequestParam('categoryForm', array());
		
		if(empty($categoryForm['currentPage'])) {
			$categoryForm['currentPage'] = '1';
		}
		
		if(empty($categoryForm['parentCategoryId'])) {
			$categoryForm['parentCategoryId'] = '0';
			if(!empty($userTypeList[0]['categoryId'])) {
				$categoryForm['parentCategoryId'] = $userTypeList[0]['categoryId'];
			}
		}
		
		$this->addInResponse('categoryListForm', $categoryForm);
		
		$categoryList = $categoryDAO->getCategoryList($categoryForm['parentCategoryId'], '-1', $categoryForm['currentPage']);
		$this->addInResponse('categoryList', $categoryList);
		
		return $this->getResponse();
	}
	
	/**
	 * @Route("/admin/addCategory", name="db_postreach_admin_addcategory")
	 * @Template("DBAppBundle:postrAdmin:edit_category.html.twig")
	 */
	public function addCategoryAction() {
		if(!$this->isAdminSessionExpire()) {
			return $this->sendRequest('db_postreach_admin');
		}
		
		$categoryForm = $this->getRequestParam('categoryForm', array());
		
		if(empty($categoryForm['category'])) {
			$errorMesage = 'Please enter category name';
			$this->addInResponse('error', $errorMesage);
			return $this->getResponse();
		}
		
		$categoryDAO = new CategoryDAO($this->getDoctrine());
		if(!isset($categoryForm['parentCategoryId'])) {
			$categoryForm['parentCategoryId'] = '0';
			$categoryForm['categoryType'] = '0';
		}
		
		$categoryDetali = $categoryDAO->addCategory($categoryForm);
		
		if(!empty($categoryDetali['categoryId']) && $categoryDetali['categoryType'] == 1) {
			return $this->sendRequest('db_postreach_admin_view_category', array('categoryId'=>$categoryDetali['categoryId']));
		} else {
			return $this->sendRequest('db_postreach_admin_category');
		}
	}
	
	/**
	 * @Route("/admin/c/{categoryId}", name="db_postreach_admin_view_category")
	 * @Template("DBAppBundle:postrAdmin:edit_category.html.twig")
	 */
	public function viewCategoryAction($categoryId) {
		if(!$this->isAdminSessionExpire()) {
			return $this->sendRequest('db_postreach_admin');
		}
		
		$categoryDAO = new CategoryDAO($this->getDoctrine());
		
		$categoryDetail = $categoryDAO->getCaegoryByCategoryId($categoryId);
		
		$spikeSortByList = Config::getSpkeSortByList();
		$this->addInResponse('spikeSortByList', $spikeSortByList);
		
		$spikeTopicList = SpikeConfig::getTopic();
		$this->addInResponse('spikeTopicList', $spikeTopicList);
		
		$countryList = SpikeConfig::getCountry();
		$this->addInResponse('countryList', $countryList);
		
		//Make include list into user readable form
		if(!empty($categoryDetail['includeCountryList'])) {
			$temptList = array();
			foreach($categoryDetail['includeCountryList'] as $countryCode) {
				foreach($countryList as $country) {
					if($country->Code == $countryCode) {
						$temptList[] = array('code'=>$country->Code, 'name'=>$country->Name);
					}
				}
			}
			
			$categoryDetail['includeCountryList'] = $temptList;
		}
		
		//Make exclude list into user readable form
		if(!empty($categoryDetail['excludeCountryList'])) {
			$temptList = array();
			foreach($categoryDetail['excludeCountryList'] as $countryCode) {
				foreach($countryList as $country) {
					if($country->Code == $countryCode) {
						$temptList[] = array('code'=>$country->Code, 'name'=>$country->Name);
					}
				}
			}
				
			$categoryDetail['excludeCountryList'] = $temptList;
		}
		
		//Make includeTopicList into user readable form
		if(!empty($categoryDetail['includeTopicList'])) {
			$temptList = array();
			foreach($categoryDetail['includeTopicList'] as $topic) {
				foreach($spikeTopicList as $spikeTopic) {
					if($spikeTopic['topicId'] == $topic) {
						$temptList[] = $spikeTopic;
					}
				}
			}
		
			$categoryDetail['includeTopicList'] = $temptList;
		}
		
		//Make includeTopicList into user readable form
		if(!empty($categoryDetail['excludeTopicList'])) {
			$temptList = array();
			foreach($categoryDetail['excludeTopicList'] as $topic) {
				foreach($spikeTopicList as $spikeTopic) {
					if($spikeTopic['topicId'] == $topic) {
						$temptList[] = $spikeTopic;
					}
				}
			}
		
			$categoryDetail['excludeTopicList'] = $temptList;
		}
		
		$this->addInResponse('categoryDetail', $categoryDetail);
		
		return $this->getResponse();
	}
	
	/**
	 * @Route("/admin/updateCategory", name="db_postreach_admin_update_category")
	 *@Template("DBAppBundle:postrAdmin:category.html.twig")
	 */
	public function updateCategoryAction() {
		if(!$this->isAdminSessionExpire()) {
			return $this->sendRequest('db_postreach_admin');
		}
		
		$categoryForm = $this->getRequestParam('categoryForm');
		
		if(!empty($categoryForm['categoryId'])) {
			$categoryDAO = new CategoryDAO($this->getDoctrine());
			
			$categoryDetail = $categoryDAO->findSingleDetailBy(new Category(), array('categoryId'=>$categoryForm['categoryId']));
			
			if(!empty($categoryDetail['categoryId'])) {
				$categoryForm['image'] = $this->uploadCategoryImage($categoryDetail['categoryId'], $categoryDetail['image']);
			}
			
			$categoryDAO->updateCategory($categoryForm);
		}
		
		return $this->sendRequest('db_postreach_admin_category');
	}
	
	/**
	 * This function upload the category image into upload default upload directory
	 * @param integer $categoryId
	 * @param integer $imageUrl
	 * @return string Return the uploaded image path
	 */
	private function uploadCategoryImage($categoryId, $imageUrl) {
		if(isset($_FILES['categoryForm']['name']['categoryImage']) && !empty($_FILES['categoryForm']['name']['categoryImage'])) {
			if($_FILES['categoryForm']['size']['categoryImage'] > 0) {
				$imageUploadPath = Config::getSParameter('UPLOAD_PATH')  . '/category/';
				if(!file_exists($imageUploadPath)) {
					mkdir($imageUploadPath);
					chmod($imageUploadPath, 0777);
				}
	
				$extension = pathinfo($_FILES['categoryForm']['name']['categoryImage'], PATHINFO_EXTENSION);
				$fileName = 'category' . $categoryId . DBUtil::getUniqueKey() . '.' . $extension;
	
				$imageUrlPath = $imageUploadPath . $fileName;
					
				if(!empty($imageUrl) && file_exists($imageUrl)) {
					unlink($imageUrl);
				}
				move_uploaded_file($_FILES['categoryForm']['tmp_name']['categoryImage'], $imageUrlPath);
	
				$imageUrl = $imageUrlPath;
			}
		}
		
		return $imageUrl;
	}
	
	/**
	 * @Route("/admin/getCategory", name="db_postreach_admin_get_category")
	 */
	public function getCategoryAction() {
		if(!$this->isAdminSessionExpire()) {
			return $this->getJSONSessionExpire('db_postreach_admin');
		}
	
		$parentCategoryId = $this->getRequestParam('parentCategoryId', '-1');
		
		$categoryDAO = new CategoryDAO($this->getDoctrine());
		$categoryList = $categoryDAO->getAllDefaultCategoryList($parentCategoryId);
		
		$this->addInResponse('categoryList', $categoryList);
	
		return $this->getJsonResponse($this->getResponse());
	}
	
}
?>