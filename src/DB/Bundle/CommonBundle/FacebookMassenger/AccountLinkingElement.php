<?php

namespace DB\Bundle\CommonBundle\FacebookMassenger;


/**
 * Class AccountLinkingElement
 *
 * @package DB\Bundle\CommonBundle\FacebookMassenger
 */
class AccountLinkingElement {
	const TYPE_ACCOUNT_LINK = "account_link";
	const TYPE_WEB_URL = "web_url";
	const TYPE_POSTBACK = "postback";
	
    /**
     * Title
     *
     * @var string|null
     */
    protected $title = null;

    /**
     * Image url
     *
     * @var null|string
     */
    protected $image_url = null;

    /**
     * Buttons
     *
     * @var array
     */
    protected $buttons = [];

    /**
     * AccountLinkingElement constructor.
     * 
     * @param string $authURL
     * @param string $title
     * @param string $image_url
     */
    public function __construct($authURL, $title, $image_url = '') {
        $this->title = $title;
        $this->image_url = $image_url;
        
        $button = array();
        $button['type'] = self::TYPE_ACCOUNT_LINK;
        $button['url'] = $authURL;
        
        $this->buttons[] = $button;
    }
    
    /**
     * This function add signup button in account linking
     * @param unknown $title
     * @param unknown $url
     */
    public function addSignupButton($title, $payload) {
    	$button = array();
    	$button['type'] = self::TYPE_POSTBACK;
    	$button['title'] = $title;
    	$button['payload'] = $payload;
    	
    	$this->buttons[] = $button;
    }

    /**
     * Get Element data
     * 
     * @return array
     */
    public function getData() {
        $result = [
            'title' => $this->title,
            'image_url' => $this->image_url,
        	'buttons' => $this->buttons
        ];

        return $result;
    }
}