<?php

namespace DB\Bundle\AppBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use DB\Bundle\AppBundle\DAO\SettingDAO;
use DB\Bundle\AppBundle\DAO\LeadDAO;
use Symfony\Component\Debug\Exception\ContextErrorException;

class ScrapHouzzCommand extends ContainerAwareCommand {
	const DEBUG_INFO = 'INFO';
	CONST DEBUG_ERROR = 'ERROR';
	
	const NO_OF_RECORDS = 60;
	
	private $output = null;
	
	private $_response = array();
	
	private $noOfRecord = 30;
	
	//php app/console primo:notification 80 --prod --ArticleOnly
	protected function configure() {
		$this->setName('primo:scrap-houzz')
		->setDescription('Scrap Houzz site')
		->addArgument('noOfRecord',InputArgument::OPTIONAL,'Handle no of request')
		->addOption('ChangeScrapUrl', null, InputOption::VALUE_REQUIRED, 'This will only scrap the list of all companies')
		->addOption('ScapList', null, InputOption::VALUE_NONE, 'This will only scrap the list of all companies')
		->addOption('ScrapDetail', null, InputOption::VALUE_NONE, 'This option will scrap the detail of each company')
		->addOption('Reset', null, InputOption::VALUE_NONE, 'This option will reset the object')
		;
	}

	/**
	 * This function return the response collection
	 * @return mixed[] : This function will return the colelction of response variables
	 */
	public function getResponse() {
		return array('response'=>$this->_response);
	}

	/**
	 * This function add new data/object in response array, So we can use that objects on template.
	 *
	 * @param string $key - Key is always unique, If use dublicate then that will overight the values.
	 * @param mixed $value - Value might be object or any simple variables.
	 */
	public function addInResponse($key, $value) {
		if(isset($key) && isset($value)) {
			$this->_response['' . $key] = $value;
		}
		 
		return $value;
	}
	
	/**
	 * This function execute the commands
	 * {@inheritDoc}
	 * @see \Symfony\Component\Console\Command\Command::execute()
	 */
	protected function execute(InputInterface $input, OutputInterface $output) {
		$this->output = $output;
		
		$this->noOfRecord = $input->getArgument('noOfRecord');
		if(empty($this->noOfRecord)) {
			$this->noOfRecord = 500;
		}
		
		$this->log('[noOfRecord] : ' . $this->noOfRecord );
		
		if($input->getOption('ScapList')) {
			$this->log('Start to scap the pages');
			$this->scrapSite();
		} else if($input->getOption('ScrapDetail')) {
			$this->log('Start to scrap company detail');
			$this->scrapLeadSite();
		} else if($input->getOption('ChangeScrapUrl')) {
			$this->log('Changing the scrap URL');
			$url = $input->getOption('ChangeScrapUrl');
			$this->log('[URL]: ' . $url);
			$this->changeUrl($url);
		} else {
			$this->log('Invalid option, So stop the command');
		}
	}
	
	/**
	 * This function change the Houzz URL
	 * @param string $url
	 */
	private function changeUrl($url = '') {
		if(!empty($url)) {
			$settingDAO = new SettingDAO($this->getDoctrine());
			//Get current URL
			$settingDetail = $settingDAO->getSettingByKey(SettingDAO::SETTING_KEY_SCRAP_HOUZZ_URL);
			if(!empty($settingDetail['id'])) {
				$settingDAO->editSetting(array('id'=>$settingDetail['id'], 'settingValue'=>$url));
			} else {
				$settingDetail = $settingDAO->addSetting(array('label'=>SettingDAO::SETTING_KEY_SCRAP_HOUZZ_URL, 'settingKey'=>SettingDAO::SETTING_KEY_SCRAP_HOUZZ_URL, 'settingValue'=>$url));
			}
		
			//reset the count of page
			$settingDetail = $settingDAO->getSettingByKey(SettingDAO::SETTING_KEY_SCRAP_HOUZZ_CURRENT_PAGE);
			if(!empty($settingDetail['id'])) {
				$settingDAO->editSetting(array('id'=>$settingDetail['id'], 'settingValue'=>'1'));
			} else {
				$settingDetail = $settingDAO->addSetting(array('label'=>SettingDAO::SETTING_KEY_SCRAP_HOUZZ_CURRENT_PAGE, 'settingKey'=>SettingDAO::SETTING_KEY_SCRAP_HOUZZ_CURRENT_PAGE, 'settingValue'=>'1'));
			}
		}
	}
	
	/**
	 * This function scrap the site and pull into CSV files
	 */
	private function scrapSite() {
		$settingDAO = new SettingDAO($this->getDoctrine());
		$settingDetail = $settingDAO->getSettingByKey(SettingDAO::SETTING_KEY_SCRAP_HOUZZ_CURRENT_PAGE);
		if(empty($settingDetail)) {
			$settingDetail = $settingDAO->addSetting(array('label'=>'Houzz current page', 'settingKey'=>'SCRAP_HOUZZ_CURRENT_PAGE', 'settingValue'=>'1'));
		}
		
		//Get URL
		$settingURLDetail = $settingDAO->getSettingByKey(SettingDAO::SETTING_KEY_SCRAP_HOUZZ_URL);
		if(empty($settingURLDetail)) {
			$this->log('No any Houzz URL found to scrap ');
			return;
		}
		
		$pageNumber = $settingDetail['settingValue'];
		$noOfRecord = 15;
		$url = $settingURLDetail['settingValue'] . '/p/';
		
		$companyList = array();
		for($index = 1; $index < $this->noOfRecord; $index ++) {
			$startTime = time();
			
			$currentUl = $url . $pageNumber;
			$this->log('Scrapp page : ' . $pageNumber);
			$this->log($currentUl);
			
			$list = $this->scrapUrl($currentUl);
			//$companyList = array_merge($companyList, $list);
			
			
			$pageNumber = $pageNumber + $noOfRecord;
			$settingDetail['settingValue'] = $pageNumber;

			$endTime = time();
			$diff = $endTime - $startTime;
			
			$this->log('Process takes ' . $diff . ' second to run and scrap then ' . count($list) . ' records');
			
			//Update setting
			$settingDAO->editSetting($settingDetail);
			$leadDAO = new LeadDAO($this->getDoctrine());
			$leadDAO->addLeadList($list);
		}
		
		/*if(!empty($companyList)) {
			$this->arrayToCSV('scrap-data.csv', $companyList);
		}*/
	}
	
