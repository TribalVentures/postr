<?php

namespace DB\Bundle\CommonBundle\FacebookMassenger;

/**
 * Class StructuredMessage
 *
 * @package DB\Bundle\CommonBundle\FacebookMassenger
 */
class Payload {
	private $route = '';
	
	private $data = array();

	/**
	 * This will create payload with route
	 * @param string $route
	 */
	function __construct() {
	}
	
	/**
	 * This function will create new payload and set route and data
	 * @param string $paloadString
	 * @return Payload
	 */
	public static function initPayload($paloadString = '') {
		$payload = new Payload();
		if(empty($paloadString)) {
			return $payload;
		}
		
		//$payloadData = json_decode($paloadString, true);
		$payloadData = @unserialize($paloadString);
		//Check for route
		if(!empty($payloadData['route'])) {
			$payload->setRoute($payloadData['route']);
		}
		
		//Check for route data
		if(!empty($payloadData['data'])) {
			$payload->setData($payloadData['data']);
		}
		
		return $payload;
	}
	
	/**
	 * This function set route
	 * @param string $route
	 */
	public function setRoute($route) {
		$this->route = $route;
		
		return $this;
	}
	
	/**
	 * This function will return the route
	 */
	public function getRoute() {
		return $this->route;
	}
	
	/**
	 * This function set payload data
	 * @param array $data
	 */
	public function setData($data = array()) {
		$this->data = $data;
		return $this;
	}
	
	/**
	 * This function will return payload data
	 */
	public function getData() {
		return $this->data;
	}
	
	/**
	 * This function will add new data into payload
	 * @param String $key
	 * @param String $value
	 */
	public function addData($key, $value = '') {
		$this->data[$key] = $value;
		
		return $this;
	}
	
	/**
	 * This function remove form data
	 * @param string $key
	 */
	public function removeData($key) {
		if(isset($this->data[$key])) {
			unset($this->data[$key]);
		}
		return $this;
	}
	
	/**
	 * This function will return data by key
	 * @param string $key
	 */
	public function getDataByKey($key) {
		if(isset($this->data[$key])) {
			return $this->data[$key];
		}
		return '';
	}
	
	/**
	 * This function will return the string payload
	 */
	public function getPayload() {
		//return json_encode(array('route'=>$this->getRoute(), 'data'=>$this->getData()));
		return @serialize(array('route'=>$this->getRoute(), 'data'=>$this->getData()));
	}
}