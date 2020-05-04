<?php
/**
 * Created by PhpStorm.
 * User: Bekaku
 * Date: 3/1/2016
 * Time: 8:20 PM
 */

namespace application\service;

use application\core\BaseDatabaseSupport;
use application\serviceInterface\AppTableServiceInterface;
use application\model\AppTable;
class AppTableService extends BaseDatabaseSupport implements AppTableServiceInterface
{
    protected $tableName = 'app_table';

    public function __construct($dbConn){
        $this->setDbh($dbConn);
    }

    public function findAll($perpage=0, $q_parameter=array())
    {
        return[];
    }
    public function findById($id)
    {
        $query = "SELECT * FROM ".$this->tableName." WHERE id=:id";
        $this->query($query);
        $this->bind(":id", $id);
        return $this->single();
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
        return $this->single();
    }
}