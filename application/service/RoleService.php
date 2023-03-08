<?php

namespace application\service;

use application\core\BaseDatabaseSupport;
use application\model\Role;
use application\serviceInterface\RoleServiceInterface;

class RoleService extends BaseDatabaseSupport implements RoleServiceInterface
{
    protected $tableName = 'role';

    public function __construct($dbConn)
    {
        $this->setDbh($dbConn);
    }

    public function findAll($perpage = 0, $q_parameter = array())
    {
        //if have param
        $data_bind_where = null;

        $query = "SELECT *  ";

        $query .= "FROM role AS role ";

        //default where query
        $query .= " WHERE role.`id` IS NOT NULL ";
        //custom where query
        //$query .= "WHERE role.custom_field =:customParam ";

        //gen additional query and sort order
        $additionalParam = $this->genAdditionalParamAndWhereForListPageV2($q_parameter, new Role());
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
            $t->status = boolval($t->status);
            array_push($list, $t);
        }
        return $list;
    }

    public function findById($id)
    {
        $query = "SELECT *  ";

        $query .= "FROM role AS role ";
        $query .= "WHERE role.`id`=:id ";

        $this->query($query);
        $this->bind(":id", (int)$id);
        $t = $this->single();
        if ($t) {
            $t->status = boolval($t->status);
        }
        return $t;
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

    //user_role
    public function createUserRoleByArray($data_array)
    {
        return $this->insertHelper('user_role', $data_array);
    }

    public function deleteUserRoleByUserId($uid)
    {
        $query = "DELETE FROM user_role WHERE user=:uid";
        $this->query($query);
        $this->bind(":uid", (int)$uid);
        return $this->execute();
    }
}