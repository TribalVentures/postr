<?php
namespace DB\Bundle\AppBundle\DAO;

use DB\Bundle\CommonBundle\Base\BaseDAO;
use DB\Bundle\AppBundle\Entity\SocialPost;
use DB\Bundle\CommonBundle\Util\DBUtil;
use DB\Bundle\AppBundle\Common\Config;
use DB\Bundle\AppBundle\Entity\TrendingArticle;
use DB\Bundle\AppBundle\Entity\SocialProfile;
use GuzzleHttp\Client;
use Facebook\Facebook;
use Facebook\Authentication\AccessToken;
use Abraham\TwitterOAuth\TwitterOAuth;
use DB\Bundle\AppBundle\Entity\ArticleNotifyHistory;

/**
 * Class For SocialPost DAO, This class is responsible for manage database 
 * operation for SocialPost table/entity
 *
 * @namespace DB\Bundle\AppBundle\DAO
 *
 * @author Dipak Patil
 */
class SocialPostDAO extends BaseDAO { 
	/**
	 * Always need doctrim object to initilise SocialPost dao object
	 * @param $_dm - Doctrime object
	 */
	function __construct($_dm) {
		parent :: __construct($_dm);
	}
	
	/**
	 * This function add new facebook page
	 * @param array $socialPostDetail
	 */
	public function addSocialPost($socialPostDetail = array()) {
		if(empty($socialPostDetail)) {
			return false;
		}
		
		if(empty($socialPostDetail['link'])) {
			$socialPostDetail['link'] = '';
		}
		
		if(empty($socialPostDetail['message'])) {
			$socialPostDetail['message'] = '';
		}
		
		if(empty($socialPostDetail['facebookPostId'])) {
			$socialPostDetail['facebookPostId'] = '0';
		}
		
		if(empty($socialPostDetail['twitterPostId'])) {
			$socialPostDetail['twitterPostId'] = '0';
		}
		
		if(empty($socialPostDetail['postStatus'])) {
			$socialPostDetail['postStatus'] = '0';
		}
		
		if(empty($socialPostDetail['validStatus'])) {
			$socialPostDetail['validStatus'] = '0';
		}
		
		if(empty($socialPostDetail['social'])) {
			$socialPostDetail['social'] = '0';
		}
		
		if(empty($socialPostDetail['trendingArticleId'])) {
			$socialPostDetail['trendingArticleId'] = '0';
		}
		
		if(empty($socialPostDetail['articleNotifyHistoryId'])) {
			$socialPostDetail['articleNotifyHistoryId'] = '0';
		}
		
		if(empty($socialPostDetail['creationDate'])) {
			$socialPostDetail['creationDate'] = new \DateTime();
		}
		
		if(empty($socialPostDetail['lastUpdate'])) {
			$socialPostDetail['lastUpdate'] = new \DateTime();
		}
		
		$socialPost = new SocialPost();
		
		$socialPost->setAccountId($socialPostDetail['accountId']);
		
		$socialPost->setMessage($socialPostDetail['message']);
		$socialPost->setLink($socialPostDetail['link']);
		
		$socialPost->setFacebookPostId($socialPostDetail['facebookPostId']);
		$socialPost->settWitterPostId($socialPostDetail['twitterPostId']);
		
		$socialPost->setPostStatus($socialPostDetail['postStatus']);
		$socialPost->setValidStatus($socialPostDetail['validStatus']);
		$socialPost->setSocial($socialPostDetail['social']);
		
		$socialPost->setTrendingArticleId($socialPostDetail['trendingArticleId']);
		$socialPost->setArticleNotifyHistoryId($socialPostDetail['articleNotifyHistoryId']);
		
		$socialPost->setCreationDate($socialPostDetail['creationDate']);
		$socialPost->setLastUpdate($socialPostDetail['lastUpdate']);
		
		$socialPost = $this->save($socialPost);
		
		$newDetail = false;
		if(is_object($socialPost)) {
			$newDetail = $socialPost->toArray();
		}
		return $newDetail;
	}
	
