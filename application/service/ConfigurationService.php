<?php
namespace application\service;

use application\core\DatabaseSupport as DatabaseSupport;
use application\serviceInterface\ConfigurationServiceInterface as ConfigurationServiceInterface;
use application\model\Configuration as Configuration;
class ConfigurationService extends DatabaseSupport implements ConfigurationServiceInterface
{
    protected $tableName = 'configuration';

    public function __construct($dbConn){
        $this->setDbh($dbConn);
    }
    public function findAll($perpage=0, $q_parameter=array())
    {
        //if have param
        $data_bind_where = null;

        $query = "SELECT configuration.`id` AS `id` ";
        $query .=",configuration.`name` AS `name` ";
        $query .=",configuration.`name_eng` AS `name_eng` ";
        $query .=",configuration.`address` AS `address` ";
        $query .=",configuration.`web` AS `web` ";
        $query .=",configuration.`phone_no` AS `phone_no` ";
        $query .=",configuration.`img_name` AS `img_name` ";
        $query .=",configuration.`fax` AS `fax` ";
        $query .=",configuration.`e_mail` AS `e_mail` ";
        $query .=",configuration.`secret_key` AS `secret_key` ";
        $query .=",configuration.`map_latitude` AS `map_latitude` ";
        $query .=",configuration.`map_longtitude` AS `map_longtitude` ";

        $query .="FROM configuration AS configuration ";

		//where query
        $query .=" WHERE configuration.`id` IS NOT NULL ";
        //START PREPARE WHERE QUERY AND BIND VALUE FOR PAGING HELPER
        if(isset($q_parameter['q_id']) && $q_parameter['q_id']!=''){
            //for query concat
            $query .=" AND configuration.`id` LIKE :q_id  ";
            //for bind param
            $data_bind_where['q_id'] = "%".$q_parameter['q_id']."%";//bind param for like query
        }
        if(isset($q_parameter['q_name']) && $q_parameter['q_name']!=''){
            //for query concat
            $query .=" AND configuration.`name` LIKE :q_name  ";
            //for bind param
            $data_bind_where['q_name'] = "%".$q_parameter['q_name']."%";//bind param for like query
        }
        if(isset($q_parameter['q_name_eng']) && $q_parameter['q_name_eng']!=''){
            //for query concat
            $query .=" AND configuration.`name_eng` LIKE :q_name_eng  ";
            //for bind param
            $data_bind_where['q_name_eng'] = "%".$q_parameter['q_name_eng']."%";//bind param for like query
        }
        if(isset($q_parameter['q_address']) && $q_parameter['q_address']!=''){
            //for query concat
            $query .=" AND configuration.`address` LIKE :q_address  ";
            //for bind param
            $data_bind_where['q_address'] = "%".$q_parameter['q_address']."%";//bind param for like query
        }
        if(isset($q_parameter['q_web']) && $q_parameter['q_web']!=''){
            //for query concat
            $query .=" AND configuration.`web` LIKE :q_web  ";
            //for bind param
            $data_bind_where['q_web'] = "%".$q_parameter['q_web']."%";//bind param for like query
        }
        if(isset($q_parameter['q_phone_no']) && $q_parameter['q_phone_no']!=''){
            //for query concat
            $query .=" AND configuration.`phone_no` LIKE :q_phone_no  ";
            //for bind param
            $data_bind_where['q_phone_no'] = "%".$q_parameter['q_phone_no']."%";//bind param for like query
        }
        if(isset($q_parameter['q_img_name']) && $q_parameter['q_img_name']!=''){
            //for query concat
            $query .=" AND configuration.`img_name` LIKE :q_img_name  ";
            //for bind param
            $data_bind_where['q_img_name'] = "%".$q_parameter['q_img_name']."%";//bind param for like query
        }
        if(isset($q_parameter['q_fax']) && $q_parameter['q_fax']!=''){
            //for query concat
            $query .=" AND configuration.`fax` LIKE :q_fax  ";
            //for bind param
            $data_bind_where['q_fax'] = "%".$q_parameter['q_fax']."%";//bind param for like query
        }
        if(isset($q_parameter['q_e_mail']) && $q_parameter['q_e_mail']!=''){
            //for query concat
            $query .=" AND configuration.`e_mail` LIKE :q_e_mail  ";
            //for bind param
            $data_bind_where['q_e_mail'] = "%".$q_parameter['q_e_mail']."%";//bind param for like query
        }
        if(isset($q_parameter['q_map_latitude']) && $q_parameter['q_map_latitude']!=''){
            //for query concat
            $query .=" AND configuration.`map_latitude` LIKE :q_map_latitude  ";
            //for bind param
            $data_bind_where['q_map_latitude'] = "%".$q_parameter['q_map_latitude']."%";//bind param for like query
        }
        if(isset($q_parameter['q_map_longtitude']) && $q_parameter['q_map_longtitude']!=''){
            //for query concat
            $query .=" AND configuration.`map_longtitude` LIKE :q_map_longtitude  ";
            //for bind param
            $data_bind_where['q_map_longtitude'] = "%".$q_parameter['q_map_longtitude']."%";//bind param for like query
        }
        //END PREPARE WHERE QUERY AND BIND VALUE FOR PAGING HELPER

        //PREPARE SORT ORDER
        if(isset($q_parameter['sortField']) && $q_parameter['sortField']!=""){
                $query .=" ORDER BY configuration.`".$q_parameter['sortField']."` ";
        }else{
            $query .= " ORDER BY configuration.`id` ";
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
        if(isset($q_parameter['q_name']) && $q_parameter['q_name']!=''){
            $this->bind(":q_name", "%".$q_parameter['q_name']."%");
        }
        if(isset($q_parameter['q_name_eng']) && $q_parameter['q_name_eng']!=''){
            $this->bind(":q_name_eng", "%".$q_parameter['q_name_eng']."%");
        }
        if(isset($q_parameter['q_address']) && $q_parameter['q_address']!=''){
            $this->bind(":q_address", "%".$q_parameter['q_address']."%");
        }
        if(isset($q_parameter['q_web']) && $q_parameter['q_web']!=''){
            $this->bind(":q_web", "%".$q_parameter['q_web']."%");
        }
        if(isset($q_parameter['q_phone_no']) && $q_parameter['q_phone_no']!=''){
            $this->bind(":q_phone_no", "%".$q_parameter['q_phone_no']."%");
        }
        if(isset($q_parameter['q_img_name']) && $q_parameter['q_img_name']!=''){
            $this->bind(":q_img_name", "%".$q_parameter['q_img_name']."%");
        }
        if(isset($q_parameter['q_fax']) && $q_parameter['q_fax']!=''){
            $this->bind(":q_fax", "%".$q_parameter['q_fax']."%");
        }
        if(isset($q_parameter['q_e_mail']) && $q_parameter['q_e_mail']!=''){
            $this->bind(":q_e_mail", "%".$q_parameter['q_e_mail']."%");
        }
        if(isset($q_parameter['q_map_latitude']) && $q_parameter['q_map_latitude']!=''){
            $this->bind(":q_map_latitude", "%".$q_parameter['q_map_latitude']."%");
        }
        if(isset($q_parameter['q_map_longtitude']) && $q_parameter['q_map_longtitude']!=''){
            $this->bind(":q_map_longtitude", "%".$q_parameter['q_map_longtitude']."%");
        }
        //END BIND VALUE FOR REGULAR QUERY
        $resaultList =  $this->resultset();
		if (is_array($resaultList) || is_object($resaultList)){
            $findList = array();
            foreach($resaultList as $obj){
                $singleObj = null;
                $singleObj = new Configuration($obj);
                array_push($findList, $singleObj);
            }
            return $findList;
        }
        return false;
    }

    public function findById($id)
    {
        $query = "SELECT configuration.`id` AS `id` ";
        $query .=",configuration.`name` AS `name` ";
        $query .=",configuration.`name_eng` AS `name_eng` ";
        $query .=",configuration.`address` AS `address` ";
        $query .=",configuration.`web` AS `web` ";
        $query .=",configuration.`phone_no` AS `phone_no` ";
        $query .=",configuration.`img_name` AS `img_name` ";
        $query .=",configuration.`fax` AS `fax` ";
        $query .=",configuration.`e_mail` AS `e_mail` ";
        $query .=",configuration.`secret_key` AS `secret_key` ";
        $query .=",configuration.`map_latitude` AS `map_latitude` ";
        $query .=",configuration.`map_longtitude` AS `map_longtitude` ";

        $query .="FROM configuration AS configuration ";
        $query .="WHERE configuration.`id`=:id ";

        $this->query($query);
        $this->bind(":id", (int)$id);
        $result =  $this->single();
		if (is_array($result) || is_object($result)){
            $configuration = new Configuration($result);
            return $configuration;
        }
        return false;
    }
    public function findByIdAndSecret($id,$secret)
    {
        $query = "SELECT configuration.`id` AS `id` ";
        $query .=",configuration.`name` AS `name` ";
        $query .=",configuration.`name_eng` AS `name_eng` ";
        $query .=",configuration.`address` AS `address` ";
        $query .=",configuration.`web` AS `web` ";
        $query .=",configuration.`phone_no` AS `phone_no` ";
        $query .=",configuration.`img_name` AS `img_name` ";
        $query .=",configuration.`fax` AS `fax` ";
        $query .=",configuration.`e_mail` AS `e_mail` ";
        $query .=",configuration.`secret_key` AS `secret_key` ";
        $query .=",configuration.`map_latitude` AS `map_latitude` ";
        $query .=",configuration.`map_longtitude` AS `map_longtitude` ";

        $query .="FROM configuration AS configuration ";
        $query .="WHERE configuration.`id`=:id ";
        $query .="AND configuration.`secret_key`=:secret_key ";

        $this->query($query);
        $this->bind(":id", (int)$id);
        $this->bind(":secret_key", $secret);
        $result =  $this->single();
        if (is_array($result) || is_object($result)){
            $configuration = new Configuration($result);
            return $configuration;
        }
        return false;
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