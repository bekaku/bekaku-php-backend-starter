<?php

namespace application\service;

use application\core\BaseDatabaseSupport;
use application\serviceInterface\ApiClientServiceInterface;

class ApiClientService extends BaseDatabaseSupport implements ApiClientServiceInterface
{
    protected $tableName = 'api_client';

    public function __construct($dbConn)
    {
        $this->setDbh($dbConn);
    }

    public function findAll($perpage = 0, $q_parameter = array())
    {
        //if have param
        $data_bind_where = null;

        $query = "SELECT *  ";
        $query .= "FROM api_client AS api_client ";

        //default where query
        $query .= " WHERE api_client.`id` IS NOT NULL ";
        //custom where query
        //$query .= "WHERE api_client.custom_field =:customParam ";

        //gen additional query and sort order
        $additionalParam = $this->genAdditionalParamAndWhereForListPage($q_parameter, $this->tableName);
        if (!empty($additionalParam)) {
            if (!empty($additionalParam['additional_query'])) {
                $query .= $additionalParam['additional_query'];
            }
            if (!empty($additionalParam['where_bind'])) {
                $data_bind_where = $additionalParam['where_bind'];
            }
        }

        //custom where paramiter
        // $data_bind_where['custom_field']=$paramValue;
        //end
        //paging buider
        if ($perpage > 0) {
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

        $list = [];
        $reasult = $this->list();
        foreach ($reasult AS $t) {
            $t->by_pass = boolval($t->by_pass);
            $t->status = boolval($t->status);
            array_push($list, $t);
        }
        return $list;
    }

    public function findById($id)
    {
        $query = "SELECT *  ";

        $query .= "FROM api_client AS api_client ";
        $query .= "WHERE api_client.`id`=:id ";

        $this->query($query);
        $this->bind(":id", (int)$id);
        $t = $this->single();
        if ($t) {
            $t->by_pass = boolval($t->by_pass);
            $t->status = boolval($t->status);
        }
        return $t;
    }

    public function findByApiName($name)
    {
        $query = "SELECT *  ";
        $query .= "FROM api_client AS api_client ";
        $query .= "WHERE api_client.`api_name`=:api_name ";
        $this->query($query);
        $this->bind(":api_name", (string)$name);
        return $this->single();
    }

    public function deleteById($id)
    {
        $query = "DELETE FROM " . $this->tableName . " WHERE id=:id";
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
        $query = "SELECT * FROM api_client_ip AS api_client_ip  WHERE api_client_ip.api_client=:api_client AND api_client_ip.api_address=:api_address";
        $this->query($query);
        $this->bind(":api_client", (int)$apiId);
        $this->bind(":api_address", (string)$ip);
        return $this->single();
    }
}