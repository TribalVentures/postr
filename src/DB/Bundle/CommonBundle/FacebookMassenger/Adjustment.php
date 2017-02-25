<?php

namespace DB\Bundle\CommonBundle\FacebookMassenger;

/**
 * Class Adjustment
 *
 * @package DB\Bundle\CommonBundle\FacebookMassenger
 */
class Adjustment {
    /**
     * @var array
     */
    protected $data = [];

    /**
     * Adjustment constructor.
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