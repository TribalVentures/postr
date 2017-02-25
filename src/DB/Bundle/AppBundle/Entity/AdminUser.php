<?php
namespace DB\Bundle\AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use DB\Bundle\CommonBundle\Base\BaseEntity;

/**
 * DB\Bundle\AppBundle\Entity\user
 *
 * @ORM\Table(name="admin_user")
 * @ORM\Entity
 */
class AdminUser extends BaseEntity {
	/**
	 * @var integer adminUserId
	 * @ORM\Column(name="adminUserId", type="integer", length=10)
	 * @ORM\Id
	 * @ORM\GeneratedValue(strategy="AUTO")
	 */
	private $adminUserId;
		
	/**
	 * @var string name
	 * @ORM\Column(name="name", type="string", length=100)
	 */
	private $name;
	
	/**
	 * @var string email
	 * @ORM\Column(name="email", type="string", length=100)
	 */
	private $email;
	
	/**
	 * @var string $password
	 * @ORM\Column(name="password", type="string", length=150)
	 */
	private $password;
	 
	 /**
	  * Set adminUserId
	  *
	  * @param integer adminUserId
	  * @return AdminUser
	  */
	 public function setAdminUserId($adminUserId) {
	 	$this->adminUserId = $adminUserId;
	 	return $this;
	 }
	 
	 /**
	  * Get adminUserId
	  *
	  * @return integer adminUserId
	  */
	 public function getAdminUserId() {
	 	return $this->adminUserId;
	 }
	 
	 /**
	  * Set name
	  *
	  * @param string name
	  * @return AdminUser
	  */
	 public function setName($name) {
	 	$this->name = $name;
	 	return $this;
	 }
	 
	 /**
	  * Get name
	  *
	  * @return string name
	  */
	 public function getName() {
	 	return $this->name;
	 }
	 
	 /**
	  * Set email
	  *
	  * @param string email
	  * @return AdminUser
	  */
	 public function setEmail($email) {
	 	$this->email = $email;
	 	return $this;
	 }
	 
	 /**
	  * Get email
	  *
	  * @return string email
	  */
	 public function getEmail() {
	 	return $this->email;
	 }
	 
	 /**
	  * Set password
	  *
	  * @param string password
	  * @return AdminUser
	  */
	 public function setPassword($password) {
	 	$this->password = $password;
	 	return $this;
	 }
	 
	 /**
	  * Get password
	  *
	  * @return string password
	  */
	 public function getPassword() {
	 	return $this->password;
	 }
	 
	 /**
	  * This method return the primary ID for entity,
	  * Primary ID might be composite ID
	  * @return mixed[] - It return array of primary key
	  */
	 public function getPrimaryKey() {
	 	$idParam = array();
	 	if(isset($this->adminUserId)) {
	 		$idParam['adminUserId'] = $this->adminUserId;
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