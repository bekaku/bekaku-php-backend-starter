<?php
namespace application\service;

use application\core\DatabaseSupport as DatabaseSupport;
use application\serviceInterface\AppStatisticMonthServiceInterface as AppStatisticMonthServiceInterface;
use application\model\AppStatisticMonth as AppStatisticMonth;
class AppStatisticMonthService extends DatabaseSupport implements AppStatisticMonthServiceInterface
{
    protected $tableName = 'app_statistic_month';

    public function __construct($dbConn){
        $this->setDbh($dbConn);
    }
    public function findAll($perpage=0, $q_parameter=array())
    {
        //if have param
        $data_bind_where = null;

        $query = "SELECT app_statistic_month.`id` AS `id` ";
        $query .=",app_statistic_month.`month_view` AS `month_view` ";
        $query .=",app_statistic_month.`month_no` AS `month_no` ";
        $query .=",app_statistic_month.`year_no` AS `year_no` ";

        $query .="FROM app_statistic_month AS app_statistic_month ";

		//where query
        $query .=" WHERE app_statistic_month.`id` IS NOT NULL ";
        //START PREPARE WHERE QUERY AND BIND VALUE FOR PAGING HELPER
        if(isset($q_parameter['q_id']) && $q_parameter['q_id']!=''){
            //for query concat
            $query .=" AND app_statistic_month.`id` LIKE :q_id  ";
            //for bind param
            $data_bind_where['q_id'] = "%".$q_parameter['q_id']."%";//bind param for like query
        }
        if(isset($q_parameter['q_month_view']) && $q_parameter['q_month_view']!=''){
            //for query concat
            $query .=" AND app_statistic_month.`month_view` LIKE :q_month_view  ";
            //for bind param
            $data_bind_where['q_month_view'] = "%".$q_parameter['q_month_view']."%";//bind param for like query
        }
        if(isset($q_parameter['q_month_no']) && $q_parameter['q_month_no']!=''){
            //for query concat
            $query .=" AND app_statistic_month.`month_no` LIKE :q_month_no  ";
            //for bind param
            $data_bind_where['q_month_no'] = "%".$q_parameter['q_month_no']."%";//bind param for like query
        }
        if(isset($q_parameter['q_year_no']) && $q_parameter['q_year_no']!=''){
            //for query concat
            $query .=" AND app_statistic_month.`year_no` LIKE :q_year_no  ";
            //for bind param
            $data_bind_where['q_year_no'] = "%".$q_parameter['q_year_no']."%";//bind param for like query
        }
        //END PREPARE WHERE QUERY AND BIND VALUE FOR PAGING HELPER

        //PREPARE SORT ORDER
        if(isset($q_parameter['sortField']) && $q_parameter['sortField']!=""){
                $query .=" ORDER BY app_statistic_month.`".$q_parameter['sortField']."` ";
        }else{
            $query .= " ORDER BY app_statistic_month.`id` ";
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
        if(isset($q_parameter['q_month_view']) && $q_parameter['q_month_view']!=''){
            $this->bind(":q_month_view", "%".$q_parameter['q_month_view']."%");
        }
        if(isset($q_parameter['q_month_no']) && $q_parameter['q_month_no']!=''){
            $this->bind(":q_month_no", "%".$q_parameter['q_month_no']."%");
        }
        if(isset($q_parameter['q_year_no']) && $q_parameter['q_year_no']!=''){
            $this->bind(":q_year_no", "%".$q_parameter['q_year_no']."%");
        }
        //END BIND VALUE FOR REGULAR QUERY
        $resaultList =  $this->resultset();
		if (is_array($resaultList) || is_object($resaultList)){
            $findList = array();
            foreach($resaultList as $obj){
                $singleObj = null;
                $singleObj = new AppStatisticMonth($obj);
                array_push($findList, $singleObj);
            }
            return $findList;
        }
        return false;
    }

    public function findById($id)
    {
        $query = "SELECT app_statistic_month.`id` AS `id` ";
        $query .=",app_statistic_month.`month_view` AS `month_view` ";
        $query .=",app_statistic_month.`month_no` AS `month_no` ";
        $query .=",app_statistic_month.`year_no` AS `year_no` ";

        $query .="FROM app_statistic_month AS app_statistic_month ";
        $query .="WHERE app_statistic_month.`id`=:id ";

        $this->query($query);
        $this->bind(":id", (int)$id);
        $result =  $this->single();
		if (is_array($result) || is_object($result)){
            $appStatisticMonth = new AppStatisticMonth($result);
            return $appStatisticMonth;
        }
        return false;
    }
    public function findDuplicateMonthView($dYear, $dMonth)
    {
        $query = "SELECT * ";
        $query .="FROM app_statistic_month ";
        $query .="WHERE `year_no`=:d_year ";
        $query .="AND `month_no`=:d_month ";

        $this->query($query);
        $this->bind(":d_year", (int)$dYear);
        $this->bind(":d_month", (int)$dMonth);

        $result =  $this->single();
        if (is_array($result) || is_object($result)){
            $appStatisticDay = new AppStatisticMonth($result);
            return $appStatisticDay;
        }
        return false;
    }
    public function getStatisticAll(){

        $query = "SELECT SUM(month_view) AS allViews ";
        $query .="FROM app_statistic_month ";

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