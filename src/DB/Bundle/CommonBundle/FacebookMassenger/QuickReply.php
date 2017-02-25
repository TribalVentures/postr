<?php

namespace DB\Bundle\CommonBundle\FacebookMassenger;

/**
 * Class QuickReply
 *
 * @package pimax\Messages
 */
class QuickReply extends Message{
    /**
     * @var array
     */
    protected $quick_replies = null;

    /**
     * Message constructor.
     *
     * @param $recipient
     * @param $text - string
     * @param $quick_replies - array of array("content_type","title","payload"),..,..
     */
    public function __construct($recipient, $text, $quick_replies = array()) {
        $this->quick_replies = $quick_replies;
        parent::__construct($recipient,$text);
    }
    
    public function getData() {
        return [
            'recipient' =>  [
                'id' => $this->recipient
            ],
            'message' => [
                'text' => $this->text,
                'quick_replies'=>$this->quick_replies
            ]
        ];
    }
    
    /**
     * This function add new quick reply
     * @param string $title
     * @param string $payload
     * @return \DB\Bundle\CommonBundle\FacebookMassenger\QuickReply
     */
    public function addTextQuickReply($title, $payload = '') {
    	if(empty($payload)) {
    		$payload = $title;
    	}
    	
    	$quiclReply = array();
    	$quiclReply['content_type'] = 'text';
    	$quiclReply['title'] = $title;
    	$quiclReply['payload'] = $payload;
    	
    	if(!isset($this->quick_replies)) {
    		$this->quick_replies = array();
    	}
    	
    	$this->quick_replies[] = $quiclReply;
    	
    	return $this;
    }
}