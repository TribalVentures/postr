<?php

namespace DB\Bundle\AppBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use DB\Bundle\AppBundle\DAO\SocialPostDAO;

class FacebookCommand extends ContainerAwareCommand {
	const DEBUG_INFO = 'INFO';
	CONST DEBUG_ERROR = 'ERROR';
	
	const NO_OF_RECORDS = 60;
	
	private $output = null;
	
	private $_response = array();
	
	protected function configure() {
		$this->setName('postr:facebook')
		->setDescription('Process facebook post and insights')
		->addArgument('action',InputArgument::OPTIONAL,'What action you are sending to command')
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
		
		$action = $input->getArgument('action');
		if(empty($action)) {
			$action = 'insights';
		}
		
		if($action == 'insights') {
			$socialPostDAO = new SocialPostDAO($this->getDoctrine());
			$socialPostDAO->getPagePostInsights('', 1);
		}
	}
	
	/**
	 * This function process the insights for social post
	 */
	private function processPostInsight() {
		
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