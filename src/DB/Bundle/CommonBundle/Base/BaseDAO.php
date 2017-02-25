<?php
namespace DB\Bundle\CommonBundle\Base;

use Exception;

class BaseDAO {
	private $doctrine;

	function __construct($_doctrine) {
		$this->doctrine = $_doctrine;
	}

	/**
	 * This function get doctrin manager
	 * @return doctrine manager
	 */
	public function getDoctrineManager() {
		return $this->doctrine->getManager();
	}

	/**
	 * This function get doctrin
	 * @return doctrine
	 */
	public function getDoctrine() {
		return $this->doctrine;
	}

	/**
	 * This funtion save object into database
	 * @param object - Object of enity class, Entity class should be configure using YML or Amotation
	 * @return object - Return same object with filled id data in it, If property esist
	 */
	public function save($object) {
		try {
			if(isset($object)) {
				$dm = $this->getDoctrineManager();
				$dm->persist($object);
				$dm->flush();
			}
		} catch (Exception $e) {
			echo "<br/>";
			echo $e->getMessage();
			echo "<hr/>";
		}
		return $object;
	}

	/**
	 * This function save multiple records in batch
	 * @param $list - Array of entity object
	 * @return return list as it is
	 */
	public function saveBatch($listt, $batchSize = 10) {
		$objectList = array();
		try {
			if(!empty($listt)) {
				//$batchSize = 10;
				$dm = $this->getDoctrineManager();
				for($index = 0; $index < count($listt); $index++) {
					$object = $listt[$index];
					$dm->persist($object);
					$objectList[] = $object;
					if (((($index+1) % $batchSize) == 0) || ($index+1) == count($listt)) {
						$dm->flush();
						$dm->clear();
					}
				}
				$dm->flush();
				$dm->clear();
			}
		} catch (Exception $e) {
			print_r($e->getMessage());
		}
		return $objectList;
	}

	public function update($object, $newParam = array()) {
		if(empty($object) || !is_object($object)) {
			return false;
		}
		$result = false;
		try {
			$updateObject = $this->get($object);
			if(empty($newParam) || empty($updateObject)) {
				return false;
			}
			foreach($newParam as $field=>$value) {
				$method = 'set'. ucfirst($field);
				if(method_exists($updateObject, $method)) {
					call_user_func_array(array($updateObject, $method), array($value));
				}
			}

			$result = $this->save($updateObject);

		} catch (Exception $e) {
			echo "<br/>";
			echo $e->getMessage();
			echo "<hr/>";
		}
		return $result;
	}

	/**
	 * This function get data from database and return
	 * @param $id - Primary key of table
	 * @return Object of return entity with data, If any exception then return null
	 */
	public function findBy($object, $criteriaParam = array(), $criteriaParam2 = array(), $limit = '', $offset = '') {
		if(empty($object) || !is_object($object)) {
			return false;
		}
		$result = null;
		try {
			if(!empty($limit) && !empty($offset)) {
				$result = $this->getDoctrine()->getRepository(get_class($object))->findBy($criteriaParam, $criteriaParam2, $limit, $offset);
			} else {
				$result = $this->getDoctrine()->getRepository(get_class($object))->findBy($criteriaParam, $criteriaParam2);
			}
		} catch (Exception $e) {
			throw new Exception("[BaseDAO.findById] " . $e->getMessage());
		}
		return $result;
	}
	
	/**
	 * This function return the list of detail
	 * @param Object $object - This is entity object
	 * @param array $criteriaParam
	 * @param array $criteriaParam2
	 */
	public function findDetailBy($object, $criteriaParam = array(), $criteriaParam2 = array()) {
		$list = $this->findBy($object, $criteriaParam, $criteriaParam2);
		
		$detailList = array();
		if(!empty($list)) {
			foreach($list as $object) {
				$detailList[] = $object->toArray();
			}
		}
		
		return $detailList;
	}
	
	/**
	 * This function return the list of detail
	 * @param Object $object - This is entity object
	 * @param array $criteriaParam
	 * @param array $criteriaParam2
	 */
	public function findSingleDetailBy($object, $criteriaParam = array(), $criteriaParam2 = array()) {
		$detailList = $this->findDetailBy($object, $criteriaParam, $criteriaParam2);
		
		if(!empty($detailList[0])) {
			return $detailList[0];
		}
		
		return $detailList;
	}

	/**
	 * This function gets data from database
	 */
	public function get($object) {
		if(empty($object) || !is_object($object)) {
			return false;
		}

		$result = null;
		try {
			$result = $this->findBy($object, $object->getPrimaryKey());
			if(is_array($result) && count($result) > 0) {
				$result = $result[0];
			}
		} catch (Exception $e) {
			throw new Exception("[BaseDAO.get] " . $e->getMessage());
		}
		return $result;
	}
	
	/**
	 * This function return the detail of entity
	 * @param Object $object
	 */
	public function getDetail($object) {
		$object = $this->get($object);
		
		if(!empty($object) && is_object($object)) {
			$object = $object->toArray();
		}
		return $object;	
	}

	/**
	 * This function get all data from database
	 */
	public function getAll($object) {
		if(empty($object) || !is_object($object)) {
			return false;
		}

		$result = null;
		try {
			$result = $this->findBy($object);
		} catch (Exception $e) {
			throw new Exception("[BaseDAO.getAll] " . $e->getMessage());
		}
		return $result;
	}
	
	/**
	 * This function return the All records detail of entity
	 * @param Object $object
	 */
	public function getAllDetail($object) {
		$list = $this->getAll($object);
		
		$detailList = array();
		if(!empty($list)) {
			foreach($list as $detail) {
				$detailList[] = $detail->toArray();
			}
		}
		return $detailList;	
	}

