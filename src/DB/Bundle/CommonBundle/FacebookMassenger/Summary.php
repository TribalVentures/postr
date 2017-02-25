<?php

namespace DB\Bundle\CommonBundle\FacebookMassenger;

/**
 * Class Summary
 *
 * @package DB\Bundle\CommonBundle\FacebookMassenger
 */
class Summary {
    /**
     * @var array
     */
    protected $data = [];

    /**
     * Summary constructor.
     *
     * @param $data
     */
    public function __construct($data) {
        $this->data = $data;
    }

    /**
     * Get Data
     * 
     * @return array
     */
    public function getData() {
        return $this->data;
    }
}