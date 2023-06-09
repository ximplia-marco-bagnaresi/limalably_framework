<?php

/**
 * @author MBCRAFT di Marco Bagnaresi - mail : info@mbcraft.it
 * 
 *  
 */

class LRemoteDeployerInstanceDriver implements LIDeployerInstanceDriver {

	function __construct($full_deployer_url) {

		$this->full_deployer_url = $full_deployer_url;

	}

	private function asResult($data) {

		$result = json_decode($data,true);

		if ($result==null) return ['result' => self::FAILURE_RESULT,'message' => $data];
		else return $result;

	}

	public function version($password) {

		$params = [];
		$params['METHOD'] = 'VERSION';
		$params['PASSWORD'] = $password;

		$result = LHttp::post($this->full_deployer_url,$params);

		return $this->asResult($result);
	}

	public function listElements($password,$folder) {

		$params = [];
		$params['METHOD'] = 'LIST_ELEMENTS';
		$params['PASSWORD'] = $password;
		$params['FOLDER'] = $folder;

		$result = LHttp::post($this->full_deployer_url,$params);

		return $this->asResult($result);

	}

	public function listHashes($password,$excluded_paths,$included_paths) {

		$params = [];
		$params['METHOD'] = 'LIST_HASHES';
		$params['PASSWORD'] = $password;
		$params['EXCLUDED_PATHS'] = implode(',',$excluded_paths);
		$params['INCLUDED_PATHS'] = implode(',',$included_paths);

		$result = LHttp::post($this->full_deployer_url,$params);

		return $this->asResult($result);

	}

	public function deleteFile($password,$path) {

		$params = [];
		$params['METHOD'] = 'DELETE_FILE';
		$params['PASSWORD'] = $password;
		$params['PATH'] = $path;

		$result = LHttp::post($this->full_deployer_url,$params);

		return $this->asResult($result);
	}

	public function makeDir($password,$path) {

		$params = [];
		$params['METHOD'] = 'MAKE_DIR';
		$params['PASSWORD'] = $password;
		$params['PATH'] = $path;

		$result = LHttp::post($this->full_deployer_url,$params);

		return $this->asResult($result);

	}

	public function deleteDir($password,$path,$recursive) {

		$params = [];
		$params['METHOD'] = 'DELETE_DIR';
		$params['PASSWORD'] = $password;
		$params['PATH'] = $path;
		$params['RECURSIVE'] = $recursive ? 'true' : 'false';

		$result = LHttp::post($this->full_deployer_url,$params);

		return $this->asResult($result);

	}

	public function copyFile($password,$path,$source_file) {

		if (!$source_file instanceof LFile) throw new \Exception("source_file is actually not an LFile instance.");

		$params = [];
		$params['METHOD'] = 'COPY_FILE';
		$params['PASSWORD'] = $password;
		$params['PATH'] = $path;
		$params['f'] = $source_file;

		$result = LHttp::post($this->full_deployer_url,$params);

		return $this->asResult($result);
	}

	public function downloadDir($password,$path,$save_file) {

		$params = [];
		$params['METHOD'] = 'DOWNLOAD_DIR';
		$params['PASSWORD'] = $password;
		$params['PATH'] = $path;

		LHttp::post_to_file($this->full_deployer_url,$params,$save_file);

		if ($save_file->exists() && $save_file->getSize()>0) return ['result' => self::SUCCESS_RESULT];
		else return ['result' => self::FAILURE_RESULT,'message' => 'Unable to save file on downloadDir : '.$save_file->getFullPath()];

	}

	public function listEnv($password) {
		$params = [];
		$params['METHOD'] = 'LIST_ENV';
		$params['PASSWORD'] = $password;

		$result = LHttp::post($this->full_deployer_url,$params);

		return $this->asResult($result);
	}

	public function getEnv($password,$env_var_name) {
		$params = [];
		$params['METHOD'] = 'GET_ENV';
		$params['PASSWORD'] = $password;
		$params['ENV_VAR_NAME'] = $env_var_name;

		$result = LHttp::post($this->full_deployer_url,$params);

		return $this->asResult($result);
		
	}

	public function setEnv($password,$env_var_name,$env_var_value) {

		$params = [];
		$params['METHOD'] = 'SET_ENV';
		$params['PASSWORD'] = $password;
		$params['ENV_VAR_NAME'] = $env_var_name;
		$params['ENV_VAR_VALUE'] = $env_var_value;

		$result = LHttp::post($this->full_deployer_url,$params);

		return $this->asResult($result);
	}
	
