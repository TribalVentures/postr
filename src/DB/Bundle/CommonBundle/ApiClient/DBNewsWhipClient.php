<?php
namespace DB\Bundle\CommonBundle\ApiClient;

use GuzzleHttp\Client;
use DB\Bundle\AppBundle\Common\Config;

/**
* NewsWhip
* 
* http://www.newswhip.com
*/
class DBNewsWhipClient {
	/**
	 * NewsWhip API key
	 * http://www.newswhip.com/my_api_key.html
	 * @var string
	 */
	private $apiKey = '';
	
	private $spikeApiCall = true;
	private $spikeResponseSave = false;

	/**
	 * NewsWhip API URL
	 * @var string
	 * @access private
	 */
	private $apiURL = 'https://api.newswhip.com/';
	
	public function __construct($apiKey = null) {
		$this->apiKey = $apiKey;
		
		//get default config
		$this->spikeApiCall 	 = Config::getSParameter('SPIKE_API_CALL');
		$this->spikeResponseSave = Config::getSParameter('SPIKE_RESPONSE_SAVE');
	}
	
	/**
	 * This function return all articles of given region
	 * @param unknown $type
	 * @param unknown $category
	 * @param unknown $time
	 * @return string
	 */
	public function getRegion($regionName, $category, $time) {
		$client = new Client();
		$url = $this->apiURL . 'v1/region/' . $regionName . '/' . $category . '/'. $time;
		$response = $client->get($url . '?key=gmShp4csuq6sp', []);
		return json_decode($response->getBody(), true);
	}
	
	
	public function getTopTrend($regionName, $category, $time) {
		$client = new Client();
		$url = $this->apiURL . 'v1/region/' . $regionName . '/' . $category . '/'. $time;
		$response = $client->get($url . '?key=gmShp4csuq6sp', []);
		$result = json_decode($response->getBody(), true);
		
		$totalArticles = count($result['articles']);
		$social = 0;
		
		$article = array();
		$articleList = array();
		
		for($index=0; $index < $totalArticles; $index ++) {
			$article['url'] = $result['articles'][$index]['link'];
			$article ['title'] = $result ['articles'] [$index] ['headline'];
			$article ['description'] = $result ['articles'] [$index] ['excerpt'];
			$article ['image'] = $result ['articles'] [$index] ['image_link'];
			
			$keywords = "";
			$totalKeywords = count($result['articles'][$index]['topics']);
			if ($totalKeywords > 1) {
				
				if (isset($result['articles'][$index]['topics'][0]['name']) && isset($result['articles'][$index]['topics'][1]['name']) ) {
					$keywords = $result['articles'][$index]['topics'][0]['name'] . ", " . $result['articles'][$index]['topics'][1]['name'];
				}
				
			} else {
				
				if (isset($result['articles'][$index]['topics'][0]['name'])) {
					$keywords = $result['articles'][$index]['topics'][0]['name'];
				}
				
			}
			
			$article ['keyword'] = $keywords;
			
			$fbSocial = 0;
			if(!empty($article['fb_data']['total_engagement_count'])) {
				$fbSocial = $fbSocial + $article['fb_data']['total_engagement_count'];
			}
			/* $commentCountFB = $article['fb_data']['comment_count'];
			$likeCountFB = $article['fb_data']['like_count'];
			$shareCountFB = $article['fb_data']['share_count'];*/
			$tweetCountTW = $result['articles'][$index]['tw_data']['tw_count'];
			$linkCountLI = $result['articles'][$index]['li_data']['li_count'];
			
			$social = $fbSocial + $tweetCountTW +  $linkCountLI;
			
			$article ['facebookSocial'] = $fbSocial;
			$article ['twitterSocial'] = $tweetCountTW;
			
			$newScrore = $result['articles'][$index]['nw_score'];
			$maxScrore = $result['articles'][$index]['max_nw_score'];
			
			$article ['velocity'] = $this->getVelocity($social, $newScrore, $maxScrore );
			
			$articleList [$index] = $article;
			
		}
		
		return $articleList;
	}
	
