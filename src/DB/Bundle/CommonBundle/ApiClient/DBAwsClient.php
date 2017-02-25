<?php
namespace DB\Bundle\CommonBundle\ApiClient;

use Aws\Ses\SesClient;
/**
* This class use aws email client and send mail
*/
class DBAwsClient {
	private $awsKey= ''; 
	
	private $awsSecretKey= '';
	
	private $awsRegion = '';
	
	public function __construct($awsKey = null, $awsSecretKey = null, $aswRegion = null) {
		$this->awsKey = $awsKey;
		$this->awsSecretKey = $awsSecretKey;
		$this->awsRegion = $aswRegion;
	}
	
	/**
	 * This function for send mail
	 *
	 * @param $subject 	- holds subject of email
	 * @param $body 	- holds email contents
	 * @param $from 	- holds from email address
	 * @param $to		- holds to email address
	 */
	public function sendMailBySESV($data) {
		/* $sesClient = SesClient::factory(array(
				'key'    => $this->awsKey,
				'secret' => $this->awsSecretKey,
				'region' => $this->awsRegion,
                'version' => 'latest'
		)); */
		
		$sesClient = new SesClient([
				'version'     => 'latest',
				'region'      => $this->awsRegion,
				'credentials' => [
						'key'    => $this->awsKey,
						'secret' => $this->awsSecretKey,
				],
		]);
	
		//Now that you have the client ready, you can build the message
		$msg = array();
	
		if(isset($data['from'])) {
			$msg['Source'] =  $data['from'];
		}
	
		if(isset($data['to'])) {
			$msg['Destination']['ToAddresses'][] 	= $data['to'];
		}
	
		if(isset($data['bcc']) && !empty($data['bcc'])){
			if(count($data['bcc']) > 0 ) {
				foreach($data['bcc'] as $bcc) {
					$bcc = trim($bcc);
					if(!empty($bcc)) {
						//BccAddresses must be an array
						$msg['Destination']['BccAddresses'][] 	= $bcc;
					}
				}
			}
		}
	
		if(isset($data['subject'])) {
			$msg['Message']['Subject']['Data'] 		= $data['subject'];
		}
	
		$msg['Message']['Subject']['Charset'] 		= "UTF-8";
	
		/* if(isset($data['body'])) {
			$msg['Message']['Body']['Text']['Data'] = $data['body'];
		}
	
		$msg['Message']['Body']['Text']['Charset'] 	= "UTF-8";
	 */
		if(isset($data['body'])) {
			$msg['Message']['Body']['Html']['Data'] = $data['body'];
		}
	
		$msg['Message']['Body']['Html']['Charset'] 	= "UTF-8";
		
	
		$result = array('MessageId'=> '0');
	
		try{
				
			$result = $sesClient->sendEmail($msg);
			$result = $result->toArray();
		} catch (Exception $e) {
			$result['status'] = 'ERROR';
			$result['message'] = $e->getMessage();
		}
	
		if(isset($result['status'])) {
			if($result['status'] == 'ERROR') {
				echo "[" . $data['from'] . "] to [" . $data['to'] . "]" . $result['message'];
			}
		}
		return $result;
	}
}
?>