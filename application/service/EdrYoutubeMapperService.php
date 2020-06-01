<?php

namespace application\service;

use application\core\BaseDatabaseSupport;
use application\serviceInterface\EdrYoutubeMapperServiceInterface;

class EdrYoutubeMapperService extends BaseDatabaseSupport implements EdrYoutubeMapperServiceInterface
{
    protected $tableName = 'edr_youtube_mapper';

    public function __construct($dbConn)
    {
        $this->setDbh($dbConn);
    }

    public function findAll($perpage = 0, $q_parameter = array())
    {
        //if have param
        $data_bind_where = null;

        $query = "SELECT *  ";

        $query .= "FROM edr_youtube_mapper AS edr_youtube_mapper ";

        //default where query
        $query .= " WHERE edr_youtube_mapper.`id` IS NOT NULL ";
        //custom where query
        //$query .= "WHERE edr_youtube_mapper.custom_field =:customParam ";

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

        $tmps = $this->list();
        $list = [];
        foreach ($tmps As $item) {
            $item->active = (bool)$item->active;
            $item->edrList = $this->findAllEdrlink($item->id);
            if ($item->metadata) {
                $item->metadata = json_decode($item->metadata);
            }
            array_push($list, $item);
        }
        return $list;
    }

    public function findById($id)
    {
        $query = "SELECT *  ";

        $query .= "FROM edr_youtube_mapper AS edr_youtube_mapper ";
        $query .= "WHERE edr_youtube_mapper.`id`=:id ";

        $this->query($query);
        $this->bind(":id", (int)$id);
        $item = $this->single();
        if ($item) {
            $item->active = (bool)$item->active;
            $item->edrList = $this->findAllEdrlink($item->id);
            if ($item->metadata) {
                $item->metadata = json_decode($item->metadata);
            }
        }
        return $item;
    }

    public function deleteById($id)
    {
        //delete edr link before delete youtube link
        $this->deleteEdrLinkByYoutubeId($id);

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

    //edr link
    public function findAllEdrlink($id)
    {
        $query = "SELECT *  ";

        $query .= "FROM edr_youtube_mapper_detail ";
        $query .= "WHERE edr_youtube_mapper=:edr_youtube_mapper ";

        $this->query($query);
        $this->bind(":edr_youtube_mapper", (int)$id);
        return $this->list();
    }

    public function createEdrLink($data_array)
    {
        return $this->insertHelper('edr_youtube_mapper_detail', $data_array);
    }

    public function updateEdrLink($data_array, $where_array, $whereType = 'AND')
    {
        return $this->updateHelper('edr_youtube_mapper_detail', $data_array, $where_array, $whereType);
    }

    public function deleteEdrLinkById($id)
    {
        $query = "DELETE FROM edr_youtube_mapper_detail WHERE id=:id";
        $this->query($query);
        $this->bind(":id", (int)$id);
        return $this->execute();
    }

    public function deleteEdrLinkByYoutubeId($id)
    {
        $query = "DELETE FROM edr_youtube_mapper_detail WHERE edr_youtube_mapper=:edr_youtube_mapper";
        $this->query($query);
        $this->bind(":edr_youtube_mapper", (int)$id);
        return $this->execute();
    }

    public function checkYoutubelink($edrLink)
    {
        $query = "SELECT ytm.youtube_link
FROM edr_youtube_mapper_detail md 
LEFT JOIN edr_youtube_mapper ytm ON md.edr_youtube_mapper = ytm.id
WHERE md.edr_link=:edr_link";

        $this->query($query);
        $this->bind(":edr_link", (string)$edrLink);
        return $this->single();
    }
}