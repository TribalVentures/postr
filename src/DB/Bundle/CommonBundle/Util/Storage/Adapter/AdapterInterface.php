<?php 
namespace DB\Bundle\CommonBundle\Util\Storage\Adapter;
/*
 * Defines the Adapter Interface for File Storage.
 */
abstract class AdapterInterface {
	private $error = false;
	
	/**
     * Constructor.
     *
     * @param array $config
     */
	public abstract function __construct($config = array());
	 
    /**
     * Save the file to storage.
     *
     * @param string $file_name The name of the file to save. This needs to be unique. 
	 * @param string $file_path	The absolute path on the file system.
     */
	public abstract function save($file_name, $file_path, $delete_local = true);
	
    /**
     * Delete file from storage.
     *
     * @param string $file_name The name of the file to delete. 
     */
	public abstract function delete($file_name);
	 
    /**
     * Determine if the file exists in storage.
     *
     * @param string $file_name The name of the file to delete. 
     */
	public abstract function exists($file_name);
	
    /**
     * Returns the error
     *
     */
	public function getError(){
		return $this->error;
	}
}