<?php
/**
 * Created by PhpStorm.
 * User: Bekaku
 * Date: 3/1/2016
 * Time: 8:20 PM
 */

namespace application\service;

use application\core\DatabaseSupport as DatabaseSupport;
use application\serviceInterface\AppTableServiceInterface as AppTableServiceInterface;
use application\model\AppTable;
class AppTableService extends DatabaseSupport implements AppTableServiceInterface
{
    protected $tableName = 'app_table';

    public function __construct($dbConn){
        $this->setDbh($dbConn);
    }

    public function findAll($perpage=0, $q_parameter=array())
    {
        //if have param
        $data_bind_where = null;

        $query = "SELECT app_table.`id` AS `id` ";
        $query .=",app_table.`app_table_name` AS `app_table_name` ";
        $query .=",app_table.`vtheme` AS `vtheme` ";
        $query .=",app_table.`description` AS `description` ";
        $query .=",app_table.`created_user` AS `created_user` ";
        $query .=",app_table.`created_date` AS `created_date` ";
        $query .=",app_table.`updated_user` AS `updated_user` ";
        $query .=",app_table.`updated_date` AS `updated_date` ";

        $query .="FROM app_table AS app_table ";

        //where query
        $query .=" WHERE app_table.`id` IS NOT NULL ";

        //START PREPARE WHERE QUERY AND BIND VALUE FOR PAGING HELPER
        if(isset($q_parameter['q_app_table_name']) && $q_parameter['q_app_table_name']!=''){
            //for query concat
            $query .=" AND app_table.`app_table_name` LIKE :q_app_table_name  ";
            //for bind param
            $data_bind_where['q_app_table_name'] = "%".$q_parameter['q_app_table_name']."%";//bind param for like query
        }
        if(isset($q_parameter['q_description']) && $q_parameter['q_description']!=''){
            //for query concat
            $query .=" AND app_table.`description` LIKE :q_description  ";
            //for bind param
            $data_bind_where['q_description'] = "%".$q_parameter['q_description']."%";//bind param for like query
        }
        //END PREPARE WHERE QUERY AND BIND VALUE FOR PAGING HELPER

        //PREPARE SORT ORDER
        if(isset($q_parameter['sortField']) && $q_parameter['sortField']!=""){
            $query .=" ORDER BY app_table.`".$q_parameter['sortField']."` ";
        }else{
            $query .= " ORDER BY app_table.`id` ";
        }
        if(isset($q_parameter['sortMode']) && $q_parameter['sortMode']!=""){
            $query .=" ".$q_parameter['sortMode']." ";
        }else{
            $query .= " ASC ";
        }
        // END SORT ORDER
        //paging buider
        if($perpage>0){
            $query .= $this->pagingHelper($query,$perpage,$data_bind_where);
        }
        //regular query
        $this->query($query);



        //START BIND VALUE FOR REGULAR QUERY
        //$this->bind(":q_name", "%".$q_parameter['q_name']."%");//bind param for 'LIKE'
        //$this->bind(":q_name", $q_parameter['q_name']);//bind param for '='

        if(isset($q_parameter['q_app_table_name']) && $q_parameter['q_app_table_name']!=''){
            $this->bind(":q_app_table_name", "%".$q_parameter['q_app_table_name']."%");
        }
        if(isset($q_parameter['q_description']) && $q_parameter['q_description']!=''){
            $this->bind(":q_description", "%".$q_parameter['q_description']."%");
        }

        //END BIND VALUE FOR REGULAR QUERY
        $resaultList =  $this->resultset();
        if (is_array($resaultList) || is_object($resaultList)){
            $findList = array();
            foreach($resaultList as $obj){
                $singleObj = null;
                $singleObj = new AppTable($obj);
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
            $appUser = new AppTable($result);
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
    public function getTableColunm($tableName)
    {
        return $this->getTableColunmName($tableName);
    }

    public function dropTable($tableNameParam)
    {
        $query = "DROP TABLE ".$tableNameParam;
        $this->query($query);
        return $this->execute();
    }

    public function findByTableName($tableName)
    {
        $query = "SELECT * FROM ".$this->tableName." WHERE app_table_name=:tableName";
        $this->query($query);
        $this->bind(":tableName", $tableName);
        $result =  $this->single();
        if (is_array($result) || is_object($result)){
            $appUser = new AppTable($result);
            return $appUser;
        }
        return false;
    }
}