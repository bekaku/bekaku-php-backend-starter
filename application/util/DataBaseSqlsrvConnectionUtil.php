<?php
/**
 * Created by PhpStorm.
 * User: developers
 * Date: 24/6/2018
 * Time: 3:51 PM
 */

namespace application\util;


use PDO;
use PDOException;

class DataBaseSqlsrvConnectionUtil
{
    private $connection;
    protected $DB_DRIVER;
    protected $DB_PORT;
    protected $DB_CHAR_SET;
    protected $DB_HOST_NAME;
    protected $DB_DATABASE_NAME;
    protected $DB_USERNAME;
    protected $DB_PASSWORD;

    public function __construct($dbConfig=array()){

        $this->DB_DRIVER = $dbConfig['driver'];
        $this->DB_CHAR_SET = $dbConfig['charset'];
        $this->DB_HOST_NAME = $dbConfig['host'];
        $this->DB_USERNAME = $dbConfig['username'];
        $this->DB_PASSWORD = $dbConfig['password'];
        $this->DB_DATABASE_NAME = $dbConfig['database'];

    }

    public function __destruct(){
        $this->closeConnection();
    }

    public function openSqlsrvConnection(){
        try{
            $DB_con = new PDO($this->DB_DRIVER.":Server={$this->DB_HOST_NAME};Database={$this->DB_DATABASE_NAME};",$this->DB_USERNAME,$this->DB_PASSWORD);
            $DB_con->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->setConnection($DB_con);

        }catch(PDOException $e){
            ControllerUtil::displaySqlError($e->getMessage());
        }
    }

    public function closeConnection(){
        if($this->connection){
            $this->connection = null;
        }
    }

    /**
     * @return mixed
     */
    public function getConnection()
    {
        return $this->connection;
    }

    /**
     * @param mixed $connection
     */
    public function setConnection($connection)
    {
        $this->connection = $connection;
    }

}