	/**
	 * 
	 * @param unknown $keywords
	 * @param unknown $hour
	 * @return \Btit\Bundle\CommonBundle\ApiClient\$list
	 */
	public function getArticle($keywords, $hour, $size, $isSort = true) {
		$response = array();

		if(!empty($keywords)) {
			$url = $this->apiURL . 'v1/articles?key='. $this->apiKey;
		
			$criteria = array();
			$criteria['language'] = 'en';
			$criteria['from'] = ((round(microtime(true) * 1000)) - ($hour * 60 * 60 * 1000));
			$criteria['sort_by'] = 'fb_total_engagement';
			$criteria['find_related'] = false;
			$criteria['video_only'] = false;
			
			$criteria['size'] = intval($size);
			
			$keywords = str_replace(',', " AND ", $keywords);
			
			if(!empty($keywords)) {
				$criteria['filters'] = array($keywords);
			} else {
				$criteria['filters'] = array('""');
			}
			/* $client = new Client();
			$response = $client->post($url, [
					'headers' => ['Content-Type' => 'application/json'],
					'body' => json_encode($criteria)
					
			]);
			$response = $response->getBody();*/
			
			//echo "Criteri : " . json_encode($criteria) , "\r\n\r\n";
			$response = '{}';
			if($this->spikeApiCall) {
				$client = new Client();
				$response = $client->post($url, [
						'headers' => ['Content-Type' => 'application/json'],
						'body' => json_encode($criteria)
				]);
				$response = $response->getBody();
				if($this->spikeResponseSave) {
					$this->write('getArticle', $response);
				}
			} else {
				$response = $this->read('getArticle');
			}
			
			$response =  json_decode($response, true);
		} else {
			$regionName = "U.S.";
			$category = "All";

			$url = $this->apiURL . 'v1/region/' . $regionName . '/' . $category . '/'. $hour;

			/* $client = new Client();
			$response = $client->get($url . '?key=gmShp4csuq6sp', []);
			$response = json_decode($response->getBody(), true); */
			
			$response = '{}';
			if($this->spikeApiCall) {
				$client = new Client();
				$response = $client->get($url . '?key=gmShp4csuq6sp', []);
				//$response = json_decode($response->getBody(), true);
				$response = $response->getBody();
				
				if($this->spikeResponseSave) {
					$this->write('getArticle', $response);
				}
			} else {
				$response = $this->read('getArticle');
			}
			$response = json_decode($response, true);
		}
		
		//print_r($response);
		//exit();
		$postList = array();
		
		if(!empty($response['articles'])) {
			$index = 0;
			//$sentiment = new SentimentAnalysisClient();
			$text = "";
			foreach($response['articles'] as $article) {
				$record = array();
				$record['postId'] = $article['uuid'];
				$record['accountId'] = '1';
				
				$record['url'] = $article['link'];
				$record ['title'] = $article['headline'];
				$record ['description'] = $article['excerpt'];
				$record ['keywords'] = $article['keywords'];
				$record ['sentiment'] = '0';
				
				//Add topic
				$record ['topics'] = $article['topics'];
				
				$record ['publisher'] = $article['source']['publisher'];
				$record ['image'] = $article['image_link'];
				
				$fbSocial = 0;
				if(!empty($article['fb_data']['total_engagement_count'])) {
					$fbSocial = $fbSocial + $article['fb_data']['total_engagement_count'];
				}
				/* $commentCountFB = $article['fb_data']['comment_count'];
				 $likeCountFB = $article['fb_data']['like_count'];
				 $shareCountFB = $article['fb_data']['share_count'];*/
				$tweetCountTW = $article['tw_data']['tw_count'];
				$linkCountLI = $article['li_data']['li_count'];
					
				$social = $fbSocial + $tweetCountTW +  $linkCountLI;
					
				$newScrore = $article['nw_score'];
				$maxScrore = $article['max_nw_score'];
				
				$record['facebookSocial'] = $fbSocial;
				$record['twitterSocial'] = $tweetCountTW;
				
				$record['totalSocial'] = $social;
				$record ['velocity'] = $this->getVelocity($social, $newScrore, $maxScrore );
				
				$record ['creationDate'] = new \DateTime();
				
				$text = $record ['title'] ;
				
// 				$text = preg_replace('/[\x00-\x1F\x80-\xFF]/', '', $text);
// 				$text = str_replace('"', '', $text);
				//echo $text;
				 
// 				$record['sentiment'] = $sentiment->getSentiment($text);
			    /* $record['sentiment'] = rand(0, 1) / 10;
 				$index ++;
				
				if($index % 4 == 0 || $index % 5 == 0 || $index % 6 == 0) {
					$record['sentiment'] = (rand(0, 1) / 10) * -1;
				} */
				
				$record['totalImpression'] = '0';
				
				$record['projectionChartDetail'] = $this->getProjectionChartDetail($article);
				$record['publicationTimestamp'] = $article['publication_timestamp'];
				$record['publicationDate'] = date('Y-m-d H:i:s', substr($article['publication_timestamp'], 0, 10));
				
				$postList[] = $record;
			}
			//echo count($postList);
		}		
		
		if($isSort == true) {
			$postList = $this->sortObjectList($postList, 'velocity');
		}
		
		return $postList;
	}
	


