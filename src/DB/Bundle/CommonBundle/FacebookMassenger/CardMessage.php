<?php

namespace DB\Bundle\CommonBundle\FacebookMassenger;

/**
 * Class StructuredMessage
 *
 * @package DB\Bundle\CommonBundle\FacebookMassenger
 */
class CardMessage {
	protected $title = '';
	
	protected $subtitle = '';
	
	protected $imageUrl = '';
	
	protected $data = array();
	
	/**
	 * Constructore of Button message
	 * @param integer $recipient
	 * @param string $text
	 */
	public function __construct($title, $subtitle, $imageUrl = '') {
		$this->title = $title;
		$this->subtitle = $subtitle;
		$this->imageUrl = $imageUrl;
		
		
		$this->data = array();
		$this->data['buttons'] = array();
	}
	
	/**
	 * This function add new button 
	 * @param string $type
	 * @param string $title
	 * @param string $url
	 */
	public function addButton($type, $title, $action) {
		$this->data['buttons'][] = new MessageButton($type, $title, $action);
		
		return $this;
	}
	
	/**
	 * This function add postback button
	 * @param string $title
	 * @param string $payload
	 */
	public function addPostBackButton($title, $payload = '') {
		$this->addButton(MessageButton::TYPE_POSTBACK, $title, $payload);
		return $this;
	}
	
	/**
	 * This function add web button
	 * @param string $title
	 * @param string $url
	 */
	public function addWebButton($title, $url = '') {
		$this->addButton(MessageButton::TYPE_WEB, $title, $url);
		return $this;
	}
	
	/**
	 * This function return the structure button wil type 
	 */
	public function getData() {
		return new MessageElement($this->title, $this->subtitle, $this->imageUrl, $this->data['buttons']);
	}
}