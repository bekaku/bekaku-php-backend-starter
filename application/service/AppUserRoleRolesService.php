<?php
namespace application\service;

use application\core\DatabaseSupport as DatabaseSupport;
use application\serviceInterface\AppUserRoleRolesServiceInterface as AppUserRoleRolesServiceInterface;
use application\model\AppUserRoleRoles as AppUserRoleRoles;
class AppUserRoleRolesService extends DatabaseSupport implements AppUserRoleRolesServiceInterface
{
    protected $tableName = 'app_user_role_roles';

    public function __construct($dbConn){
        $this->setDbh($dbConn);
    }
    public function findAll($perpage=0, $q_parameter=array())
    {
        $query = "SELECT * FROM ".$this->tableName;
        //paging buider
        if($perpage>0){
            $query .= $this->pagingHelper($query,$perpage);
        }
        //regular query
        $this->query($query);
        $resaultList =  $this->resultset();
        if (is_array($resaultList) || is_object($resaultList)){
            $findList = array();
            foreach($resaultList as $obj){
                $singleObj = null;
                $singleObj = new AppUserRoleRoles($obj);
                array_push($findList, $singleObj);
            }
            return $findList;
        }
        return false;
    }

    public function findById($id)
    {
        $query = "SELECT * FROM ".$this->tableName." WHERE id=:id";
        $this->query($query);
        $this->bind(":id", $id);
        $result =  $this->single();
        if (is_array($result) || is_object($result)){
            $appUserRoleRoles = new AppUserRoleRoles($result);
            return $appUserRoleRoles;
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

    public function findAppUserRoleRolesByApUser($appUserId)
    {
        $query = "SELECT * FROM ".$this->tableName." WHERE app_user=:app_user";
        $this->query($query);
        $this->bind(":app_user", $appUserId);
        return $this->resultset();
    }
}