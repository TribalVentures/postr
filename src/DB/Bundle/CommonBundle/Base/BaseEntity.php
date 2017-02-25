<?php
namespace DB\Bundle\CommonBundle\Base;

use Doctrine\ORM\Mapping as ORM;

abstract class BaseEntity {
	public function toArray() {
		$objectArray = array();

		//Get Entity property
		$property = $this->getProperty();

		if(!empty($property)) {
			foreach ($property as $key=>$value) {
				$objectArray[$key] = call_user_func_array(array($this, "get" . ucfirst($key)), array());
				//echo  "set" . ucfirst($key) . "<hr/>";
			}
		}

		return $objectArray;
	}
	
	public abstract function getProperty();
}
?>