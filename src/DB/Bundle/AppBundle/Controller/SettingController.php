<?php

namespace DB\Bundle\AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use DB\Bundle\AppBundle\DAO\SettingDAO;

class SettingController extends DbAppController {
	/**
	 * @Route("/admin/setting", name="db_postreach_admin_setting")
	 * @Template("DBAppBundle:postrAdmin:setting.html.twig")
	 */
	public function settingAction() {
		if(!$this->isAdminSessionExpire()) {
			return $this->sendRequest('db_postreach_admin');
		}
		
		$settingDAO = new SettingDAO($this->getDoctrine());
		
		if($this->getRequest()->isMethod('POST')) {
			$settingForm = $this->getRequestParam('settingForm', array());
			$settingDAO->updateNotification($settingForm);
			
			return $this->sendRequest('db_postreach_admin_setting');
		}
		
		$settingMap = $settingDAO->getEnailNotificationSettingMap();
		
		$this->addInResponse('settingMap', $settingMap);
		
		return $this->getResponse();
	}
}
?>