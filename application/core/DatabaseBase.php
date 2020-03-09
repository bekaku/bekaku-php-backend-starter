<?php namespace application\core;

use PDO;
use PDOException;
use application\util\MessageUtils as MessageUtil;
use application\util\ControllerUtil as ControllerUtils;
class DatabaseBase{

	protected $DB_DRIVER;
	protected $DB_PORT;
	protected $DB_CHAR_SET;
	protected $DB_HOST_NAME;
	protected $DB_DATABASE_NAME;
	protected $DB_USERNAME;
	protected $DB_PASSWORD;

	private $defaultDriver;
	
	private $systemConnection;

	private $DEFAULT_DRIVER_MYSQL = 'mysql';
	private $DEFAULT_DRIVER_SQLSRV = 'sqlsrv';

	public function __construct(){

		$this->defaultDriver = MessageUtil::getConfig('db_default_driver');
		$this->selectConnection();

	}

	public function __destruct(){
		$this->CloseConnection();
	}

	public function selectConnection(){
		switch ($this->defaultDriver) {
			case $this->DEFAULT_DRIVER_MYSQL:
				$this->openMysqlConnection();
				break;
			case $this->DEFAULT_DRIVER_SQLSRV:
				//method for sql ser goes here
				break;
			default:
				ControllerUtils::displayError('No Connection Driver Config');
		}
	}
	
	public function openMysqlConnection(){

		$mysqlConfig = MessageUtil::getConfig($this->DEFAULT_DRIVER_MYSQL);

		$this->DB_DRIVER = $mysqlConfig['driver'];
		$this->DB_PORT = $mysqlConfig['port'];
		$this->DB_CHAR_SET = $mysqlConfig['charset'];
		$this->DB_HOST_NAME = $mysqlConfig['host'];
		$this->DB_USERNAME = $mysqlConfig['username'];
		$this->DB_PASSWORD = $mysqlConfig['password'];
		$this->DB_DATABASE_NAME = $mysqlConfig['database'];

		try{
// 			$DB_con = new PDO("mysql:host={$this->DB_HOST_NAME};dbname={$this->DB_DATABASE_NAME}",$this->DB_USERNAME,$this->DB_PASSWORD);
			$DB_con = new PDO($this->DB_DRIVER.":host={$this->DB_HOST_NAME};dbname={$this->DB_DATABASE_NAME};
						port={$this->DB_PORT};charset={$this->DB_CHAR_SET}",$this->DB_USERNAME,$this->DB_PASSWORD);
			$DB_con->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
			$this->systemConnection = $DB_con;
		}catch(PDOException $e){
			ControllerUtils::displaySqlError($e->getMessage());
		}
	}
    public function openMsSqlConnection(){

        $mysqlConfig = MessageUtil::getConfig($this->DEFAULT_DRIVER_SQLSRV);

        $this->DB_DRIVER = $mysqlConfig['driver'];
        $this->DB_CHAR_SET = $mysqlConfig['charset'];
        $this->DB_HOST_NAME = $mysqlConfig['host'];
        $this->DB_USERNAME = $mysqlConfig['username'];
        $this->DB_PASSWORD = $mysqlConfig['password'];
        $this->DB_DATABASE_NAME = $mysqlConfig['database'];

        try{
            $DB_con = new PDO($this->DB_DRIVER.":Server={$this->DB_HOST_NAME};Database={$this->DB_DATABASE_NAME};",$this->DB_USERNAME,$this->DB_PASSWORD);
            $DB_con->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->systemConnection = $DB_con;
        }catch(PDOException $e){
            ControllerUtils::displaySqlError($e->getMessage());
        }
    }
	public function closeConnection(){
		if($this->systemConnection){
			$this->systemConnection = null;
		}
		
	}
	
	public function getSystemConnection(){
		
		if($this->systemConnection){
			return $this->systemConnection;
		}else{
			return null;
		}
	}
}