	/**
	 * This function return all spike article by criteria
	 * @param array $criteria
	 */
	public function getArticleByCriteriaV1($criteria, $isSort = true) {
		$response = array();
	
		if(!empty($criteria)) {
			$url = $this->apiURL . 'v1/articles?key='. $this->apiKey;
			
			//echo $url . "\r\n";
			//echo "Criteri : " . json_encode($criteria) , "\r\n\r\n";
			$response = '{}';
			if($this->spikeApiCall) {
				try {
					$client = new Client(['http_errors' => false]);
					$response = $client->post($url, [
							'headers' => ['Content-Type' => 'application/json'],
							'body' => json_encode($criteria)
					]);
					$response = $response->getBody();
				} catch (Exception $e) {
				}
				
				if($this->spikeResponseSave) {
					$this->write('getArticle', $response);
				}
			} else {
				$response = $this->read('getArticle');
			}
				
			$response =  json_decode($response, true);
		}
	
		$postList = array();
	
		if(!empty($response['articles'])) {
			foreach($response['articles'] as $article) {
				$record = array();
				$record['postId'] = $article['uuid'];
				$record['accountId'] = '1';
	
				$record['url'] = $article['link'];
				$record ['title'] = $article['headline'];
				$record ['description'] = $article['excerpt'];
				$record ['keywords'] = $article['keywords'];
				$record ['sentiment'] = '0';
	
				//Add topic
				$record ['topics'] = $article['topics'];
	
				$record ['publisher'] = $article['source']['publisher'];
				$record ['image'] = $article['image_link'];
				$fbSocial = 0;
				if(!empty($article['fb_data']['total_engagement_count'])) {
					$fbSocial = $fbSocial + $article['fb_data']['total_engagement_count'];
				}
				/* $commentCountFB = $article['fb_data']['comment_count'];
				$likeCountFB = $article['fb_data']['like_count'];
				$shareCountFB = $article['fb_data']['share_count'];*/
				$tweetCountTW = $article['tw_data']['tw_count'];
				$linkCountLI = $article['li_data']['li_count']; 
					
				$social = $fbSocial + $tweetCountTW +  $linkCountLI;
					
				$newScrore = $article['nw_score'];
				$maxScrore = $article['max_nw_score'];
	
				$record['facebookSocial'] = $fbSocial;
				$record['twitterSocial'] = $tweetCountTW;
	
				$record['totalSocial'] = $social;
				$record ['velocity'] = $this->getVelocity($social, $newScrore, $maxScrore );
	
				$record ['creationDate'] = new \DateTime();
	
				$record['totalImpression'] = '0';
	
				$record['projectionChartDetail'] = $this->getProjectionChartDetail($article);
				$record['publicationTimestamp'] = $article['publication_timestamp'];
				$record['publicationDate'] = date('Y-m-d H:i:s', substr($article['publication_timestamp'], 0, 10));
	
				$postList[] = $record;
			}
		}
	
		if($isSort == true) {
			$postList = $this->sortObjectList($postList, 'velocity');
		}
	
		return $postList;
	}
	
