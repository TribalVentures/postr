<?php

namespace DB\Bundle\AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use DB\Bundle\AppBundle\DAO\AdminUserDAO;
use DB\Bundle\AppBundle\Entity\AdminUser;


class AdminUserController extends DbAppController {
	/**
	 * @Route("/admin/users", name="db_postreach_admin_users")
	 * @Template("DBAppBundle:postrAdmin:admin-users.html.twig")
	 */
	public function indexAction() {
		if(!$this->isAdminSessionExpire()) {
			return $this->sendRequest('db_postreach_admin');
		}
		
		$adminUserForm = $this->getRequestParam('adminUserForm', array());
		
		if(empty($adminUserForm['currentPage'])) {
			$adminUserForm['currentPage'] = '1';
		}
		
		if(empty($adminUserForm['search'])) {
			$adminUserForm['search'] = '';
		}
		
		$this->addInResponse('adminUserForm', $adminUserForm);
		
		$adminUserDAO = new AdminUserDAO($this->getDoctrine());
		$adminUserList = $adminUserDAO->getAdminUserList($adminUserForm['search'], $adminUserForm['currentPage']);
		
		$this->addInResponse('adminUserList', $adminUserList);
		
		return $this->getResponse();
	}
	
	/**
	 * @Route("/admin/user/new", name="db_postreach_admin_add_admin_user")
	 * @Template("DBAppBundle:postrAdmin:manage-admin-user.html.twig")
	 */
	public function addAdminUserAction() {
		if(!$this->isAdminSessionExpire()) {
			return $this->sendRequest('db_postreach_admin');
		}
		
		if($this->getRequest()->isMethod('POST')) {
			$adminUserForm = $this->getRequestParam('adminUserForm', array());
			
			$adminUserDAO = new AdminUserDAO($this->getDoctrine());
			$adminUserDetali = $adminUserDAO->addUser($adminUserForm);
			
			if(!empty($adminUserDetali['adminUserId'])) {
				return $this->sendRequest('db_postreach_admin_users');
			} else {
				return $this->sendRequest('db_postreach_admin_add_admin_user');
			}
		}
		
		return $this->getResponse();
	}
	
	/**
	 * @Route("/admin/user/edit/{adminUserId}", name="db_postreach_admin_edit_admin_user")
	 * @Template("DBAppBundle:postrAdmin:manage-admin-user.html.twig")
	 */
	public function viewAdminUserAction($adminUserId) {
		if(!$this->isAdminSessionExpire()) {
			return $this->sendRequest('db_postreach_admin');
		}
		
		$adminUserDAO = new AdminUserDAO($this->getDoctrine());
		if($this->getRequest()->isMethod('POST')) {
			$adminUserForm = $this->getRequestParam('adminUserForm', array());
			
			$adminUserDAO = new AdminUserDAO($this->getDoctrine());
			$adminUserDetali = $adminUserDAO->editUser($adminUserForm);
		
			if(!empty($adminUserDetali['adminUserId'])) {
				return $this->sendRequest('db_postreach_admin_users');
			} else {
				return $this->sendRequest('db_postreach_admin_add_admin_user');
			}
		}
		
		$adminUserDetail = $adminUserDAO->getAdminUserByadminUserId($adminUserId);
		$this->addInResponse('adminUserDetail', $adminUserDetail);
		
		return $this->getResponse();
	}
	
	/**
	 * @Route("/admin/user/update", name="db_postreach_admin_update_admin_user")
	 * @Template("DBAppBundle:postrAdmin:manage-admin-user.html.twig")
	 */
	public function updateAdminUserAction() {
		if(!$this->isAdminSessionExpire()) {
			return $this->sendRequest('db_postreach_admin');
		}
	
		$adminUserForm = $this->getRequestParam('adminUserForm', array());
		$adminUserDAO = new AdminUserDAO($this->getDoctrine());
		$adminUserDetali = $adminUserDAO->editUser($adminUserForm);
	
		if(!empty($adminUserDetali['adminUserId'])) {
			return $this->sendRequest('db_postreach_admin_users');
		} else {
			return $this->sendRequest('db_postreach_admin_add_admin_user');
		}
	}
	
	/**
	 * @Route("/admin/user/delete/{adminUserId}", name="db_postreach_admin_delete_admin_user")
	 */
	public function deleteAdminUserAction($adminUserId) {
		if(!$this->isAdminSessionExpire()) {
			return $this->sendRequest('db_postreach_admin');
		}
		
		$adminUserDAO = new AdminUserDAO($this->getDoctrine());
		$result = $adminUserDAO->deleteBy(new AdminUser(), array('adminUserId'=>$adminUserId));
		
		return $this->sendRequest('db_postreach_admin_users');
	}
}
?>