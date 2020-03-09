<?php
namespace application\service;

use application\core\DatabaseSupport as DatabaseSupport;
use application\serviceInterface\AppUserLoginAttemptsServiceInterface as AppUserLoginAttemptsServiceInterface;
use application\model\AppUserLoginAttempts as AppUserLoginAttempts;
class AppUserLoginAttemptsService extends DatabaseSupport implements AppUserLoginAttemptsServiceInterface
{
    protected $tableName = 'app_user_login_attempts';

    public function __construct($dbConn){
        $this->setDbh($dbConn);
    }
    public function findAll($perpage=0, $q_parameter=array())
    {
        $query = "SELECT app_user_login_attempts.app_user AS app_user ";
        $query .=",app_user_login_attempts.time AS time ";
        $query .=",app_user_login_attempts.ip_address AS ip_address ";
        $query .=",app_user_login_attempts.created_date AS created_date ";
        $query .=",app_user_login_attempts.id AS id ";

        $query .="FROM app_user_login_attempts AS app_user_login_attempts ";

        //paging buider
        if($perpage>0){
            $query .= $this->pagingHelper($query,$perpage);
        }
        //regular query
        $this->query($query);
        $resaultList =  $this->resultset();
        if(count($resaultList)>0){
            $findList = array();
            foreach($resaultList as $obj){
                $singleObj = null;
                $singleObj = new AppUserLoginAttempts($obj);
                array_push($findList, $singleObj);
            }
            return $findList;
        }
        return false;
    }

    public function findById($id)
    {
        $query = "SELECT app_user_login_attempts.app_user AS app_user ";
        $query .=",app_user_login_attempts.time AS time ";
        $query .=",app_user_login_attempts.ip_address AS ip_address ";
        $query .=",app_user_login_attempts.created_date AS created_date ";
        $query .=",app_user_login_attempts.id AS id ";

        $query .="FROM app_user_login_attempts AS app_user_login_attempts ";
        $query .="WHERE app_user_login_attempts.id=:id ";

        $this->query($query);
        $this->bind(":id", $id);
        $result =  $this->single();
        if($result){
            $appUserLoginAttempts = new AppUserLoginAttempts($result);
            return $appUserLoginAttempts;
        }
        return false;
    }
    public function deleteById($id)
    {
        $query = "DELETE FROM ".$this->tableName." WHERE id=:id";
        $this->query($query);
        $this->bind(":id", $id);
        return $this->execute();
    }
    public function deleteByUserId($id)
    {
        $query = "DELETE FROM ".$this->tableName." WHERE app_user=:id";
        $this->query($query);
        $this->bind(":id", $id);
        return $this->execute();
    }
    public function createByArray($data_array)
    {
        return $this->insertHelper($this->tableName, $data_array);
    }
    public function createByObject($oject)
    {
        return $this->insertObjectHelper($oject);
    }
    public function update($data_array, $where_array, $whereType = 'AND')
    {
        return $this->updateHelper($this->tableName, $data_array, $where_array, $whereType);
    }
    public function updateByObject($object, $where_array, $whereType = 'AND')
    {
        return $this->updateObjectHelper($object, $where_array, $whereType);
    }

}