<?php
/**
 * Created by PhpStorm.
 * User: Bekaku
 * Date: 3/1/2016
 * Time: 8:20 PM
 */

namespace application\service;

use application\core\DatabaseSupport as DatabaseSupport;
use application\serviceInterface\AppUserRoleServiceInterface as AppUserRoleServiceInterface;
use application\model\AppUserRole as AppUserRole;
class AppUserRoleService extends DatabaseSupport implements AppUserRoleServiceInterface
{
    protected $tableName = 'app_user_role';

    public function __construct($dbConn){
        $this->setDbh($dbConn);
    }
    public function findAll($perpage=0, $q_parameter=array())
    {
        //if have param
        $data_bind_where = null;

        $query = "SELECT * FROM ".$this->tableName;

        //where query
        $query .=" WHERE app_user_role.`id` IS NOT NULL ";
        //gen additional query and sort order
        $additionalParam = $this->genAdditionalParamAndWhereForListPage($q_parameter, $this->tableName);
        if(!empty($additionalParam)){
            if(!empty($additionalParam['additional_query'])){
                $query .= $additionalParam['additional_query'];
            }
            if(!empty($additionalParam['where_bind'])){
                $data_bind_where = $additionalParam['where_bind'];
            }
        }
        //paging buider
        if($perpage>0){
            $query .= $this->pagingHelper($query,$perpage,$data_bind_where);
        }
        //regular query
        $this->query($query);

        $this->genBindParamAndWhereForListPage($data_bind_where);
        return $this->resultset();
    }
    public function findAll_COPY($perpage=0, $q_parameter=array())
    {
        $query = "SELECT * FROM ".$this->tableName;

        //paging buider
        if($perpage>0){

            $query .= $this->pagingHelper($query,$perpage);
//            $this->query($query);
//            $this->execute();
//            $totalRec = $this->rowCount();

//            $this->setPagingLink($totalRec, $perpage);

            //query for get record by curent page
//            $query .= $this->getPagingLimitQuery($perpage);
        }

        //regular query
        $this->query($query);
        $resaultList =  $this->resultset();
        if (is_array($resaultList) || is_object($resaultList)){
            $findList = array();
            foreach($resaultList as $obj){
                $singleObj = null;
                $singleObj = new AppUserRole($obj);
                array_push($findList, $singleObj);
            }
            return $findList;
        }
        return false;
    }
    /* Select with paging and where condition*/
    /*
    public function findAll($perpage=0)
    {
        $id = 99;
        $query = "SELECT * FROM ".$this->tableName." WHERE id!=:id";
        if($perpage>0){
            //$this->setPagingLink($query,$perpage);
            $this->query($query);
            $this->bind(":id", $id);
            $this->execute();
            $totalRec = $this->rowCount();

            echoln('total rec='.$totalRec);
            $this->setPagingLink($totalRec, $perpage);
            //query for get record by curent page
            $query .= $this->pagingHelper($perpage);
            echoln($query);
        }

        $this->query($query);
        $this->bind(":id", $id);
        $resaultList =  $this->resultset();
        if(count($resaultList)>0){
            $findList = array();
            foreach($resaultList as $obj){
                $singleObj = null;
                $singleObj = new AppUserRole($obj);
                array_push($findList, $singleObj);
            }
            return $findList;
        }
        return false;
    }
    */
    public function findById($id)
    {
        $query = "SELECT * FROM ".$this->tableName." WHERE id=:id";
        $this->query($query);
        $this->bind(":id", $id);
        $result =  $this->single();
        if (is_array($result) || is_object($result)){
            $appUser = new AppUserRole($result);
            return $appUser;
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


}