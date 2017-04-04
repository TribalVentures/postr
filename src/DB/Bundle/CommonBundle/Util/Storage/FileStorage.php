<?php 
namespace DB\Bundle\CommonBundle\Util\Storage;

/*
 * Factory class to generate storage adapters. 
 */
class FileStorage {
	
    /**
     * Factory to dynamically generate a File Storage Adapter.
     *
     * @param array $storage_config The configuration data to create a File Storage Adapter.
     */
	public static function factory($storage_config){
		$adapter_name = '';
		$adapter_config = null;

		// Validate the storage_config array. 
        if (is_array($storage_config)) {
            $adapter_name = $storage_config['name'];
			$adapter_config = $storage_config['config'];
        }else{
			throw new \InvalidArgumentException('Invalid FileStorage Configuration.');
		}
		
        if (!is_string($adapter_name) || empty($adapter_name)) {
            throw new \InvalidArgumentException('Adapter name must be specified in a string');
        }
		
		// Set full adapter name.
		$adapter_name = 'DB\\Bundle\\CommonBundle\\Util\\Storage\\Adapter\\'.$adapter_name;
		
		// Instanciate the adapter. 
        $instance = new $adapter_name($adapter_config);

		return $instance;
	}
}