	/**
	 * This function return social post of account
	 * @param integer $account
	 * @param integer $currentPage
	 */
	public function getSocialPostList($accountId, $currentPage = 1, $validStatus = '', $options = array()) {
		//getTrendingArticleListByNotification($period = 1, $emailNotificationId = '', $categoryList = array(), $noOfDay = 2) {
		$em = $this->getDoctrine()->getManager();
		
		//$from for entity name (table name)
		$from = "DB\\Bundle\\AppBundle\Entity\\SocialPost socialPost ";
		
		$whereCondition = ' socialPost.postStatus = 1 AND socialPost.accountId = ' . $accountId . ' ';
		
		if($validStatus == '1' || $validStatus == '0') {
			$whereCondition .= 'AND socialPost.validStatus = ' . $validStatus . ' ';
		}
		
		if(!empty($options['lastUpdateTime'])) {
			$currentDate = new \DateTime();
			$currentDate->modify('-' . $options['lastUpdateTime'] . ' minutes');
			$todayDate = DBUtil::format($currentDate, 'Y-m-d H:i:s');
			
			$whereCondition .= "AND socialPost.lastUpdate < '" . $todayDate . "' ";
		}
		
		//Get count of record available in table
		$count = $this->getCountByWhere("socialPost.socialPostId", $from, $whereCondition);
		
		//Get paging detail
		$paggingDetails = DBUtil::getPaggingDetails($currentPage, $count, Config::getSParameter('RECORDS_PER_PAGE'));
		
		$sql = "SELECT socialPost.socialPostId, socialPost.accountId, socialPost.message, socialPost.link, socialPost.facebookPostId, " .
				"socialPost.twitterPostId, socialPost.postStatus, socialPost.validStatus, socialPost.social, socialPost.trendingArticleId, " . 
				
				"trendingArticle.url, trendingArticle.url, trendingArticle.title , trendingArticle.image , trendingArticle.description , " .
				"trendingArticle.caption , " . 
				"socialPost.creationDate, socialPost.lastUpdate " .
				"FROM " . $from;
		$sql .= ' JOIN DB\\Bundle\\AppBundle\Entity\\TrendingArticle trendingArticle WITH trendingArticle.trendingArticleId = socialPost.trendingArticleId ';
		if(!empty($whereCondition)) {
			$sql .= " WHERE " . $whereCondition . " ";
		}
		
		$sql .= "ORDER BY socialPost.socialPostId DESC ";
		
		//echo $sql . "\r\n\r\n";
		
		$query = $em->createQuery($sql);
		$result = $query->setFirstResult($paggingDetails['MYSQL_LIMIT1'])->setMaxResults($paggingDetails['MYSQL_LIMIT2']);
		$result = $query->getResult();
		
		//make event type
		if(!empty($result)) {
			for($index = 0; $index < count($result); $index ++) {
				if(!empty($result[$index]['creationDate'])) {
					$result[$index]['creationDateAt'] = $result[$index]['creationDate']->format('M d, Y');
				}
			}
		}
		
		$socialPostList = array();
		$socialPostList['PAGING'] = $paggingDetails;
		$socialPostList['LIST'] = $result;
		
		return $socialPostList;
	}
	
