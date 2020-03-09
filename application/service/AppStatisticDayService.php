<?php
namespace application\service;

use application\core\DatabaseSupport as DatabaseSupport;
use application\serviceInterface\AppStatisticDayServiceInterface as AppStatisticDayServiceInterface;
use application\model\AppStatisticDay as AppStatisticDay;
class AppStatisticDayService extends DatabaseSupport implements AppStatisticDayServiceInterface
{
    protected $tableName = 'app_statistic_day';

    public function __construct($dbConn){
        $this->setDbh($dbConn);
    }
    public function findAll($perpage=0, $q_parameter=array())
    {
        //if have param
        $data_bind_where = null;

        $query = "SELECT app_statistic_day.`id` AS `id` ";
        $query .=",app_statistic_day.`ip_add` AS `ip_add` ";
        $query .=",app_statistic_day.`ss_id` AS `ss_id` ";
        $query .=",app_statistic_day.`count_date` AS `count_date` ";
        $query .=",app_statistic_day.`user_angine` AS `user_angine` ";
        $query .=",app_statistic_day.`isBot` AS `isBot` ";

        $query .="FROM app_statistic_day AS app_statistic_day ";

		//where query
        $query .=" WHERE app_statistic_day.`id` IS NOT NULL ";
        //START PREPARE WHERE QUERY AND BIND VALUE FOR PAGING HELPER
        if(isset($q_parameter['q_id']) && $q_parameter['q_id']!=''){
            //for query concat
            $query .=" AND app_statistic_day.`id` LIKE :q_id  ";
            //for bind param
            $data_bind_where['q_id'] = "%".$q_parameter['q_id']."%";//bind param for like query
        }
        if(isset($q_parameter['q_ip_add']) && $q_parameter['q_ip_add']!=''){
            //for query concat
            $query .=" AND app_statistic_day.`ip_add` LIKE :q_ip_add  ";
            //for bind param
            $data_bind_where['q_ip_add'] = "%".$q_parameter['q_ip_add']."%";//bind param for like query
        }
        if(isset($q_parameter['q_ss_id']) && $q_parameter['q_ss_id']!=''){
            //for query concat
            $query .=" AND app_statistic_day.`ss_id` LIKE :q_ss_id  ";
            //for bind param
            $data_bind_where['q_ss_id'] = "%".$q_parameter['q_ss_id']."%";//bind param for like query
        }
        if(isset($q_parameter['q_count_date']) && $q_parameter['q_count_date']!=''){
            //for query concat
            $query .=" AND app_statistic_day.`count_date` LIKE :q_count_date  ";
            //for bind param
            $data_bind_where['q_count_date'] = "%".$q_parameter['q_count_date']."%";//bind param for like query
        }
        if(isset($q_parameter['q_user_angine']) && $q_parameter['q_user_angine']!=''){
            //for query concat
            $query .=" AND app_statistic_day.`user_angine` LIKE :q_user_angine  ";
            //for bind param
            $data_bind_where['q_user_angine'] = "%".$q_parameter['q_user_angine']."%";//bind param for like query
        }
        if(isset($q_parameter['q_isBot']) && $q_parameter['q_isBot']!=''){
            //for query concat
            $query .=" AND app_statistic_day.`isBot` LIKE :q_isBot  ";
            //for bind param
            $data_bind_where['q_isBot'] = "%".$q_parameter['q_isBot']."%";//bind param for like query
        }
        //END PREPARE WHERE QUERY AND BIND VALUE FOR PAGING HELPER

        //PREPARE SORT ORDER
        if(isset($q_parameter['sortField']) && $q_parameter['sortField']!=""){
                $query .=" ORDER BY app_statistic_day.`".$q_parameter['sortField']."` ";
        }else{
            $query .= " ORDER BY app_statistic_day.`id` ";
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
        if(isset($q_parameter['q_ip_add']) && $q_parameter['q_ip_add']!=''){
            $this->bind(":q_ip_add", "%".$q_parameter['q_ip_add']."%");
        }
        if(isset($q_parameter['q_ss_id']) && $q_parameter['q_ss_id']!=''){
            $this->bind(":q_ss_id", "%".$q_parameter['q_ss_id']."%");
        }
        if(isset($q_parameter['q_count_date']) && $q_parameter['q_count_date']!=''){
            $this->bind(":q_count_date", "%".$q_parameter['q_count_date']."%");
        }
        if(isset($q_parameter['q_user_angine']) && $q_parameter['q_user_angine']!=''){
            $this->bind(":q_user_angine", "%".$q_parameter['q_user_angine']."%");
        }
        if(isset($q_parameter['q_isBot']) && $q_parameter['q_isBot']!=''){
            $this->bind(":q_isBot", "%".$q_parameter['q_isBot']."%");
        }
        //END BIND VALUE FOR REGULAR QUERY
        $resaultList =  $this->resultset();
		if (is_array($resaultList) || is_object($resaultList)){
            $findList = array();
            foreach($resaultList as $obj){
                $singleObj = null;
                $singleObj = new AppStatisticDay($obj);
                array_push($findList, $singleObj);
            }
            return $findList;
        }
        return false;
    }

    public function findById($id)
    {
        $query = "SELECT app_statistic_day.`id` AS `id` ";
        $query .=",app_statistic_day.`ip_add` AS `ip_add` ";
        $query .=",app_statistic_day.`ss_id` AS `ss_id` ";
        $query .=",app_statistic_day.`count_date` AS `count_date` ";
        $query .=",app_statistic_day.`user_angine` AS `user_angine` ";
        $query .=",app_statistic_day.`isBot` AS `isBot` ";

        $query .="FROM app_statistic_day AS app_statistic_day ";
        $query .="WHERE app_statistic_day.`id`=:id ";

        $this->query($query);
        $this->bind(":id", (int)$id);
        $result =  $this->single();
		if (is_array($result) || is_object($result)){
            $appStatisticDay = new AppStatisticDay($result);
            return $appStatisticDay;
        }
        return false;
    }

    public function getCountStatisticViewsDay(){

        $trueValue = 1;

        $query = "SELECT COUNT(DISTINCT ss_id) AS dayViews ";
        $query .="FROM app_statistic_day ";
        $query .="WHERE `isBot` !=:is_bot ";

        $this->query($query);
        $this->bind(":is_bot", (int)$trueValue);
        return  $this->single();
    }

    public function deleteById($id)
    {
        $query = "DELETE FROM ".$this->tableName." WHERE `id`=:id";
        $this->query($query);
        $this->bind(":id", (int)$id);
        return $this->execute();
    }
    public function deleteByDate($date)
    {
        $query = "DELETE FROM ".$this->tableName." WHERE `count_date`<:date_today";
        $this->query($query);
        $this->bind(":date_today", $date);
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