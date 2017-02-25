<?php

namespace DB\Bundle\CommonBundle\ApiClient;


use Unirest\Request;
use Doctrine\DBAL\Platforms\Keywords\KeywordList;
/**
 * 
 * @author btit34
 *
 */
class DBSentimentAnalysisClient {
	
	private  $url = "https://twinword-sentiment-analysis.p.mashape.com/analyze/";
	
	private  $mashapeKey = "JRzJ01ZFH9mshNiS77C6M0D82hwbp1lg1H8jsnwR2DSIbudcfX";
	
	public function __construct() {
		
	}
	
	/**
	 * This fucntion return sentimental of text
	 * @param string $text
	 * @return number
	 */
	public function getSentimenal($text) {
		$text = preg_replace('/[\x00-\x1F\x80-\xFF]/', '', $text);
		$text = str_replace('"', '', $text);
		$response = Request::post($this->url,array("X-Mashape-Key" => $this->mashapeKey, "Accept" => "application/json" ),array("text" => $text));
		
		if($response->body->result_code ==200) {
			return $response->body->score;
		} else {
			return 0;
		}
	}
	
	
	/**
	 *
	 * @param unknown $text
	 *
	 */
	public function getSentiment($postList) {
		$text = "";
		 for($index = 0; $index <  count($postList['postlist']); $index ++) {
		 	$text .= $postList['postlist'][0]['title'].$postList['postlist'][0]['description'];
		 }
		 $text = preg_replace('/[\x00-\x1F\x80-\xFF]/', '', $text);
		 $text = str_replace('"', '', $text);
		 $response = Request::post($this->url,array("X-Mashape-Key" => $this->mashapeKey, "Accept" => "application/json" ),array("text" => $text));
		 $sentiment = array();
		 $keywordsScore = array();
		 
		 if($response->body->result_code ==200) {
		 	$score = $response->body->score;
		 	$keywordList = $this->sortObjectList($response->body->keywords, 'score');
		 	$count = count($keywordList);
		 	$sentiment['min'] = $keywordList[0];
		 	$sentiment['max'] = $keywordList[$count-1];
		 	for($index = 0; $index < $count; $index ++ ){
		 		$keywordsScore[$index] = $response->body->keywords[$index]->score;
		 	}
		 	$sentiment['average'] = array_sum($keywordsScore) / count($keywordsScore);
		 	return $sentiment;
		 } else {
		 	return 0;
		 }
	
	}
	
	/**
	 * 
	 * @param unknown $text
	 * 
	 */
	public function oldgetSentiment($text) {
		$response = Request::post($this->url,array("X-Mashape-Key" => $this->mashapeKey, "Accept" => "application/json" ),array("text" => $text));
		$sentiment = array();
		$keywordsScore = array();
		
		if($response->body->result_code ==200) {
			$count = count($response->body->keywords);
			$keywordList = $this->sortObjectList($response->body->keywords, 'score');
			$count = count($keywordList);
			$sentiment['min'] = $keywordList[0];
			$sentiment['max'] = $keywordList[$count-1];
			for($index = 0; $index < $count; $index ++ ){
				$keywordsScore[$index] = $response->body->keywords[$index]->score;
			}
			$sentiment['average'] = array_sum($keywordsScore) / count($keywordsScore); 
			return $sentiment;
		} else {
			return 0;
		}
		
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
				if($list[$index]->$property < $list[$jindex]->$property) {
					$temp = $list[$index];
					$list[$index] = $list[$jindex];
					$list[$jindex] = $temp;
				}
			}
		}
	
		return $list;
	}
	 
	
}
?>