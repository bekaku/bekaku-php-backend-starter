<?php
namespace application\service;

use application\core\DatabaseSupport;
use application\model\ApiClientIp;
use application\serviceInterface\ApiClientServiceInterface;
use application\model\ApiClient;
class ApiClientService extends DatabaseSupport implements ApiClientServiceInterface
{
    protected $tableName = 'api_client';

    public function __construct($dbConn){
        $this->setDbh($dbConn);
    }
    public function findAll($perpage=0, $q_parameter=array())
    {
        //if have param
        $data_bind_where = null;

        $query = "SELECT api_client.`id` AS `id` ";
        $query .=",api_client.`api_name` AS `api_name` ";
        $query .=",api_client.`api_token` AS `api_token` ";
        $query .=",api_client.`bypass` AS `bypass` ";
        $query .=",api_client.`status` AS `status` ";
        $query .=",api_client.`created_user` AS `created_user` ";
        $query .=",api_client.`created_date` AS `created_date` ";
        $query .=",api_client.`updated_user` AS `updated_user` ";
        $query .=",api_client.`updated_date` AS `updated_date` ";

        $query .="FROM api_client AS api_client ";

		//where query
        $query .=" WHERE api_client.`id` IS NOT NULL ";

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
            $query .= $this->pagingHelper($query, $perpage, $data_bind_where);
        }
        //regular query
        $this->query($query);

        //START BIND VALUE FOR REGULAR QUERY
        //$this->bind(":q_name", "%".$q_parameter['q_name']."%");//bind param for 'LIKE'
	     //$this->bind(":q_name", $q_parameter['q_name']);//bind param for '='
        //END BIND VALUE FOR REGULAR QUERY

        //bind param for search param
        $this->genBindParamAndWhereForListPage($data_bind_where);

        $resaultList =  $this->resultset();
		if (is_array($resaultList) || is_object($resaultList)){
            $findList = array();
            foreach($resaultList as $obj){
                $singleObj = null;
                $singleObj = new ApiClient($obj);
                array_push($findList, $singleObj);
            }
            return $findList;
        }
        return null;
    }
    private function basicQuery(){

        $query = "api_client.`id` AS `id` ";
        $query .=",api_client.`api_name` AS `api_name` ";
        $query .=",api_client.`api_token` AS `api_token` ";
        $query .=",api_client.`bypass` AS `bypass` ";
        $query .=",api_client.`status` AS `status` ";
        $query .=",api_client.`created_user` AS `created_user` ";
        $query .=",api_client.`created_date` AS `created_date` ";
        $query .=",api_client.`updated_user` AS `updated_user` ";
        $query .=",api_client.`updated_date` AS `updated_date` ";

        return $query;
    }
    public function findById($id)
    {
        $query = "SELECT ".$this->basicQuery()." ";

        $query .="FROM api_client AS api_client ";
        $query .="WHERE api_client.`id`=:id ";

        $this->query($query);
        $this->bind(":id", (int)$id);
        $result =  $this->single();
		if (is_array($result) || is_object($result)){
            return new ApiClient($result);
        }
        return null;
    }
    public function findByApiName($apiName)
    {
        $query = "SELECT ".$this->basicQuery()." ";

        $query .="FROM api_client AS api_client ";
        $query .="WHERE api_client.api_name=:api_name ";

        $this->query($query);
        $this->bind(":api_name", (string)$apiName);
        $result =  $this->single();
		if (is_array($result) || is_object($result)){
            return new ApiClient($result);
        }
        return null;
    }
    public function deleteById($id)
    {
        $query = "DELETE FROM ".$this->tableName." WHERE id=:id";
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

    //api_client_ip
    public function findIpByClientIdAndIp($apiId, $ip)
    {
        $query = "SELECT api_client_ip.`id` AS `id` ";
        $query .=",api_client_ip.`api_client` AS `api_client` ";
        $query .=",api_client_ip.`api_address` AS `api_address` ";
        $query .=",api_client_ip.`status` AS `status` ";
        $query .=",api_client_ip.`created_user` AS `created_user` ";
        $query .=",api_client_ip.`created_date` AS `created_date` ";
        $query .=",api_client_ip.`updated_user` AS `updated_user` ";
        $query .=",api_client_ip.`updated_date` AS `updated_date` ";

        $query .="FROM api_client_ip AS api_client_ip ";
        $query .="WHERE api_client_ip.api_client=:api_client ";
        $query .="AND api_client_ip.api_address=:api_address ";

        $this->query($query);
        $this->bind(":api_client", (int)$apiId);
        $this->bind(":api_address", (string)$ip);
        $result =  $this->single();
        if (is_array($result) || is_object($result)){
            $apiClientIp = new ApiClientIp($result);
            return $apiClientIp;
        }
        return null;
    }

}