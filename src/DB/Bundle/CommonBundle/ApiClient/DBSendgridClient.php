<?php
namespace DB\Bundle\CommonBundle\ApiClient;

use SendGrid\Email;

/**
* This class use send grid apis tocommunicate with there system to send emails
*/
class DBSendgridClient {
	private $apiKey = '';
	
	function __construct($apiKey) {
		$this->apiKey = $apiKey;
	}
	
	/**
	 * This function send email and return response
	 * @param array $emailDetail
	 */
	public function sendMail($emailDetail = array()) {
		if(empty($emailDetail)) {
			return array();
		}
		
		try {
			$email = new Email();
			
			$email
			->addTo('patildipakr@gmail.com')
			->setFrom($emailDetail['from'])
			->setSubject($emailDetail['subject'])
			->addHeader('Content-Type', 'html/text')
			->setHtml($emailDetail['body']);
			
			if(!empty($emailDetail['bcc'])) {
				$email->addBcc($emailDetail['bcc']);
			}
			
			$sendGrid = new \SendGrid($this->apiKey);
			
			return $sendGrid->send($email);
		} catch(\SendGrid\Exception $e) {
			/* echo $e->getCode();
			foreach($e->getErrors() as $er) {
				echo $er;
			} */
			echo 'EXCEPTION';
		}
		
		return array();
	}
}
?>