	/**
	 * This function fetch all facebook insight of each post and update as social in to databse
	 */
	public function getPagePostInsights($account = '', $consoleCallStatus = 0) {
		$socialProfileDAO = new SocialProfileDAO($this->getDoctrine());
		
		$criteriaDetail = array();
		if(!empty($account)) {
			$criteriaDetail['accountId'] = $account;
		}
		
		$criteriaDetail['profileType'] = 'Facebook';
		$socialProfileDetailList = $socialProfileDAO->findDetailBy(new SocialProfile(), $criteriaDetail);
		
		if(!empty($socialProfileDetailList)) {
			$socialPostDAO = new SocialPostDAO($this->getDoctrine());
			$socialPostMetricDAO = new SocialPostMetricDAO($this->getDoctrine());
			$client = new Client();
			
			$insightURL = 'https://graph.facebook.com/v2.5/[postId]/insights/post_story_adds_by_action_type_unique,post_impressions/lifetime?access_token=[accessToken]';
			foreach($socialProfileDetailList as $socialProfileDetail) {
				//Get social post for each account 
				$socialPostDetailList = $socialPostDAO->getSocialPostList($socialProfileDetail['accountId'], 1, 0, array('lastUpdateTime'=>'60'));
				if(!empty($socialPostDetailList['LIST'])) {
					foreach($socialPostDetailList['LIST'] as $socialPostDetail) {
						//get facebook insights for each post
						$url = $insightURL;
						$url = str_replace('[postId]', $socialPostDetail['facebookPostId'], $url);
						$url = str_replace('[accessToken]', $socialProfileDetail['accessToken'], $url);
						if($consoleCallStatus) {
							echo $url . "\r\n\r\n";
						}
						
						try {
							$response = $client->get($url, []);
							$result = json_decode($response->getBody(), true);
							if(is_array($result)) {
								$socialPostMetricDetail = array();
								$socialPostMetricDetail['accountId'] = $socialPostDetail['accountId'];
								$socialPostMetricDetail['socialPostId'] = $socialPostDetail['socialPostId'];
								
								$socialPost = new SocialPost();
								$socialPost->setSocialPostId($socialPostDetail['socialPostId']);
								$socialPostParam = array('lastUpdate'=>new \DateTime());
								
								foreach($result['data'] as $data) {
									if(!empty($data['name'])) {
										if($data['name'] == 'post_story_adds_by_action_type_unique') {
											$socialPostMetricDetail['fbLike'] = 0;
											if(isset($data['values'][0]['value']['like'])) {
												$socialPostMetricDetail['fbLike'] = $data['values'][0]['value']['like'];
											}
											
											$socialPostMetricDetail['fbShare'] = 0;
											if(isset($data['values'][0]['value']['share'])) {
												$socialPostMetricDetail['fbShare'] = $data['values'][0]['value']['share'];
											}
											
											$socialPostMetricDetail['fbComment'] = 0;
											if(isset($data['values'][0]['value']['comment'])) {
												$socialPostMetricDetail['fbComment'] = $data['values'][0]['value']['comment'];
											}
										} else if($data['name'] == 'post_impressions') {
											if(isset($data['values'][0]['value'])) {
												$socialPostParam['social'] = $data['values'][0]['value'];
											
												$socialPostMetricDetail['fbSocial'] = $data['values'][0]['value'];
												
												if($consoleCallStatus) {
													echo 'ID: ' . $socialPostDetail['socialPostId'] .', fbId: ' . $socialPostDetail['facebookPostId'] . ', social : ' . $data['values'][0]['value'];
													echo "\r\n\r\n";
												}
											}
										}
									}
								}

								$socialPostDAO->update($socialPost, $socialPostParam);
								$socialPostMetricDAO->manageSocialMetric($socialPostMetricDetail);
							}
						} catch (\Exception $e) {
							if($consoleCallStatus) {
								echo "Exception: " . $e->getMessage() . "\r\n";
							}
							
							$socialPost = new SocialPost();
							$socialPost->setSocialPostId($socialPostDetail['socialPostId']);
							$socialPostDAO->update($socialPost, array('validStatus'=>1));
						}
					}
				}
			}
		}
	}
	