	private function getProjectionChartDetail($post) {
		//Calculate rojection
		$projectionChart = array('largeCircle'=>0, 'largeMedCircle'=>0, 'medCircle'=>0, 'medSmCircle'=>0, 'smallCircle'=>0);
		if($post['nw_score'] == 0) {
			$projectionChart['smallCircle'] = 1;
		} else if($post['nw_score'] == $post['max_nw_score']) {
			$projectionChart['largeCircle'] = 15;
			$projectionChart['largeMedCircle'] = 3;
		} else if($post['nw_score'] > 0 && $post['max_nw_score']) {
			$projectionChart['largeCircle'] = ($post['nw_score']/$post['max_nw_score']) * 24;
			$projectionChart['largeMedCircle'] = ($post['fb_data']['delta_period']/60);
			
			if($post['tw_data']['tw_count'] > 0) {
				$projectionChart['medCircle'] = ($post['tw_data']['total_count_delta']/$post['tw_data']['tw_count']);
			}
			
			$projectionChart['smallCircle'] = 0;
			if($post['li_data']['li_count'] > 0) {
				if($projectionChart['medCircle'] < 1 && $post['li_data']['total_count_delta'] > 0) {
					$projectionChart['smallCircle'] = ($post['li_data']['total_count_delta']/$post['li_data']['li_count']);
				} else {
					$projectionChart['medSmCircle'] = ($post['li_data']['total_count_delta']/$post['li_data']['li_count']);
				}
			}
			
			if($projectionChart['largeCircle'] > 0 && $projectionChart['largeMedCircle'] > 0 
					&& $projectionChart['medCircle'] > 0 && $projectionChart['medSmCircle'] > 0
					&& ($projectionChart['largeCircle'] + $projectionChart['largeMedCircle'] + $projectionChart['medCircle'] + $projectionChart['largeMedCircle']) < 18) {
						$projectionChart['smallCircle'] = $projectionChart['smallCircle'] + 1;
				
			}
		} 
		 
		return $projectionChart;
	}
	
