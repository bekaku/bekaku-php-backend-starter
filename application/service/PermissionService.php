<?php

namespace application\service;

use application\core\BaseDatabaseSupport;
use application\serviceInterface\PermissionServiceInterface;

class PermissionService extends BaseDatabaseSupport implements PermissionServiceInterface
{
    protected $tableName = 'permission';

    private $cruds = ['list', 'add', 'view', 'edit', 'delete'];
    private $devRole = 1;

    public function __construct($dbConn)
    {
        $this->setDbh($dbConn);
    }

    public function findAll($perpage = 0, $q_parameter = array())
    {
        //if have param
        $data_bind_where = null;

        $query = "SELECT *  ";

        $query .= "FROM permission AS permission ";

        //default where query
        $query .= " WHERE permission.`id` IS NOT NULL ";
        //custom where query
        //$query .= "WHERE permission.custom_field =:customParam ";

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

        return $this->list();
    }

    public function findById($id)
    {
        $query = "SELECT *  ";

        $query .= "FROM permission AS permission ";
        $query .= "WHERE permission.`id`=:id ";

        $this->query($query);
        $this->bind(":id", (int)$id);
        return $this->single();
    }

    public function findByName($name)
    {
        $query = "SELECT *  ";
        $query .= "FROM permission AS permission ";
        $query .= "WHERE permission.`name`=:name";
        $this->query($query);
        $this->bind(":name", (string)$name);
        return $this->single();
    }

    public function deleteById($id)
    {
        //delete all role_permission by this permission
        $this->deleteRolePermissionByPermisison($id);

        $query = "DELETE FROM " . $this->tableName . " WHERE id=:id";
        $this->query($query);
        $this->bind(":id", (int)$id);
        return $this->execute();
    }

    public function createCrudPermission(string $crud)
    {
        foreach ($this->cruds AS $c) {
            $permissionName = $crud . '_' . $c;
            $permissionExist = $this->findByName($permissionName);
            if (empty($permissionExist)) {
                $this->createByArray([
                    'crud_table' => $crud,
                    'description' => $crud . ' ' . $c,
                    'name' => $permissionName,
                    'status' => 1,
                ]);
            }
        }
    }

    public function createByArray($data_array)
    {
        $lastInsertId = $this->insertHelper($this->tableName, $data_array);

        //create all permission for dev role
        $this->createRolePermission([
            'permission' => $lastInsertId,
            'role' => $this->devRole,
        ]);


        return $lastInsertId;
    }

    public function createByObject($oject)
    {
        $lastInsertId = $this->insertObjectHelper($oject);
        //create all permission for dev role
        $this->createRolePermission([
            'permission' => $lastInsertId,
            'role' => $this->devRole,
        ]);
        return $lastInsertId;
    }

    public function update($data_array, $where_array, $whereType = 'AND')
    {
        return $this->updateHelper($this->tableName, $data_array, $where_array, $whereType);
    }

    public function updateByObject($object, $where_array, $whereType = 'AND')
    {
        return $this->updateObjectHelper($object, $where_array, $whereType);
    }

    public function findPermissionListByTableName($tableName)
    {
        $query = "SELECT id FROM permission WHERE crud_table=:tableName";
        $this->query($query);
        $this->bind(":tableName", $tableName);
        return $this->list();
    }

    //role_permission
    public function createRolePermission($data_array)
    {
        return $this->insertHelper('role_permission', $data_array);
    }

    public function deleteRolePermissionByRole(int $roleId)
    {
        $query = "DELETE FROM role_permission WHERE role=:role";
        $this->query($query);
        $this->bind(":role", (int)$roleId);
        return $this->execute();
    }

    public function deleteRolePermissionByPermisison(int $permissionId)
    {
        $query = "DELETE FROM role_permission WHERE permission=:permission";
        $this->query($query);
        $this->bind(":permission", (int)$permissionId);
        return $this->execute();
    }

    public function findPermissionListByRole($roleId)
    {
        $query = "SELECT permission FROM role_permission WHERE role=:role";
        $this->query($query);
        $this->bind(":role", $roleId);
        return $this->list();
    }

    public function findPermissionByRoleAndPermission($roleId, $permissionName)
    {
        $query = "SELECT permission ";
        $query .= "FROM role_permission AS rp ";
        $query .= "LEFT JOIN permission AS p ON rp.permission =  p.id ";
        $query .= "WHERE rp.role=:role_id ";
        $query .= "AND  p.name=:permission_name";
        $this->query($query);
        $this->bind(":role_id", (int)$roleId);
        $this->bind(":permission_name", $permissionName);
        return $this->list();
    }
    public function isHavePermission($userId, $permission)
    {
        $roles = $this->findUserRolesByUser($userId);
        if (!empty($roles)) {
            foreach ($roles as $r) {
                $permisionList = $this->findPermissionByRoleAndPermission($r->role, $permission);
                if (!empty($permisionList)) {
                    return true;
                    break;
                }
            }
        } else {
            return false;
        }
        return false;
    }

    //user_role
    public function findUserRolesByUser($userId)
    {
        $query = "SELECT role FROM user_role WHERE user=:user";
        $this->query($query);
        $this->bind(":user", $userId);
        return $this->list();
    }

}