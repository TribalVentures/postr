<?php
namespace DB\Bundle\AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use DB\Bundle\CommonBundle\Base\BaseEntity;

/**
 * DB\Bundle\AppBundle\Entity\Setting
 *
 * @ORM\Table(name="setting")
 * @ORM\Entity
 */
class Setting extends BaseEntity {
	/**
	 * @var integer id
	 * @ORM\Column(name="id", type="integer", length=10)
	 * @ORM\Id
	 * @ORM\GeneratedValue(strategy="AUTO")
	 */
	private $id;
	
	/**
	 * @var string label
	 * @ORM\Column(name="label", type="string", length=100)
	 */
	private $label;
	
	/**
	 * @var string settingKey
	 * @ORM\Column(name="settingKey", type="string", length=100)
	 */
	private $settingKey;
	
	/**
	 * @var string settingValue
	 * @ORM\Column(name="settingValue", type="string")
	 */
	private $settingValue;
	
	/**
	 * Set id
	 *
	 * @param integer id
	 * @return Setting
	 */
	public function setId($id) {
		$this->id = $id;
		return $this;
	}
	
	/**
	 * Get id
	 *
	 * @return integer id
	 */
	public function getId() {
		return $this->id;
	}
	
	/**
	 * Set label
	 *
	 * @param string label
	 * @return Setting
	 */
	public function setLabel($label) {
		$this->label = $label;
		return $this;
	}
	
	/**
	 * Get label
	 *
	 * @return string label
	 */
	public function getLabel() {
		return $this->label;
	}
	 
	 /**
	  * Set settingKey
	  *
	  * @param string settingKey
	  * @return Setting
	  */
	 public function setSettingKey($settingKey) {
	 	$this->settingKey = $settingKey;
	 	return $this;
	 }
	 
	 /**
	  * Get settingKey
	  *
	  * @return string settingKey
	  */
	 public function getSettingKey() {
	 	return $this->settingKey;
	 }
	 
	 /**
	  * Set settingValue
	  *
	  * @param string settingValue
	  * @return Setting
	  */
	 public function setSettingValue($settingValue) {
	 	$this->settingValue = $settingValue;
	 	return $this;
	 }
	 
	 /**
	  * Get settingValue
	  *
	  * @return string settingValue
	  */
	 public function getSettingValue() {
	 	return $this->settingValue;
	 }
	
	/**
	 * This method return the primary ID for entity,
	 * Primary ID might be composite ID
	 * @return mixed[] - It return array of primary key
	 */
	public function getPrimaryKey() {
		$idParam = array();
		if(isset($this->id)) {
			$idParam['id'] = $this->id;
		}
		return $idParam;
	}

	/**
	 * This function return all fields as a properties
	 *
	 * @return mixed[]
	 */
	public function getProperty() {
		return get_object_vars($this);
	}
}
?>