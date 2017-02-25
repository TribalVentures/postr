<?php

namespace DB\Bundle\AppBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use DB\Bundle\AppBundle\Common\Config;
use DB\Bundle\AppBundle\DAO\TrendingArticleDAO;
use DB\Bundle\AppBundle\BotHelper\BotHelper;
use DB\Bundle\CommonBundle\FacebookMassenger\FbBotApp;
use DB\Bundle\AppBundle\DAO\CategoryDAO;
use DB\Bundle\AppBundle\DAO\TrendingArticleCategoryDAO;
use DB\Bundle\CommonBundle\ApiClient\DBNewsWhipClient;
use DB\Bundle\AppBundle\Entity\TrendingArticleCategory;
use DB\Bundle\AppBundle\DAO\NotificationSettingsDAO;
use DB\Bundle\AppBundle\Entity\NotificationSettings;
use DB\Bundle\AppBundle\DAO\AccountDAO;
use DB\Bundle\AppBundle\Entity\User;
use DB\Bundle\AppBundle\DAO\UserDAO;
use DB\Bundle\AppBundle\Entity\Account;
use DB\Bundle\CommonBundle\ApiClient\DBSendgridClient;
use DB\Bundle\AppBundle\DAO\ArticleNotifyHistoryDAO;
use DB\Bundle\AppBundle\DAO\EmailHistoryDAO;
use DB\Bundle\AppBundle\DAO\SocialPostDAO;
use DB\Bundle\AppBundle\DAO\AccountFrequencyDAO;
use DB\Bundle\CommonBundle\Util\DBUtil;
use DB\Bundle\AppBundle\DAO\SocialProfileDAO;
use DB\Bundle\AppBundle\Entity\SocialProfile;
use DB\Bundle\AppBundle\Entity\TrendingArticle;
use DB\Bundle\AppBundle\Entity\SocialPost;

class DBCleanCommand extends ContainerAwareCommand {
	const DEBUG_INFO = 'INFO';
	CONST DEBUG_ERROR = 'ERROR';
	
	const NO_OF_RECORDS = 60;
	
	private $score = 60;
	private $time = 12;
	
	private $output = null;
	
	private $_response = array();
	
	private $url = 'http://localhost/dev/postreach/web/app_dev.php/';
	
	//php app/console primo:notification 80 --prod --ArticleOnly
	protected function configure() {
		$this->setName('primo:dbClean')
		->setDescription('This command cleans the the DB records')
		->addOption('TrendingArticles', null, InputOption::VALUE_NONE, 'This option clean the TrendingArticles DB')
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
		
		if($input->getOption('TrendingArticles')) {
			$this->log('[TrendingArticles] Start to clean DB');
			$this->cleanTrendingArticles();
			$this->log('[TrendingArticles] End to clean DB');
		} else {
			$this->log('Invalid option choose');
		}
	}
	
	/**
	 * This function cleans the clean trending articles records
	 */
	private function cleanTrendingArticles() {
		//clean all article those are article status is 1
		$trendingArticleDAO = new TrendingArticleDAO($this->getDoctrine());
		$trendingArticleDAO->cleanTrendingArticle(array('trendingArticleStatus'=>'1'));
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