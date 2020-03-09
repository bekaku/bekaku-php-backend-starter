<?php
namespace application\service;

use application\core\DatabaseSupport as DatabaseSupport;
use application\serviceInterface\AppUserLoginServiceInterface as AppUserLoginServiceInterface;
use application\model\AppUserLogin as AppUserLogin;
class AppUserLoginService extends DatabaseSupport implements AppUserLoginServiceInterface
{
    protected $tableName = 'app_user_login';

    public function __construct($dbConn){
        $this->setDbh($dbConn);
    }
    public function findAll($perpage=0, $q_parameter=array())
    {
        $query = "SELECT app_user_login.id AS id ";
        $query .=",app_user_login.loged_in_date AS loged_in_date ";
        $query .=",app_user_login.loged_ip AS loged_ip ";
        $query .=",app_user_login.app_user AS app_user ";

        $query .="FROM app_user_login AS app_user_login ";

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
                $singleObj = new AppUserLogin($obj);
                array_push($findList, $singleObj);
            }
            return $findList;
        }
        return false;
    }

    public function findById($id)
    {
        $query = "SELECT app_user_login.id AS id ";
        $query .=",app_user_login.loged_in_date AS loged_in_date ";
        $query .=",app_user_login.loged_ip AS loged_ip ";
        $query .=",app_user_login.app_user AS app_user ";

        $query .="FROM app_user_login AS app_user_login ";
        $query .="WHERE app_user_login.id=:id ";

        $this->query($query);
        $this->bind(":id", $id);
        $result =  $this->single();
        if($result){
            $appUserLogin = new AppUserLogin($result);
            return $appUserLogin;
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