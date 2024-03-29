<?php

namespace DB\Bundle\CommonBundle\FacebookMassenger;


/**
 * Class Message
 *
 * @package DB\Bundle\CommonBundle\FacebookMassenger
 */
class Message {
    /**
     * @var integer|null
     */
    protected $recipient = null;

    /**
     * @var string
     */
    protected $text = null;

    /**
     * Message constructor.
     *
     * @param $recipient
     * @param $text
     */
    public function __construct($recipient, $text) {
        $this->recipient = $recipient;
        $this->text = $text;

    }

    /**
     * Get message data
     *
     * @return array
     */
    public function getData() {
        return [
            'recipient' =>  [
                'id' => $this->recipient
            ],
            'message' => [
                'text' => $this->text
            ]
        ];
    }
}