	/**
	 * Thisfunction get the all social post message and post to social accouitns
	 */
	public function postSocialmessage($accountId, $criteria = array()) {
		$socialProfileDAO = new SocialProfileDAO($this->getDoctrine());
		$socialProfileDetailList = $socialProfileDAO->findDetailBy(new SocialProfile(), array('accountId'=>$accountId));
		
		if(empty($socialProfileDetailList)) {
			return array();
		}
		
		$postResponse = array();
		
		$socialPostDAO = new SocialPostDAO($this->getDoctrine());
		$socialPostDetailList = $socialPostDAO->findDetailBy(new SocialPost(), array('accountId'=>$accountId, 'postStatus'=>'0'));
		if(!empty($criteria['socialPost'])) {
			$socialPostDetailList[] = $criteria['socialPost'];
		}
		
		if(!empty($socialPostDetailList)) {
			$facebook = new Facebook([
					'app_id' => Config::getSParameter('POSTREACH_FACEBOOK_APP_ID'),
					'app_secret' => Config::getSParameter('POSTREACH_FACEBOOK_APP_SECRET'),
					'default_graph_version' => Config::getSParameter('POSTREACH_FACEBOOK_DEFAULT_VERSION'),
			]);
			
			foreach($socialPostDetailList as $socialPostDetail) {
				$socialPostResponseDetail = array();
				$socialPostResponseDetail['socialPostId'] = $socialPostDetail['socialPostId'];
				$socialPostResponseDetail['trendingArticleId'] = $socialPostDetail['trendingArticleId'];
				
				$isFbExecute = true;
				$isTwExecute = true;
				
				if(!empty($criteria['validSocial'])) {
					$isFbExecute = false;
					$isTwExecute = false;
					
					$isFbFound = false;
					$isTwFound = false;
					foreach($criteria['validSocial'] as $validSocialDetail) {
						if($validSocialDetail['trendingArticleId'] == $socialPostDetail['trendingArticleId']) {
							if('Facebook' == $validSocialDetail['profileType']) {
								$isFbExecute = true;
								$isFbFound = true;
							}
							
							if('Twitter' == $validSocialDetail['profileType']) {
								$isTwExecute = true;
								$isTwFound = true;
							}
						}
					}
					
					if($isFbFound == false && $isTwFound == false) {
						$isFbExecute = true;
						$isTwExecute = true;
					}
				}
				
				foreach($socialProfileDetailList as $socialProfileDetail) {
					if('Facebook' == $socialProfileDetail['profileType'] && $isFbExecute == true && empty($socialPostDetail['facebookPostId'])) {
						try {
							//save id to DB
							$socialPost = new SocialPost();
							$socialPost->setSocialPostId($socialPostDetail['socialPostId']);
							
							$socialPostResponseDetail['fbStatus'] = false;
							
							//Check access token expiry date
							if(Config::POSTREACH_FACEBOOK_PROFILE_CATEGORY == $socialProfileDetail['category']) {
								$accessToken = new AccessToken($socialProfileDetail['accessToken'], Config::getSParameter('POSTREACH_FACEBOOK_APP_ID'), $socialProfileDetail['socialId']);
								if($accessToken->isExpired()) {
									//set message to process, So taht wil never post again
									$socialPostDAO->update($socialPost, array('postStatus'=>1));
									continue;
								}
							}
							$params = array();
							
							if(!empty($socialPostDetail['message'])) {
								$params['message'] = $socialPostDetail['message'];
							}
								
							if(!empty($socialPostDetail['link'])) {
								$params['link'] = $socialPostDetail['link'];
							}
							
							if(!empty($params)) {
								$response = $facebook->post('/'.$socialProfileDetail['socialId'].'/feed', $params, $socialProfileDetail['accessToken']);
								
								$graphObject = $response->getGraphObject();
								$graphObject = $graphObject->asArray();
									
								if(!empty($graphObject['id'])) {
									$socialPostResponseDetail['fbStatus'] = true;
									$socialPostDAO->update($socialPost, array('facebookPostId'=>$graphObject['id'], 'postStatus'=>1));
								} else {
									$socialPostDAO->update($socialPost, array('postStatus'=>1));
								}
							} else {
								$socialPostDAO->update($socialPost, array('postStatus'=>1));
							}
							
						} catch(Exception $e) {
							//echo "Error haivng posting : ";
							//echo $e->getMessage();
						}
					} else if('Twitter' == $socialProfileDetail['profileType'] && $isTwExecute == true && empty($socialPostDetail['twitterPostId'])) {
						$socialPostResponseDetail['twStatus'] = false;
						
						//If message and link both are empty then do not post message and make that message status to process
						if(empty($socialPostDetail['message']) && empty($socialPostDetail['link'])) {
							$socialPost = new SocialPost();
							$socialPost->setSocialPostId($socialPostDetail['socialPostId']);
							$socialPostDAO->update($socialPost, array('postStatus'=>1));
							continue;
						}
						
						$tw = new TwitterOAuth(Config::getSParameter('POSTREACH_TWITTER_API_KEY'), Config::getSParameter('POSTREACH_TWITTER_API_SECRET'), $socialProfileDetail['oauthToken'], $socialProfileDetail['oauthTokenSecret']);
						$account = $tw->get('account/verify_credentials');
						
						if(empty($socialPostDetail['link'])) {
							$socialPostDetail['link'] = '';
						}
						
						$response = $tw->post('statuses/update', array('status' => $socialPostDetail['message'] . ' ' . $socialPostDetail['link']));
						
						if(isset($response) && is_object($response)) {
							if(property_exists($response, 'id')) {
								//save id to DB
								$socialPost = new SocialPost();
								$socialPost->setSocialPostId($socialPostDetail['socialPostId']);
								if(!empty($response->id)) {
									$socialPostResponseDetail['twStatus'] = true;
									$socialPostDAO->update($socialPost, array('twitterPostId'=>$response->id, 'postStatus'=>1));
								} else {
									$socialPostDAO->update($socialPost, array('postStatus'=>1));
								}
							}
						}
					} else {
						$socialPost = new SocialPost();
						$socialPost->setSocialPostId($socialPostDetail['socialPostId']);
						$socialPostDAO->update($socialPost, array('postStatus'=>1));
					}
				}
				
				$postResponse[] = $socialPostResponseDetail;
			}
		}
		
		return $postResponse;
	}
	
