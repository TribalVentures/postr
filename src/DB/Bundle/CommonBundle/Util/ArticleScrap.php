<?php
namespace DB\Bundle\CommonBundle\Util;

/**
 * This class use for Scrapping artical data
 * @author Dipak Patil
 *
 */
class ArticleScrap {
	private $url = null;
	
	function __construct($url =null) {
		$this->url = $url;
	}
	
	/**
	 * This function scrap the artical
	 * @param unknown $url
	 */
	public function getArticleDetail($url) {
    	$html = file_get_dom($url);
    	$postDetail = array('title'=>'', 'url'=>'', 'description'=>'', 'image'=>'');
    	if(!empty($html)){
    		$elements = $html('head meta');
    	
    		foreach($elements as $element){
    			$property = $element->property;
    			if(strpos($property, 'og:') == 0) {
    				$property = str_replace('og:', '', $property);
    				if(isset($postDetail[$property])) {
    					$postDetail[$property] = $element->content;
    				}
    			}
    		}
    	}
    	
    	return $postDetail;
    }
    
    /**
     * This function return all meta tag form URL
     * @param string $url
     */
    public static function getAllMeta($url, $skipMetaList = array()) {
    	$list = array();
    	$html = file_get_dom($url);
    	
    	if(!empty($html)){
    		$elements = $html('head meta');
    		foreach($elements as $element) {
    			$record = array();
    			if(!empty($element->property) && !in_array($element->property, $skipMetaList)) {
    				$record['property'] = $element->property;
    				$record['content'] = $element->content;
    			} else if(!empty($element->name) && !in_array($element->name, $skipMetaList)) {
    				$record['name'] = $element->name;
    				$record['content'] = $element->content;
    			} else {
    				continue;
    			}
    			$list[] = $record;
    		}
    	}
    	
    	return $list;
    }
} 
?>