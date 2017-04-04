<?php 
namespace DB\Bundle\CommonBundle\Util\Storage\Adapter;

use Aws\S3\S3Client;

/*
 * Defines the Adapter Interface to interact with Amazon S3.
 */
class AmazonS3Adapter extends AdapterInterface {
	
	private $config = false;
	private $s3 = false;
	private $storage_class = 'STANDARD';
	
	public function __construct($config = array()) {
		$this->config = $config;
		
		if(array_key_exists('storage_class', $this->config)){
			$this->storage_class = $this->config['storage_class'];
		}
		if(array_key_exists('credentials', $this->config)){
			putenv('HOME='.$this->config['credentials']);
		}		
		return true;
	}
	
    /**
     * Load the Amazon S3 client.
     */
	private function loadS3() {
		if($this->s3 !== false){
			return $this->s3;
		}
		if($this->config != false){
			$factory_config = [];
			if(array_key_exists('key', $this->config)){
				$factory_config['key'] = $this->config['key'];
			}
			if(array_key_exists('secret', $this->config)){
				$factory_config['secret'] = $this->config['secret'];
			}
			$factory_config['version'] = $this->config['version'];
			$factory_config['region'] = $this->config['region'];
			$factory_config['scheme'] = $this->config['scheme'];
			$this->s3 = S3Client::factory($factory_config);
			return true;
		}
		return false;
	}
	
    /**
     * Save the file to Amazon S3.
     *
     * @param string $file_name The name of the file to save. This needs to be unique. 
	 * @param string $file_path	The absolute path on the file system.
	 * @param bool $delete_local	Delete the file after saving
     */
	public function save($file_name, $file_path, $delete_local = true){
		try {
			if($this->loadS3()){
				$mimetype = mime_content_type($file_path);
				$this->s3->putObject([
					'Bucket'		=> $this->config['bucket'],	
					'Key'			=> $file_name,
					'SourceFile'	=> $file_path,
					'ContentType'	=> $mimetype,
					'CacheControl'	=> 'Max-age=31536000',
					'ACL'			=> 'public-read',
					'StorageClass'	=> $this->storage_class 

				]);
				
				if($delete_local){
					@unlink($file_path);
				}
				return true;
			}
		} catch(Exception $e) {	
			return false;
		}
		return false;
	}
	
    /**
     * Delete file from Amazon S3.
     *
     * @param string $file_name The name of the file to delete. 
     */
	public function delete($file_name){
		try {
			if($this->loadS3()){
				$result = $this->s3->deleteObject(array(
					'Bucket' => $this->config['bucket'],	
					'Key'    => $file_name
				));
				return true;
			}
		} catch(Exception $e) {
			return false;
		}
		return false;
	}
	
    /**
     * Determine if the file exists in Amazon S3.
     *
     * @param string $file_name The name of the file to delete. 
     */
	public function exists($file_name){
		$fileExists = false;
		try {
			if($this->loadS3()){
				$fileExists = $this->s3->doesObjectExist($this->config['bucket'], $file_name);
			}
		} catch(Exception $e) {
			return false;
		}
		return $fileExists;
	}
}