	/**
	 * This function return article based on publisher and topic
	 * @param array() $publisher - This expect records of publisher and topic
	 * @param number $hour default is 1 hour
	 * @param number $size default is 20 
	 */
	public function getArticleByCriteria($filterList = array(), $hour = 1, $size = 20) {
		$criteria = array();
		$criteria['language'] = 'en';
		$criteria['time_period'] = $hour;
		$criteria['sort_by'] = 'fb_tw_and_li';
		$criteria['size'] = intval($size);
		$criteria['filters'] = array();
		$url =  $this->apiURL . 'v1/articles?key='. $this->apiKey;
		
		//$filterList = array(array('publisher'=>'storypick.com', 'topic'=>'Organic'), array('publisher'=>'indianexpress.com', 'topic'=>'Organic'));
		if(!empty($filterList)) {
			$filterTemplate = "(publisher:##PUBLISHER## AND \"##TOPIC##\")";
			$publisherFilterTemplate = "(publisher:##PUBLISHER##)";
			$publisherFilter = '';
			
			foreach($filterList as $filter) {
				if(!empty($filter['publisher']) && !empty($filter['topic']) ) {
					if(!empty($publisherFilter)) {
						$publisherFilter = $publisherFilter . ' OR ';
					}
					$publisherFilter .= $filterTemplate;
					$publisherFilter = str_replace('##PUBLISHER##', $filter['publisher'], $publisherFilter);
					$publisherFilter = str_replace('##TOPIC##', $filter['topic'], $publisherFilter);
				} else {
					if(!empty($publisherFilter)) {
						$publisherFilter = $publisherFilter . ' OR ';
					}
					$publisherFilter .= $publisherFilterTemplate;
					$publisherFilter = str_replace('##PUBLISHER##', $filter['publisher'], $publisherFilter);
				}
			}
			$criteria['filters'][] = $publisherFilter;
		}

		$response = '{}';
		if($this->spikeApiCall) {
			$client = new Client();
			$response = $client->post($url, [
					'headers' => ['Content-Type' => 'application/json'],
					'body' => json_encode($criteria)
			]);
			$response = $response->getBody();
			if($this->spikeResponseSave) {
				$this->write('getArticleByCriteria', $response);
			}
		} else {
			$response = $this->read('getArticleByCriteria');
		}
		
		$response =  json_decode($response, true);
		
		$postList = array();
		
		if(!empty($response['articles'])) {
			foreach($response['articles'] as $article) {
				$record = array();
				$record['postId'] = $article['uuid'];
				$record['accountId'] = '1';
		
				$record['url'] = $article['link'];
				$record ['title'] = $article['headline'];
				$record ['description'] = $article['excerpt'];
				$record ['keywords'] = $article['keywords'];
				$record ['sentiment'] = '0';
		
				$record ['publisher'] = $article['source']['publisher'];
				$record ['image'] = $article['image_link'];
				$fbSocial = 0;
				if(!empty($article['fb_data']['total_engagement_count'])) {
					$fbSocial = $fbSocial + $article['fb_data']['total_engagement_count'];
				}
				/*$commentCountFB = $article['fb_data']['comment_count'];
				$likeCountFB = $article['fb_data']['like_count'];
				$shareCountFB = $article['fb_data']['share_count']; */
				$tweetCountTW = $article['tw_data']['tw_count'];
				$linkCountLI = $article['li_data']['li_count'];
					
				$social = $fbSocial + $tweetCountTW +  $linkCountLI;
					
				$newScrore = $article['nw_score'];
				$maxScrore = $article['max_nw_score'];
		
				$record['facebookSocial'] = $fbSocial;
				$record['twitterSocial'] = $tweetCountTW;
		
				$record['totalSocial'] = $social;
				$record ['velocity'] = $this->getVelocity($social, $newScrore, $maxScrore );
		
				$record ['creationDate'] = new \DateTime();
		
				$record['totalImpression'] = '0';
				
				$record['projectionChartDetail'] = $this->getProjectionChartDetail($article);
				$record['publicationTimestamp'] = $article['publication_timestamp'];
				$record['publicationDate'] = date('Y-m-d H:i:s', substr($article['publication_timestamp'], 0, 10));
		
				$postList[] = $record;
			}
		}
		
		$postList = $this->sortObjectList($postList, 'velocity');
		
		return $postList;
	}
	
	
	/**
	 * This function return the velocity
	 * @param unknown $social
	 * @param unknown $newScrore
	 * @param unknown $maxScrore
	 * @return number
	 */
	public function getVelocity($social, $newScrore, $maxScrore) {
		$velocity = 0;
		
		if($social >= 1 && $social <= 1000) {
			$velocity = 30;
		} elseif($social >= 1001 && $social <= 5000) {
			$velocity = 42;
		} elseif($social >= 5001 && $social <= 10000) {
			$velocity = 46;
		} elseif($social >= 10001 && $social <= 20000) {
			$velocity = 48;
		} elseif($social >= 20001 && $social <= 30000) {
			$velocity = 55;
		} elseif($social >= 30001 && $social <= 40000) {
			$velocity = 30;
		} elseif($social >= 40001 && $social <= 50000) {
			$velocity = 59;
		} elseif($social >= 50001 && $social <= 60000) {
			$velocity = 61;
		} elseif($social >= 60001 && $social <= 70000) {
			$velocity = 63;
		} elseif($social >= 70001 && $social <= 80000) {
			$velocity = 64;
		} elseif($social >= 80001 && $social <= 90000) {
			$velocity = 65;
		} elseif($social >= 90001 && $social <= 100000) {
			$velocity = 66;
		} elseif($social >= 100001 ) {
			$velocity = 60;
		}
		
		if($maxScrore >= 1 && $maxScrore <= 50) {
			$velocity = $velocity + 1.4;
		} elseif($maxScrore >= 51 && $maxScrore <= 100) {
			$velocity = $velocity + 6.7;
		} elseif($maxScrore >= 101 && $maxScrore <= 500) {
			$velocity = $velocity + 9.9;
		} elseif($maxScrore >= 501 && $maxScrore <= 2000) {
			$velocity = $velocity + 30;
		} elseif($maxScrore >= 2001 && $maxScrore <= 2500) {
			$velocity = $velocity + 30;
		} elseif($maxScrore >= 2501 && $maxScrore <= 3000) {
			$velocity = $velocity + 17.5;
		} elseif($maxScrore >= 3001 && $maxScrore <= 3500) {   
			$velocity = $velocity + 18.7;
		} elseif($maxScrore >= 3501 && $maxScrore <= 4000) {  
			$velocity = $velocity + 19.9;
		} elseif($maxScrore >= 4001 && $maxScrore <= 4500) {   
			$velocity = $velocity + 23.1;
		} elseif($maxScrore >= 4501 && $maxScrore <= 5000) {     
			$velocity = $velocity + 26.2;
		} elseif($maxScrore >= 5001 && $maxScrore <= 10000) { 
			$velocity = $velocity + 33.4;
		} elseif($maxScrore >= 10001 && $maxScrore <= 15000) {
			$velocity = $velocity + 34.6;
		}  
		
		if($newScrore >= 1 && $newScrore <= 50) {
			$velocity = $velocity + 2.9;
		} elseif($newScrore >= 51 && $newScrore <= 100) {
			$velocity = $velocity + 9.7;
		} elseif($newScrore >= 101 && $newScrore <= 500) {
			$velocity = $velocity + 10.5;
		} elseif($newScrore >= 501 && $newScrore <= 2000) {
			$velocity = $velocity + 11.8;
		} elseif($newScrore >= 2001 && $newScrore <= 2500) {
			$velocity = $velocity + 12;
		} elseif($newScrore >= 2501 && $newScrore <= 3000) {
			$velocity = $velocity + 12.7;
		} elseif($newScrore >= 3001 && $newScrore <= 3500) {
			$velocity = $velocity + 13.1;
		} elseif($newScrore >= 3501 && $newScrore <= 4000) {
			$velocity = $velocity + 13.6;
		} elseif($newScrore >= 4001 && $newScrore <= 4500) {
			$velocity = $velocity + 13.9;
		} elseif($newScrore >= 4501 && $newScrore <= 5000) {
			$velocity = $velocity + 14;
		} elseif($newScrore >= 5001 && $newScrore <= 10000) {
			$velocity = $velocity + 17.7;
		} elseif($newScrore >= 10001 && $newScrore <= 15000) {
			$velocity = $velocity + 20.6;
		}
		
		if($velocity > 100) {
			$velocity = 100;
		}
		return $velocity;
	}
	
	/**
	 *  This function return the sorted list
	 * @param $list is the list to be sort
	 * @param $property is the property on which list is going tobe sorted
	 * @return $list - Which is a sorted list
	 */
	public function sortObjectList($list, $property) {
		for($index = 0; $index < count($list); $index ++) {
			for($jindex = 0; $jindex < $index; $jindex ++) {
				//$t = $this->objectCmp($list[$index], $property, $list[$jindex], $property);
				if($list[$index][$property] > $list[$jindex][$property]) {
					$temp = $list[$index];
					$list[$index] = $list[$jindex];
					$list[$jindex] = $temp;
				}
			}
		}
	
		return $list;
	}
	
	/**
	 * This function write token into json file
	 *
	 * @param String $response
	 */
	private function write($key, $response) {
		$file = fopen('uploads/temp/' . $key . '.json', 'w');		
		fwrite($file, $response);
		fclose($file);
	}
	
	/**
	 * This function read token from loginResponse.json
	 *
	 * @return NULL
	 */
	private function read($key) {
		$filePath = 'uploads/temp/' . $key . '.json';
		if (file_exists($filePath)) {
			$data = file_get_contents($filePath);
			$response = $data;
			return $response;
		}
		return null;
	}
}
?>