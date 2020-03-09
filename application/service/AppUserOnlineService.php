<?php
namespace application\service;

use application\core\DatabaseSupport as DatabaseSupport;
use application\serviceInterface\AppUserOnlineServiceInterface as AppUserOnlineServiceInterface;
use application\model\AppUserOnline as AppUserOnline;
class AppUserOnlineService extends DatabaseSupport implements AppUserOnlineServiceInterface
{
    protected $tableName = 'app_user_online';

    public function __construct($dbConn){
        $this->setDbh($dbConn);
    }
    public function findAll($perpage=0, $q_parameter=array())
    {
        //if have param
        $data_bind_where = null;

        $query = "SELECT app_user_online.`id` AS `id` ";
        $query .=",app_user_online.`sessions` AS `sessions` ";
        $query .=",app_user_online.`app_user` AS `app_user` ";
        $query .=",app_user_online.`times` AS `times` ";
        $query .=",app_user_online.`ip_address` AS `ip_address` ";

        $query .="FROM app_user_online AS app_user_online ";

		//where query
        $query .=" WHERE app_user_online.`id` IS NOT NULL ";
        //START PREPARE WHERE QUERY AND BIND VALUE FOR PAGING HELPER
        if(isset($q_parameter['q_id']) && $q_parameter['q_id']!=''){
            //for query concat
            $query .=" AND app_user_online.`id` LIKE :q_id  ";
            //for bind param
            $data_bind_where['q_id'] = "%".$q_parameter['q_id']."%";//bind param for like query
        }
        if(isset($q_parameter['q_sessions']) && $q_parameter['q_sessions']!=''){
            //for query concat
            $query .=" AND app_user_online.`sessions` LIKE :q_sessions  ";
            //for bind param
            $data_bind_where['q_sessions'] = "%".$q_parameter['q_sessions']."%";//bind param for like query
        }
        if(isset($q_parameter['q_app_user']) && $q_parameter['q_app_user']!=''){
            //for query concat
            $query .=" AND app_user_online.`app_user` LIKE :q_app_user  ";
            //for bind param
            $data_bind_where['q_app_user'] = "%".$q_parameter['q_app_user']."%";//bind param for like query
        }
        if(isset($q_parameter['q_times']) && $q_parameter['q_times']!=''){
            //for query concat
            $query .=" AND app_user_online.`times` LIKE :q_times  ";
            //for bind param
            $data_bind_where['q_times'] = "%".$q_parameter['q_times']."%";//bind param for like query
        }
        if(isset($q_parameter['q_ip_address']) && $q_parameter['q_ip_address']!=''){
            //for query concat
            $query .=" AND app_user_online.`ip_address` LIKE :q_ip_address  ";
            //for bind param
            $data_bind_where['q_ip_address'] = "%".$q_parameter['q_ip_address']."%";//bind param for like query
        }
        //END PREPARE WHERE QUERY AND BIND VALUE FOR PAGING HELPER

        //PREPARE SORT ORDER
        if(isset($q_parameter['sortField']) && $q_parameter['sortField']!=""){
                $query .=" ORDER BY app_user_online.`".$q_parameter['sortField']."` ";
        }else{
            $query .= " ORDER BY app_user_online.`id` ";
        }
        if(isset($q_parameter['sortMode']) && $q_parameter['sortMode']!=""){
            $query .=" ".$q_parameter['sortMode']." ";
        }else{
            $query .= " ASC ";
        }
        // END SORT ORDER

        //paging buider
        if($perpage>0){
            $query .= $this->pagingHelper($query, $perpage, $data_bind_where);
        }
        //regular query
        $this->query($query);
        //START BIND VALUE FOR REGULAR QUERY
        //$this->bind(":q_name", "%".$q_parameter['q_name']."%");//bind param for 'LIKE'
	     //$this->bind(":q_name", $q_parameter['q_name']);//bind param for '='
        if(isset($q_parameter['q_id']) && $q_parameter['q_id']!=''){
            $this->bind(":q_id", "%".$q_parameter['q_id']."%");
        }
        if(isset($q_parameter['q_sessions']) && $q_parameter['q_sessions']!=''){
            $this->bind(":q_sessions", "%".$q_parameter['q_sessions']."%");
        }
        if(isset($q_parameter['q_app_user']) && $q_parameter['q_app_user']!=''){
            $this->bind(":q_app_user", "%".$q_parameter['q_app_user']."%");
        }
        if(isset($q_parameter['q_times']) && $q_parameter['q_times']!=''){
            $this->bind(":q_times", "%".$q_parameter['q_times']."%");
        }
        if(isset($q_parameter['q_ip_address']) && $q_parameter['q_ip_address']!=''){
            $this->bind(":q_ip_address", "%".$q_parameter['q_ip_address']."%");
        }
        //END BIND VALUE FOR REGULAR QUERY
        $resaultList =  $this->resultset();
		if (is_array($resaultList) || is_object($resaultList)){
            $findList = array();
            foreach($resaultList as $obj){
                $singleObj = null;
                $singleObj = new AppUserOnline($obj);
                array_push($findList, $singleObj);
            }
            return $findList;
        }
        return false;
    }

    public function findById($id)
    {
        $query = "SELECT app_user_online.`id` AS `id` ";
        $query .=",app_user_online.`sessions` AS `sessions` ";
        $query .=",app_user_online.`app_user` AS `app_user` ";
        $query .=",app_user_online.`times` AS `times` ";
        $query .=",app_user_online.`ip_address` AS `ip_address` ";

        $query .="FROM app_user_online AS app_user_online ";
        $query .="WHERE app_user_online.`id`=:id ";

        $this->query($query);
        $this->bind(":id", (int)$id);
        $result =  $this->single();
		if (is_array($result) || is_object($result)){
            $appUserOnline = new AppUserOnline($result);
            return $appUserOnline;
        }
        return false;
    }
    public function findByUniqeToken($token)
    {
        $query = "SELECT app_user_online.`id` AS `id` ";
        $query .=",app_user_online.`sessions` AS `sessions` ";
        $query .=",app_user_online.`app_user` AS `app_user` ";
        $query .=",app_user_online.`times` AS `times` ";
        $query .=",app_user_online.`ip_address` AS `ip_address` ";

        $query .="FROM app_user_online AS app_user_online ";
        $query .="WHERE app_user_online.`sessions`=:token_no ";

        $this->query($query);
        $this->bind(":token_no", $token);
        $result =  $this->single();
        if (is_array($result) || is_object($result)){
            $appUserOnline = new AppUserOnline($result);
            return $appUserOnline;
        }
        return false;
    }
    public function getCountUserOnlineSession(){

        $query = "SELECT COUNT(DISTINCT sessions) AS countOnline ";
        $query .="FROM app_user_online ";

        $this->query($query);
        return  $this->single();
    }
    public function deleteById($id)
    {
        $query = "DELETE FROM ".$this->tableName." WHERE `id`=:id";
        $this->query($query);
        $this->bind(":id", (int)$id);
        return $this->execute();
    }
    public function deleteOnlineSessionByTime($timeCheck)
    {
        $query = "DELETE FROM ".$this->tableName." WHERE `times` <:time_check";
        $this->query($query);
        $this->bind(":time_check", $timeCheck);
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