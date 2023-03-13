<?php
/** ### Generated File. If you need to change this file manually, you must remove or change or move position this message, otherwise the file will be overwritten. ### **/
namespace application\service;

use application\core\BaseDatabaseSupport;
use application\serviceInterface\MajorServiceInterface;
use application\model\Major;
class MajorService extends BaseDatabaseSupport implements MajorServiceInterface
{
    protected $tableName = 'major';

    public function __construct($dbConn){
        $this->setDbh($dbConn);
    }
    public function findAll($perpage=0, $q_parameter=array())
    {
        //if have param
        $data_bind_where = null;

        $query = "SELECT *  ";

        $query .="FROM major AS major ";

		//default where query
        $query .=" WHERE major.`id` IS NOT NULL ";
		//custom where query
       //$query .= "WHERE major.custom_field =:customParam ";

        //gen additional query and sort order
       $additionalParam = $this->genAdditionalParamAndWhereForListPageV2($q_parameter, new Major());
       if(!empty($additionalParam)){
           if(!empty($additionalParam['additional_query'])){
               $query .= $additionalParam['additional_query'];
           }
           if(!empty($additionalParam['where_bind'])){
               $data_bind_where = $additionalParam['where_bind'];
           }
       }

        //custom where paramiter
       // $data_bind_where['custom_field']=$paramValue;
       //end
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

        return  $this->list();
    }

    public function findById($id)
    {
        $query = "SELECT *  ";

        $query .="FROM major AS major ";
        $query .="WHERE major.`id`=:id ";

        $this->query($query);
        $this->bind(":id", (int)$id);
        return  $this->single();
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

}