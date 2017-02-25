<?php

namespace DB\Bundle\CommonBundle\FacebookMassenger;

/**
 * Class Address
 * 
 * @package DB\Bundle\CommonBundle\FacebookMassenger
 */
class Address {
    /**
     * @var array
     */
    protected $data = [];

    /**
     * Address constructor.
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