	public function hello($password=null) {

		$params = [];
		$params['METHOD'] = 'HELLO';
		$params['PASSWORD'] = $password;

		$result = LHttp::post($this->full_deployer_url,$params);

		return $this->asResult($result);
	}

	public function fileExists($password,$path) {

		$params = [];
		$params['METHOD'] = 'FILE_EXISTS';
		$params['PASSWORD'] = $password;
		$params['PATH'] = $path;

		$result = LHttp::post($this->full_deployer_url,$params);

		return $this->asResult($result);
	}

	public function readFileContent($password,$path) {

		$params = [];
		$params['METHOD'] = 'READ_FILE_CONTENT';
		$params['PASSWORD'] = $password;
		$params['PATH'] = $path;

		$result = LHttp::post($this->full_deployer_url,$params);

		return $this->asResult($result);
	}

	public function writeFileContent($password,$path,$content) {

		$params = [];
		$params['METHOD'] = 'WRITE_FILE_CONTENT';
		$params['PASSWORD'] = $password;
		$params['PATH'] = $path;
		$params['CONTENT'] = $content;

		$result = LHttp::post($this->full_deployer_url,$params);

		return $this->asResult($result);
	}

	public function listDb($password) {

		$params = [];
		$params['METHOD'] = 'LIST_DB';
		$params['PASSWORD'] = $password;

		$result = LHttp::post($this->full_deployer_url,$params);

		return $this->asResult($result);
	}

	public function backupDbStructure($password,$connection_name,$save_file) {

		$params = [];
		$params['METHOD'] = 'BACKUP_DB_STRUCTURE';
		$params['PASSWORD'] = $password;
		$params['CONNECTION_NAME'] = $connection_name;

		LHttp::post_to_file($this->full_deployer_url,$params,$save_file);

		if ($save_file->exists() && $save_file->getSize()>0) return ['result' => self::SUCCESS_RESULT];
		else return ['result' => self::FAILURE_RESULT,'message' => 'Unable to save file on backupDbStructure : '.$save_file->getFullPath()];

	}

	public function backupDbData($password,$connection_name,$save_file) {

		$params = [];
		$params['METHOD'] = 'BACKUP_DB_DATA';
		$params['PASSWORD'] = $password;
		$params['CONNECTION_NAME'] = $connection_name;
		
		LHttp::post_to_file($this->full_deployer_url,$params,$save_file);

		if ($save_file->exists() && $save_file->getSize()>0) return ['result' => self::SUCCESS_RESULT];
		else return ['result' => self::FAILURE_RESULT,'message' => 'Unable to save file on backupDbData : '.$save_file->getFullPath()];

	}

	public function migrateAll($password=null) {

		$params = [];
		$params['METHOD'] = 'MIGRATE_ALL';
		$params['PASSWORD'] = $password;

		$result = LHttp::post($this->full_deployer_url,$params);

		return $this->asResult($result);
	}

	public function migrateReset($password=null) {

		$params = [];
		$params['METHOD'] = 'MIGRATE_RESET';
		$params['PASSWORD'] = $password;

		$result = LHttp::post($this->full_deployer_url,$params);

		return $this->asResult($result);
	}

	public function migrateListDone($password=null) {

		$params = [];
		$params['METHOD'] = 'MIGRATE_LIST_DONE';
		$params['PASSWORD'] = $password;

		$result = LHttp::post($this->full_deployer_url,$params);

		return $this->asResult($result);
	}

	public function migrateListMissing($password=null) {

		$params = [];
		$params['METHOD'] = 'MIGRATE_LIST_MISSING';
		$params['PASSWORD'] = $password;

		$result = LHttp::post($this->full_deployer_url,$params);

		return $this->asResult($result);
	}

	public function fixPermissions($password,$permissions_to_set,$excluded_paths,$included_paths) {

		$params = [];
		$params['METHOD'] = 'FIX_PERMISSIONS';
		$params['PASSWORD'] = $password;
		$params['PERMISSIONS_TO_SET'] = $permissions_to_set;
		$params['EXCLUDED_PATHS'] = implode(',',$excluded_paths);
		$params['INCLUDED_PATHS'] = implode(',',$included_paths);

		$result = LHttp::post($this->full_deployer_url,$params);

		return $this->asResult($result);

	}
}