	/**
	 * This function scap the url and return the company list
	 * @param string $currentUl
	 */
	private function scrapUrl($currentUl) {
		$html = file_get_dom($currentUl);
		
		$companyList = array();
		foreach($html('div.browseListBody div.whiteCard') as $element) {
			$record = array();
			$record['objectId'] = $element->objid;
			//Scap company name
			foreach($element('div.name-info a[itemprop="name"]') as $node) {
				$record['company'] = $node->getPlainText();
				$record['houzzUrl'] = $node->href;
				break;
			}
			
			/*//Scap company address
			foreach($element('div.pro-meta') as $node) {
				$record[] = $node->getPlainText();
				break;
			}*/
			
			//Scap company phone
			foreach($element('ul li.pro-phone') as $node) {
				$record['phone'] = $node->getPlainText();
				break;
			}
			
			$companyList[] = $record;
		}
		
		return $companyList;
	}
	
	/**
	 * This function scrap the lelad detail
	 */
	private function scrapLeadSite() {
		$leadDAO = new LeadDAO($this->getDoctrine());
		$leadList = $leadDAO->getLeadList(array(), '0', $this->noOfRecord);
		if(!empty($leadList)) {
			foreach($leadList as $leadDetail) {
				$lead = $this->scrapDetailUrl($leadDetail['houzzUrl']);
				if(!empty($lead)) {
					$lead['id'] = $leadDetail['id'];
					$lead['leadStatus'] = '1';
					$leadDAO->editLead($lead);
				} else {
					$lead = array();
					$lead['id'] = $leadDetail['id'];
					$lead['leadStatus'] = '2';
					$leadDAO->editLead($lead);
				}
			}
		}
	}

	/**
	 * This function scrap the detail page
	 */
	private function scrapDetailUrl($currentUrl) {
		$html = '';
		try {
			$html = file_get_dom($currentUrl);
		} catch (ContextErrorException $e) {
			echo "Exception : ". $e->getMessage();
			return array();
		} catch (Exception $e) {
			echo "Exception : ". $e->getMessage();
			return array();
		}
		
		if(empty($html)) {
			return array();
		}
		
		$record = array();
		foreach($html('div.profile-about-right div.info-list-label') as $element) {
			
			$text = $element->getPlainText();
			$searchText = strtolower($text);
			if(strpos($searchText, 'contact:')) {
				$record['contactPerson'] = trim(str_replace('Contact:', '', $text));
			}
			
			if(strpos($searchText, 'location:')) {
				foreach($element('span[itemprop="streetAddress"]') as $node) {
					$record['streetAddress'] = $node->getPlainText();
					break;
				}
				
				foreach($element('span[itemprop="addressLocality"]') as $node) {
					$record['locality'] = $node->getPlainText();
					break;
				}
				
				foreach($element('span[itemprop="addressRegion"]') as $node) {
					$record['region'] = $node->getPlainText();
					break;
				}
				
				foreach($element('span[itemprop="postalCode"]') as $node) {
					$record['postalCode'] = $node->getPlainText();
					break;
				}
				
				foreach($element('span[itemprop="addressCountry"]') as $node) {
					$record['country'] = $node->getPlainText();
					break;
				}
			}
		}
		
		foreach($html('div>ul.touch-scroll-list li.profile-content-narrow div.pro-contact-methods a') as $element) {
			$record['url'] = $element->href;
			break;
		}
		
		return $record;
	}
	
	/**
	 * Takes in a filename and an array associative data array and outputs a csv file
	 * @param string $fileName
	 * @param array $assocDataArray
	 */
	private function arrayToCSV($fileName, $data) {
		if(isset($data)){
			$fp = fopen('/home/patildipakr/Downloads/' . $fileName, 'w');
			foreach($data AS $values){
				fputcsv($fp, $values);
			}
			fclose($fp);
		}
	}
	
	/**
	 * This function print log to console
	 * @param unknown $message
	 * @param unknown $type
	 */
	public function log($message, $type = self::DEBUG_INFO) {
		$text = '[' .  date('Y-m-d H:i:s') .'][' . $type . '] ' . $message;
		if(isset($this->output)) {
			$this->output->writeln($text);
		} else {
			echo $text . "\r\n";
		}
	}
	
	/**
	 * This function return the doctrine object
	 */
	public function getDoctrine() {
    	return $this->getContainer()->get('doctrine');
    }

    /**
     * Gets a service by id.
     *
     * @param string $id The service id
     *
     * @return object The service
     */
    public function get($id)
    {
        return $this->container->get($id);
    }
    
    public function renderView($template, $data) {
    	return $this->getContainer()->get('templating')->render($template, $data);
    }
}
?>