<?php

namespace DB\Bundle\AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use DB\Bundle\AppBundle\DAO\UserDAO;
use DB\Bundle\CommonBundle\Util\DBUtil;
use DB\Bundle\AppBundle\DAO\AccountDAO;
use DB\Bundle\AppBundle\Entity\Account;
use DB\Bundle\CommonBundle\Base\BaseController;
use DB\Bundle\AppBundle\DAO\CategoryDAO;
use DB\Bundle\AppBundle\DAO\AccountCategoryDAO;
use DB\Bundle\AppBundle\DAO\SocialProfileDAO;
use DB\Bundle\AppBundle\Entity\AccountFrequency;
use DB\Bundle\AppBundle\DAO\AccountFrequencyDAO;
use DB\Bundle\AppBundle\DAO\AdminUserDAO;

class AdminController extends DbAppController {
	/**
	 * @Route("/admin", name="db_postreach_admin")
	 * @Template("DBAppBundle:postrAdmin:login.html.twig")
	 */
	public function indexAction() {
		return $this->getResponse();
	}
	
	/**
	 * @Route("/admin/login", name="db_postreach_admin_login")
	 * @Template("DBAppBundle:postrAdmin:login.html.twig")
	 */
	public function loginAction() {
		//Get login detail
		$loginForm = $this->getRequestParam('loginForm', array());
		$errorMesage = 'Invalid email/password. Please try again';
		
		if(empty($loginForm['email']) || empty($loginForm['password'])) {
			$this->addInResponse('error', $errorMesage);
			return $this->getResponse();
		}
		
		$adminUserDAO = new AdminUserDAO($this->getDoctrine());
		$adminUserDetail = $adminUserDAO->login($loginForm['email'], DBUtil::getPassword($loginForm['password']));
		
		if(!empty($adminUserDetail['adminUserId'])) {
			//Remove password
			unset($adminUserDetail['password']);
				
			$this->setAdminUser($adminUserDetail);
			//Redirect to new version
			return $this->sendRequest('db_postreach_admin_account');
		} else {
			$this->addInResponse('error', $errorMesage);
			return $this->getResponse();
		}
		return $this->getResponse();
	}
	
	/**
	 * @Route("/admin/logout", name="db_postreach_admin_logout")
	 */
	public function logoutAction() {
		$this->invalidSession();
		$this->removeSession(BaseController::USER_SESSION_KEY . BaseController::USER_ADMI_SESSION_KEY);
		$this->get('session')->clear();
		$session = $this->get('session');
		$ses_vars = $session->all();
		foreach ($ses_vars as $key => $value) {
			$session->remove($key);
			$this->removeSession($key);
		}
		
		return $this->sendRequest('db_postreach_admin');
	}
}
?>