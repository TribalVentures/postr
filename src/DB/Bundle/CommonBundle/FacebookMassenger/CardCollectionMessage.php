<?php

namespace DB\Bundle\CommonBundle\FacebookMassenger;

/**
 * Class StructuredMessage
 *
 * @package DB\Bundle\CommonBundle\FacebookMassenger
 */
class CardCollectionMessage {
	protected $recipient = '';
	
	protected $data = array();
	
	/**
	 * Constructore of Button message
	 * @param integer $recipient
	 * @param string $text
	 */
	public function __construct($recipient) {
		$this->recipient = $recipient;

		$this->data = array();
		$this->data['elements'] = array();
	}
	
	/**
	 * This function add new card
	 * @param CardMessage $cardMessage
	 * @return \DB\Bundle\CommonBundle\FacebookMassenger\CardCollectionMessage
	 */
	public function addCard($cardMessage) {
		$this->data['elements'][] = $cardMessage->getData();
		
		return $this;
	}
	
	/**
	 * This function return the structure button wil type 
	 */
	public function getData() {
		return new StructuredMessage($this->recipient, StructuredMessage::TYPE_GENERIC, $this->data);
	}
}