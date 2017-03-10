<?php

namespace DB\Bundle\AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use DB\Bundle\AppBundle\DAO\UserDAO;
use DB\Bundle\AppBundle\Entity\User;
use DB\Bundle\CommonBundle\Util\DBUtil;
use DB\Bundle\AppBundle\DAO\AccountDAO;
use DB\Bundle\AppBundle\Common\Config;

class AccountController extends DbAppController {
	/**
	 * @Route("/profile", name="db_postreach_profile")
	 * @Template("DBAppBundle:postr:profile.html.twig")
	 */
	public function profileAction() {
		$isValid = $this->isValidUserRequest();
		if(!empty($isValid['nextRoute'])) {
			return $this->sendRequest($isValid['nextRoute']);
		}
		
		$currentUser = $this->getUser();
		
		$userDAO = new UserDAO($this->getDoctrine());
		if($this->getRequest()->isMethod('POST')) {
			$profileForm = $this->getRequestParam('profileForm', array());
			$error = 'Invalid current password or new password should not match';
			$isNotError = true;
			if(!empty($profileForm)) {
				$profileForm['userId'] = $currentUser['userId'];
				$existUserDetail = $userDAO->findSingleDetailBy(new User(), array('userId'=>$currentUser['userId']));
				if(!empty($profileForm['currentPassword']) && !empty($profileForm['newPassword']) && !empty($profileForm['confirmNewPassword'])) {
					if($profileForm['newPassword'] == $profileForm['confirmNewPassword']) {
						if(!empty($existUserDetail['password']) && $existUserDetail['password'] == DBUtil::getPassword($profileForm['currentPassword'])) {
							$profileForm['password'] = $profileForm['newPassword'];
						} else {
							$isNotError = false;
							$this->addInResponse('error', $error);
						}
					} else {
						$isNotError = false;
						$this->addInResponse('error', $error);
					}
				}
				
				if(!empty($profileForm['userId'])) {
					$profileForm['profile'] = $this->uploadPrfileImage($profileForm['userId'], $existUserDetail['profile']);
				}
				
				$userDAO->editUser($profileForm);
				
				if($isNotError) {
					return $this->sendRequest('db_postreach_profile');
				} else {
					$this->addInResponse('userDetail', $profileForm);
					return $this->getResponse();
				}
			}
		}
		
		$userDetail = $userDAO->findSingleDetailBy(new User(), array('userId'=>$currentUser['userId']));
		if(!empty($userDetail)) {
			$this->addInResponse('userDetail', $userDetail);
		}
		
		return $this->getResponse();
	}
	
	/**
	 * This function upload the category image into upload default upload directory
	 * @param integer $categoryId
	 * @param integer $imageUrl
	 * @return string Return the uploaded image path
	 */
	private function uploadPrfileImage($userId, $imageUrl) {//profileForm[profileImage]
		if(isset($_FILES['profileForm']['name']['profileImage']) && !empty($_FILES['profileForm']['name']['profileImage'])) {
			if($_FILES['profileForm']['size']['profileImage'] > 0) {
				$imageUploadPath = Config::getSParameter('UPLOAD_PATH')  . '/profile/';
				if(!file_exists($imageUploadPath)) {
					mkdir($imageUploadPath);
					chmod($imageUploadPath, 0777);
				}
	
				$extension = pathinfo($_FILES['profileForm']['name']['profileImage'], PATHINFO_EXTENSION);
				$fileName = 'profile' . $userId . '.' . $extension;
	
				$imageUrlPath = $imageUploadPath . $fileName;
					
				if(!empty($imageUrl) && file_exists($imageUrl)) {
					unlink($imageUrl);
				}
				move_uploaded_file($_FILES['profileForm']['tmp_name']['profileImage'], $imageUrlPath);
	
				$imageUrl = $imageUrlPath;
			}
		}
		
		return $imageUrl;
	}
	
