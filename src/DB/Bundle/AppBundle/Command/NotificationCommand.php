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

class NotificationCommand extends ContainerAwareCommand {
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
		$this->setName('primo:notification')
		->setDescription('Process emails')
		->addArgument('score',InputArgument::OPTIONAL,'Velocity')
		->addOption('prod', null, InputOption::VALUE_NONE, 'Morning mails')
		->addOption('ArticleOnly', null, InputOption::VALUE_NONE, 'This option allow to fetch article only from spike')
		->addOption('SendNotification', null, InputOption::VALUE_NONE, 'This option will send email')
		->addOption('AutoPost', null, InputOption::VALUE_NONE, 'This option will post articles to there pages')
		;
	}
	
	/**
	 * This function return the score
	 */
	private function getScore() {
		return $this->score;
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
		
		$this->score = $input->getArgument('score');
		if(empty($this->score)) {
			$this->score = 60;
		}
		
		if ($input->getOption('prod')) {
			$this->url = $this->getContainer()->getParameter('host_url');
			$this->addInResponse('url', $this->url);
		}
		
		if($input->getOption('ArticleOnly')) {
			$this->log('Start process to fetch spike articles');
			$this->processArticle();
		} else if($input->getOption('SendNotification')) {
			$this->log('Start to send article');
			$this->sendNotification();
		} else if($input->getOption('AutoPost')) {
			$this->log('Start to send article');
			$this->autoPost();
		}
	}
	
	/**
	 * This function will post article to users profile
	 */
	private function autoPost() {
		$accountDAO = new AccountDAO($this->getDoctrine());
		$accountList = $accountDAO->getAccountForAutopost();
		
		$todayDay = strtolower(DBUtil::format(new \DateTime(), 'l'));
		$this->log("Start autopost for day : " . $todayDay . "\r\n");
		
		if(!empty($accountList)) {
			$trendingArticleDAO = new TrendingArticleDAO($this->getDoctrine());
			$accountFrequencyDAO = new AccountFrequencyDAO($this->getDoctrine());
			foreach($accountList as $account) {
				$this->log("[Account Id : " . $account['accountId'] . "] Start Process \r\n");
				$accountFrequencyDetail = $accountFrequencyDAO->getAccountFrequencyDetail($account['accountId'], array('category'=>'Autopilot'));
				if(!empty($accountFrequencyDetail)) {
					$accountFrequencyDetail = $accountFrequencyDAO->setFrequencyDetail($accountFrequencyDetail);
					
					if(isset($accountFrequencyDetail[$todayDay]) && $accountFrequencyDetail[$todayDay] == '1') {
						$trendingArticleList = $trendingArticleDAO->getTrendingArticleListByAccount($account['accountId'], 1, Config::getSParameter('TRENDING_ARTICLE_NO_OF_VALID_DAY'));
							
						if(!empty($trendingArticleList["LIST"])) {
							$this->log("[Account Id : " . $account['accountId'] . "] Start to autopost \r\n");
							$socialPostDAO = new SocialPostDAO($this->getDoctrine());
							
							$response = $socialPostDAO->shareTrendingArticle($account['accountId'], $trendingArticleList["LIST"][0]);
							if(!empty($response['socialPostId'])) {
								$this->sendPostNotification($response['socialPostId'], $response['isException']);
							} 
						} else {
							$this->log("[Account Id : " . $account['accountId'] . "] No any article found \r\n");
						}
					} else {
						$this->log("[Account Id : " . $account['accountId'] . "] is not for " . $todayDay . " \r\n");
					}
				} else {
					$this->log("[Account Id : " . $account['accountId'] . "] No account frequency found \r\n");
				}
			}
		} else {
			$this->log("No any account to Auto post to profile\r\n");
		}
	}

	/**
	 * This function send post notification to user
	 * @param integer $socialPostId
	 */
	public function sendPostNotification($socialPostId, $isException = false) {
		if(!isset($socialPostId)) {
			return false;
		}
	
		$socialPostDAO = new SocialPostDAO($this->getDoctrine());
		$socialPostDetail = $socialPostDAO->findSingleDetailBy(new SocialPost(), array('socialPostId'=>$socialPostId));
		
			
		if(!empty($socialPostDetail['creationDate'])) {
			$socialPostDetail['creationDateAt'] = DBUtil::format($socialPostDetail['creationDate'], 'M d, Y');
				
			$this->addInResponse('socialPostDetail', $socialPostDetail);
				
			$userDAO = new UserDAO($this->getDoctrine());
			$userDetail = $userDAO->findSingleDetailBy(new User(), array('accountId'=>$socialPostDetail['accountId']));
			
			if(empty($userDetail)) {
				return false;
			}
			
			// get article detail
			$trendingArticleDAO = new TrendingArticleDAO($this->getDoctrine());
			$trendingArticleDetail = $trendingArticleDAO->findSingleDetailBy(new TrendingArticle(), array('trendingArticleId'=>$socialPostDetail['trendingArticleId']));
			if(!empty($trendingArticleDetail)) {
				$trendingArticleDetail['domain'] = DBUtil::getDomain($trendingArticleDetail['url']);
				$this->addInResponse('trendingArticleDetail', $trendingArticleDetail);
			}
				
			$socialProfileDAO = new SocialProfileDAO($this->getDoctrine());
			$fbSocialProfileDetail = $socialProfileDAO->findSingleDetailBy(new SocialProfile(), array('profileType'=>'Facebook', 'accountId'=>$socialPostDetail['accountId']));
				
			$postDetail = array();
			if(!empty($fbSocialProfileDetail['picture'])) {
				$postDetail['profileImage'] = $fbSocialProfileDetail['picture'];
				$postDetail['name'] = $fbSocialProfileDetail['name'];
			} else {
				$twSocialProfileDetail = $socialProfileDAO->findSingleDetailBy(new SocialProfile(), array('profileType'=>'Twitter', 'accountId'=>$socialPostDetail['accountId']));
					
				if(!empty($twSocialProfileDetail['picture'])) {
					$postDetail['profileImage'] = $twSocialProfileDetail['picture'];
					$postDetail['name'] = $twSocialProfileDetail['name'];
				}
			}
			
			$this->addInResponse('postDetail', $postDetail);
			if($isException == true) {
				$this->addInResponse('isException', $isException);
			}
			$html = $this->renderView('DBAppBundle:email:post-notification-email.html.twig', $this->getResponse());
			
			$emailDetail = array();
			
			$emailDetail['from'] = Config::getSParameter('FROM_EMAIL');
			$emailDetail['to'] = $userDetail['email'];
			$emailDetail['bcc'] = array(Config::getSParameter('BCC_EMAIL'));
			$emailDetail['subject'] = 'InteriorPostr: Your Post has been Shared';
			
			$emailDetail['body'] = $html;
			
			$dbSendgridClient = new DBSendgridClient(Config::getSParameter('SENDGRID_API_KEY_GENERATE_TOKEN'));
			$dbSendgridClient->sendMail($emailDetail);
		}
	}
	
	/**
	 * This functon fetch spike article and put into DB
	 */
	private function processArticle() {
		//Get account category
		$categoryDAO = new CategoryDAO($this->getDoctrine());
		$categoryDetailList = $categoryDAO->getAccountCategoryList();
		
		$trendingArticleCategoryDAO = new TrendingArticleCategoryDAO($this->getDoctrine());
		$categoryArticleCountMap = $trendingArticleCategoryDAO->getCategoryArticleCountMap();
		
		if(!empty($categoryDetailList)) {
			$trendingArticleNoOfRecords = Config::getSParameter('TRENDING_ARTICLE_NO_OF_RECORDS');
			
			$dbNewsWhipClient = new DBNewsWhipClient(Config::getSParameter('SPIKE_API_KEY'));
			foreach($categoryDetailList as $category) {
				$spikeCalStatus = true;
				if(isset($categoryArticleCountMap[$category['categoryId']])) {
					if($categoryArticleCountMap[$category['categoryId']] > $trendingArticleNoOfRecords) {
						$this->log($category['category'] . ' is already sufficient articles: ' . $categoryArticleCountMap[$category['categoryId']] . "\r\n\r\n");
						$spikeCalStatus = false;
					}
				}
				
				if($spikeCalStatus) {
					$criteria = $categoryDAO->getSpikeCriteriaBYCategory(3, $category);
					$articleList = $dbNewsWhipClient->getArticleByCriteriaV1($criteria);
					
					$this->log($category['category'] . ' = Count : ' . count($articleList) . ' = ' . json_encode($criteria) . "\r\n\r\n");
					
					if(!empty($articleList)) {
						$trendingArticleDAO = new TrendingArticleDAO($this->getDoctrine());
						$trendingArticleMap = $trendingArticleDAO->getTredingArtcleByPostId($articleList);
						
						$index = 0;
						$newArticleList = array();
						$newArticleCategoryMap = array();
						$existingList = array();
						foreach($articleList as $article) {
							if(isset($trendingArticleMap[$article['postId']])) {
								$existingArticle = $trendingArticleMap[$article['postId']];
								if(isset($existingArticle['trendingArticleId'])) {
									$existingArticle['score'] = $article['velocity'];
									$existingList[] = $existingArticle;
								}
								
								continue;
							}
							
							$trendingArticleCategory = new TrendingArticleCategory();
							$trendingArticleCategory->setScore($article['velocity']);
							$trendingArticleCategory->setCategoryId($category['categoryId']);
							$newArticleCategoryMap[$article['postId']] = $trendingArticleCategory;
							
							$trendingArticleMap[$article['postId']] = array('postId'=>$article['postId']);
								
							$article['score'] = $article['velocity'];
							$article['trendingArticleStatus'] = 0;
								
							$article['category'] = trim(strtolower($category['category']));;
								
							$article = $trendingArticleDAO->addTrendingArticle($article, false);
							if(is_object($article)) {
								$newArticleList[] = $article;
							}
					
							if($index > $trendingArticleNoOfRecords) {
								break;
							} else {
								$index ++;
							}
						}
					
						//Save in batch
						if(!empty($newArticleList)) {
							$newArticleList = $trendingArticleDAO->saveBatch($newArticleList);
							if(!empty($newArticleList)) {
								$newArticleCategoryList = array();
								foreach($newArticleList as $trendingArticle) {
									if(isset($newArticleCategoryMap[$trendingArticle->getPostId()])) {
										$newArticleCategoryMap[$trendingArticle->getPostId()]->setTrendingArticleId($trendingArticle->getTrendingArticleId());
										$newArticleCategoryList[] = $newArticleCategoryMap[$trendingArticle->getPostId()];
									}
								}
								
								$trendingArticleCategoryDAO->saveBatch($newArticleCategoryList);
							}
						}

						//Update article in article category table
						if(!empty($existingList)) {
							$articleCategoryMap = $trendingArticleCategoryDAO->getCategoryArticleMap($category['categoryId'], $existingList);
							$articleCategoryList = array();
							foreach($existingList as $article) {
								//Cehck if already exist
								if(isset($articleCategoryMap[$article['trendingArticleId'] . '_' . $category['categoryId']])) {
									continue;
								}
								
								$trendingArticleCategory = new TrendingArticleCategory();
								$trendingArticleCategory->setScore($article['score']);
								$trendingArticleCategory->setCategoryId($category['categoryId']);
								$trendingArticleCategory->setTrendingArticleId($article['trendingArticleId']);
								 
								$articleCategoryList[] = $trendingArticleCategory; 
								
								$this->log($category['category'] . ' is assign to article id : ' . $article['trendingArticleId'] . "\r\n\r\n");
								
								if($index > $trendingArticleNoOfRecords) {
									break;
								} else {
									$index ++;
								}
							}
							
							$trendingArticleCategoryDAO->saveBatch($articleCategoryList);
						}
					}
				} else {
					//No logic 
				}
			}
		}
	}
	
	/**
	 * This function send newsletter and messenger and put in notify history
	 */
	private function sendNotification() {
		$accountDAO = new AccountDAO($this->getDoctrine());
		$accountList = $accountDAO->getAccountForNotification();
		
		$notificationSettingsDAO = new NotificationSettingsDAO($this->getDoctrine());
		if(!empty($accountList)) {
			$trendingArticleDAO = new TrendingArticleDAO($this->getDoctrine());
			$userDAO = new UserDAO($this->getDoctrine());
			
			foreach($accountList as $account) {
				$this->log("[Account Id : " . $account['accountId'] . "] Start Process \r\n");
				$notificationSettingList = $notificationSettingsDAO->findDetailBy(new NotificationSettings(), array('accountId'=>$account['accountId']));
				$userDetail = $userDAO->findSingleDetailBy(new User(), array('accountId'=>$account['accountId']));
				
				if(!empty($notificationSettingList)) {
					$trendingArticleList = $trendingArticleDAO->getTrendingArticleListByAccount($account['accountId'], 1, 0, Config::getSParameter('RECORDS_PER_EMAIL'));
					
					if(!empty($trendingArticleList["LIST"])) {
						foreach($notificationSettingList as $notificationSetting) {
							if($notificationSetting['notifyType'] == NotificationSettings::NOTIFY_TYPE_MESSENGER) {
								$this->sendMessengerNotification($userDetail, $trendingArticleList);
							} else if($notificationSetting['notifyType'] == NotificationSettings::NOTIFY_TYPE_EMAIL) {
								$this->sendEmailNotification($userDetail, $trendingArticleList);
							} else {
								$this->log("[Account Id : " . $account['accountId'] . "] No any notify type found \r\n");
							}
						}
					} else {
						$this->log("[Account Id : " . $account['accountId'] . "] No any article list found found \r\n");
					}
				} else {
					$this->log("[Account Id : " . $account['accountId'] . "] No any notificationSettingList found \r\n");
				}
				$this->log("[Account Id : " . $account['accountId'] . "] End Process ------------------------- \r\n");
			}
		} else {
			$this->log("No any account to send email\r\n");
		}
	}
	
	/**
	 * This function send the notification emails to users
	 * @param mixed $userDetail
	 * @param mixed $trendingArticleList
	 */
	private function sendEmailNotification($userDetail, $trendingArticleList) {
		//Get user social setting
		$socialProfileDAO = new SocialProfileDAO($this->getDoctrine());
		$fbSocialProfileDetail = $socialProfileDAO->findSingleDetailBy(new SocialProfile(), array('profileType'=>'Facebook', 'accountId'=>$userDetail['accountId']));
		if(!empty($fbSocialProfileDetail)) {
			$this->addInResponse('fbSocialProfileDetail', $fbSocialProfileDetail);
		}
		
		$twSocialProfileDetail = $socialProfileDAO->findSingleDetailBy(new SocialProfile(), array('profileType'=>'Twitter', 'accountId'=>$userDetail['accountId']));
		if(!empty($twSocialProfileDetail)) {
			$this->addInResponse('twSocialProfileDetail', $twSocialProfileDetail);
		}
		
		$this->addInResponse('userDetail', $userDetail);
		$this->addInResponse('trendingArticleList', $trendingArticleList["LIST"]);
		$html = $this->renderView('DBAppBundle:email:notification-email.html.twig', $this->getResponse());
		
		$emailDetail = array();
		
		$emailDetail['from'] = Config::getSParameter('FROM_EMAIL');
		$emailDetail['to'] = $userDetail['email'];
		$emailDetail['bcc'] = array(Config::getSParameter('BCC_EMAIL'));
		$emailDetail['subject'] = 'InteriorPostr: Recommended Posts';
		
		$emailDetail['body'] = $html;
		
		$dbSendgridClient = new DBSendgridClient(Config::getSParameter('SENDGRID_API_KEY_GENERATE_TOKEN'));
		$response = $dbSendgridClient->sendMail($emailDetail);
		
		if(is_object($response)) {
			if(isset($response->body)) {
				if(isset($response->body['message']) && $response->body['message'] == 'success') {
					$this->log("[Account Id : " . $userDetail['accountId'] . "] Email send successfully: " . " \r\n");
					//Remove all article form nottification
					$articleNotifyHistoryDAO = new ArticleNotifyHistoryDAO($this->getDoctrine());
					$articleNotifyHistoryDAO->addEmailArticleNotifyHistory($userDetail['accountId'], $trendingArticleList["LIST"]);
				}
			} else {
				$this->log("[Account Id : " . $userDetail['accountId'] . "] Problem from sendgrid: " . " \r\n");
			}
		}
		
		//Add account in send email history
		$EmailHistoryDAO = new EmailHistoryDAO($this->getDoctrine());
		$EmailHistoryDAO->addEmailHistory(array('accountId'=>$userDetail['accountId']));
	}
	
	/**
	 * This function send messenger notification to users
	 * @param string $userDetail
	 * @param string $trendingArticleList
	 */
	private function sendMessengerNotification($userDetail, $trendingArticleList) {
		if(!empty($userDetail['senderId'])) {
			$response = $this->postMassengerNotification($userDetail, array('LIST'=>$trendingArticleList));
			if(!empty($response) && !isset($response['error']) && isset($response['recipient_id'])) {
				if(method_exists($this,'log')) {
					$logText = 'Sending  notification in messegner to user: ' . $userDetail['senderId'];
					$this->log($logText);
				}
			}
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
    
    /**
     * This function send message to facebook massenger
     * @param mixed[] $userDetail
     * @param mixed[] $articleList
     */
    public function postMassengerNotification($userDetail, $articleList) {
    	$bot = new FbBotApp(Config::getSParameter('POSTREACH_BOT_PAGE_ACCESS_TOKEN', ''));
    	echo "Sending to BOT : " . $userDetail['senderId'];
    	
    	$message = 'Here are suggested trending stories';
    	BotHelper::sendMessage($bot, $userDetail, $message);
    	
    	BotHelper::sendTrendignArticle($bot, $userDetail, $articleList);
    	
    	return array();
    }
    
    /**
     * This function return 5 article 3 button temaplte
     * @param array $articleList
     */
    public function getMessangerTemaplte($articleList = array()) {
    	if(empty($articleList)) {
    		return $articleList;
    	}
    	
    	$elements = array();
    	
    	if(!empty($articleList)) {
    		$index = 0;
    		foreach($articleList as $article) {
    			if($index < 4) {
    				$index ++;
    			} else {
    				break;
    			}
    			$buttons = array();
    			
    			$firstButton = array();
    			$firstButton["type"] = "web_url";
    			$firstButton["url"] = $article["url"];
    			$firstButton["title"] = "Read Story";
    			
    			$buttons[] = $firstButton;
    			
    			$operation = array();
    			$operation["operation"] = "handleCaptionMessage";
    			$operation["data"] = array();
    			$operation["data"]["url"] = $article["url"];
    					
    			$secondButton = array();
    			$secondButton["type"] = "postback";
    			$secondButton["title"] = "Write A Caption";
    			$secondButton["payload"] = json_encode($operation, true);
    			
    			$buttons[] = $secondButton;
    			
    			$thirdButton = array();
    			$thirdButton["type"] = "postback";
    			$thirdButton["title"] = "Share";
    			$thirdButton["payload"] = "#" . $article["url"];
    			
    			$buttons[] =  $thirdButton;
    			
    			$element = array();
    			$element["title"] = $article["title"];
    			$element["image_url"] = $article["image"];
    			$element["subtitle"] = $article["description"];
    			$element["buttons"] = $buttons;
    			
    			$elements[] = $element;
    		}
    	}
    	
    	$template = array();
    	$template['attachment'] = array();
    	$template["attachment"]["type"] = "template";
    	$template["attachment"]["payload"] = array();
    	$template["attachment"]["payload"]["template_type"] = "generic";
    	$template["attachment"]["payload"]["elements"] = $elements;
    	
    	return $template;
    }
}
?>