	/**
	 * This function delets record by where condion clause
	 */
	public function deleteBy($object, $param = array()) {
		$result = null;
		try {
			$em = $this->getDoctrine()->getManager();
				
			$sql = 	"DELETE FROM " . get_class($object) . " entityObject  ";
			
			if(!empty($param)) {
				$sql .= 'WHERE ';
				$index = 1;
				foreach($param as $field => $value) {
					if($index == 1) {
						$sql = $sql . " entityObject." . $field . " = :" . $field . " ";
					} else {
						$sql = $sql . " AND entityObject." . $field . " = :" . $field . " ";
					}
					$index ++;
				}
			}
				
			//echo $sql . "\r\n\r\n";
				
			$query = $em->createQuery($sql);
			$query->setParameters($param);
			
			$result = $query->getResult();
				
		} catch( Exception $e ) {
			throw new Exception("[BaseDAO.delete] " . $e->getMessage());
		}
		return $result;
	}

	/**
	 * This function delete record from databse
	 * @return boolean
	 */
	public function delete($object) {
		if(empty($object) || !is_object($object)) {
			return false;
		}
		$result = null;
		try {
			$result = $this->deleteBy($object, $object->getPrimaryKey());
		} catch (Exception $e) {
			throw new Exception("[BaseDAO.delete] " . $e->getMessage());
		}
		return $result;
	}

	/**
	 * This function search the data of related field (autocomplete)
	 * @param $entityName $param holds the values of entity and the parameter
	 * @return $result it return the searched results
	 */
	public function search($entityName, $param = array()) {
		$result = null;
		try {
			$em = $this->getDoctrine()->getManager();

			$sql = 	"SELECT entityObject FROM " . get_class($object) . " entityObject " ;
			$index = 0;
			foreach($param as $field => $value) {
				if($index > 0) {
					$sql = $sql . " AND ";

				} else {
					$sql = $sql . "WHERE ";
				}
				$sql = $sql . "entityObject." . $field . " LIKE '" . $value . "%'";
				$index ++;
			}
			//echo $sql;
			$query = $em->createQuery($sql);

			$result = $query->getResult();

		} catch( Exception $e ) {
			echo $e->getMessage();
		}
		return $result;
	}


	/**
	 * This fuction for count the records
	 * @param $entityName - Name of entity
	 * @return $countRecords
	 */
	public function getCountRecord($entityName) {
		try {
			$em = $this->getDoctrine()->getManager();
			$countRecords = null;

			$sql =  "SELECT COUNT(entityName) FROM DB\\AppBundle\\Entity\\" . $entityName . " entityName";

			$query = $em->createQuery($sql);
			$countResults = $query->getResult();

			foreach($countResults as $countResult) {
				$countRecords = $countResult[1];
			}

		} catch( Exception $e ) {
			echo $e->getMessage();
		}

		return $countRecords;
	}

	/**
	 * This function returns the count of available record table
	 * @param $fieldId - Holds the value to be count
	 * @param $from - Holds the entity name
	 * @param $where - Holds the where condition for count the records
	 * @return $id - It returns the count of records
	 */
	public function getCountByWhere($fieldId, $from, $where) {
		$id = -1;
		try {
			$em = $this->getDoctrine()->getManager();
			
			$sql =  "SELECT COUNT(" . $fieldId . ")" .
					" FROM " . $from ;
			if(!empty($where)){
				$sql = $sql . " WHERE " . $where;
			}
			
			//echo $sql; 
			
			$query = $em->createQuery($sql);			
			$result = $query->getResult();
			
			if(is_array($result) && count($result) > 0) {
				$id = $result[0][1];
			}
			
		} catch (Exception $e) {
			echo $e->getMessage();
		}
		return $id;
	}

	/**
	 * This function show the pagging details on the page
	 * @param $currentPage - Holds the values of current page
	 * @param $count - Holds the count of table record
	 * @return $paggingDetails - It returns the paging detail
	 */
	public function getPaggingDetails($currentPage = 1, $count = 0, $resultsPerPage = '') {
		//Calculate count first
		$paging = new Paging();
		$paging->TotalResults = $count;

		if($currentPage > $paging->TotalResults) {
			$currentPage = 1;
		}
		if(empty($resultsPerPage)) {
			$resultsPerPage = 4;
		}
		$paging->ResultsPerPage = $resultsPerPage;
		$paging->LinksPerPage = 10;
		$paging->PageVarName = "page";
		$paging->CurrentPage = $currentPage;

		$paggingDetails = $paging->InfoArray();

		return $paggingDetails;
	}
	
	/**
	 * This function execute the native SQL and return result
	 * @param string $sql
	 */
	public function query($sql) {
		$result = array();
		try {
			$stmt = $this->doctrine->getEntityManager()  
               ->getConnection()  
               ->prepare($sql);
			
            $stmt->execute();
			$result =  $stmt->fetchAll();
		} catch (Exception $e) {
			$result = array();
		}
		return $result;
	}
	
	/**
	 *  This function return the sorted list
	 * @param $list is the list to be sort
	 * @param $property is the property on which list is going tobe sorted
	 * @return $list - Which is a sorted list
	 */
	public function sortObjectList($list, $property) {
		for($index = 0; $index < count($list); $index ++) {
			for($jindex = 0; $jindex < $index; $jindex ++) {
				//$t = $this->objectCmp($list[$index], $property, $list[$jindex], $property);
				if($list[$index][$property] > $list[$jindex][$property]) {
					$temp = $list[$index];
					$list[$index] = $list[$jindex];
					$list[$jindex] = $temp;
				}
			}
		}
	
		return $list;
	}
}
?>