	/**
	 * This function will post article to user social profiles
	 * @param integer $accountId
	 * @param mixed $articleDetail
	 */
	public function shareTrendingArticle($accountId, $articleDetail) {
		$socialPostId = '';
		$socialPostDetail = array();
		$socialPostDetail['accountId'] = $accountId;
		
		//Send message to facebook
		$socialPostDetail['message'] = '';
		if(!empty($articleDetail['caption'])) {
			$socialPostDetail['message'] = $articleDetail['caption'];
		}
		$socialPostDetail['link'] = '';
		if(!empty($articleDetail['url'])) {
			$socialPostDetail['link'] = $articleDetail['url'];
		}
		
		$socialPostDetail['trendingArticleId'] = '';
		if(!empty($articleDetail['trendingArticleId'])) {
			$socialPostDetail['trendingArticleId'] = $articleDetail['trendingArticleId'];
		}
		
		if(!empty($socialPostDetail['message']) || !empty($socialPostDetail['link'])) {
			$existingSocialPostDetail = $this->findSingleDetailBy(new SocialPost(), array("message"=>$socialPostDetail['message'], "link"=>$socialPostDetail['link']));
			
			if(empty($existingSocialPostDetail)) {
				$socialPostDetail = $this->addSocialPost($socialPostDetail);
				if(!empty($socialPostDetail['socialPostId'])) {
					$socialPostId = $socialPostDetail['socialPostId'];
				}
				
				//Add article in article notification history
				$articleNotifyHistoryDAO = new ArticleNotifyHistoryDAO($this->getDoctrine());
				
				$articleNotifyHistoryDetail = array();
				$articleNotifyHistoryDetail['accountId'] = $accountId;
				$articleNotifyHistoryDetail['trendingArticleId'] = $socialPostDetail['trendingArticleId'];
				$articleNotifyHistoryDetail['notifyType'] = ArticleNotifyHistory::NOTIFY_TYPE_AUTOPOST;
				
				$articleNotifyHistoryDAO->addArticleNotifyHistory($articleNotifyHistoryDetail);
			}
		}
		
		$this->postSocialmessage($accountId);
		
		return $socialPostId;
	}
	
	/**
	 * This functon return the total social for account
	 * @param integer $accountId
	 * @return integer
	 */
	public function getTotalSocialPost($accountId, $date = '') {
		$response = array('totalPost'=>0, 'totalSocial'=>0);
		try {
			$em = $this->getDoctrine()->getManager();
				
			$sql =  "SELECT COUNT(socialPost.socialPostId) as totalPost, SUM(socialPost.social) as totalSocial " .
					" FROM DB\\Bundle\\AppBundle\Entity\\SocialPost socialPost ".
					' WHERE socialPost.postStatus = 1 AND socialPost.accountId = ' . $accountId . ' ';
			
			if(!empty($date)) {
				$sql .= "AND socialPost.creationDate >= '" . $date . "' ";
			}
			//echo $sql;
				
			$query = $em->createQuery($sql);
			$result = $query->getResult();
				
			if(is_array($result) && count($result) > 0) {
				$response['totalPost'] = $result[0]['totalPost'];
				$response['totalSocial'] = $result[0]['totalSocial'];
			}
				
		} catch (Exception $e) {
		}
		
		return $response;
	}
}
?>