	/**
	 * @Route("/admin/account", name="db_postreach_admin_account")
	 * @Template("DBAppBundle:postrAdmin:account.html.twig")
	 */
	public function adminAccountAction() {
		if(!$this->isAdminSessionExpire()) {
			return $this->sendRequest('db_postreach_admin');
		}
		
		$accountForm = $this->getRequestParam('accountForm', array());
		
		if(empty($accountForm['currentPage'])) {
			$accountForm['currentPage'] = '1';
		}
		
		if(empty($accountForm['search'])) {
			$accountForm['search'] = '';
		}
		
		if(empty($accountForm['accountStatus'])) {
			$accountForm['accountStatus'] = '!8';
		}
		
		$options = array();
		if(!empty($accountForm['accountStatus'])) {
			$options['accountStatus'] = $accountForm['accountStatus'];
		}
		
		$this->addInResponse('accountForm', $accountForm);
		
		//Get account list
		$accountDAO = new AccountDAO($this->getDoctrine());
		
		$accountList = $accountDAO->getAccountList($accountForm['search'], $accountForm['currentPage'], $options);
		$this->addInResponse('accountList', $accountList);
		
		//get braitnree environment
		$braintreeUrl = 'https://www.braintreegateway.com/merchants/gwd57kbpw8hp33rv/customers/';
		$environment = Config::getSParameter('BRAINTREE_ENVIRONMENT', '');
		if($environment == 'sandbox') {
			$braintreeUrl = 'https://sandbox.braintreegateway.com/merchants/nrjfyx8vsjg6qw55/customers/';
		}
		$this->addInResponse('braintreeUrl', $braintreeUrl);
		
		return $this->getResponse();
	}
	
	/**
	 * @Route("/admin/account/cancel", name="db_postreach_admin_account_cancel")
	 */
	public function adminAccountCancelAction() {
		if(!$this->isAdminSessionExpire()) {
			return $this->getJSONSessionExpire('db_postreach_admin');
		}
		
		$accountId = $this->getRequestParam('id', '');
		
		if(!empty($accountId)) {
			//Get account list
			$accountDAO = new AccountDAO($this->getDoctrine());
			$accountDAO->editAccount(array('accountId'=>$accountId, 'accountStatus'=>AccountDAO::ACCOUNT_STATUS_CANCEL));
			
			$this->addInResponse('message', 'Account canceled successfully');
			$this->addInResponse('id', $accountId);
		} else {
			$this->addInResponse('error', 'Accoutn is not deleted, Invalid ID, Pease try again');
		}
		
		return $this->getJsonResponse($this->getResponse());
	}
	
	/**
	 * @Route("/admin/account/dashboard/{accountId}", name="db_postreach_admin_account_dashboard")
	 * @Template("DBAppBundle:postrAdmin:account.html.twig")
	 */
	public function adminAccountDashboardAction($accountId) {
		if(!$this->isAdminSessionExpire()) {
			return $this->sendRequest('db_postreach_admin');
		}
		
		$accountForm = $this->getRequestParam('accountForm', array());
		
		if(empty($accountForm['currentPage'])) {
			$accountForm['currentPage'] = '1';
		}
		
		if(empty($accountForm['search'])) {
			$accountForm['search'] = '';
		}
		
		if(empty($accountForm['accountStatus'])) {
			$accountForm['accountStatus'] = '!8';
		}
		
		$options = array();
		if(!empty($accountForm['accountStatus'])) {
			$options['accountStatus'] = $accountForm['accountStatus'];
		}
		
		$this->addInResponse('accountForm', $accountForm);
		
		//Get account list
		$accountDAO = new AccountDAO($this->getDoctrine());
		
		$accountList = $accountDAO->getAccountList($accountForm['search'], $accountForm['currentPage'], $options);
		$this->addInResponse('accountList', $accountList);
		
		return $this->getResponse();
	}
	
	/**
	 * @Route("/remove-plan", name="db_postreach_remove_plan")
	 */
	public function removePlanAction() {
		$isValid = $this->isValidUserRequest();
		if(!empty($isValid['nextRoute'])) {
			return $this->sendRequest($isValid['nextRoute']);
		}
		
		$currentUser = $this->getUser();
		
		//Set account status status to send confirmation email
		$accountDAO = new AccountDAO($this->getDoctrine());
		$accountDAO->cancelAccount($currentUser['accountId']);
		
		return $this->sendRequest('db_postreach_logout', array('n'=>'ca'));
	}
}
?>