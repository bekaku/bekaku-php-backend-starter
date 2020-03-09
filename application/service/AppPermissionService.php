<?php
namespace application\service;

use application\core\DatabaseSupport;
use application\serviceInterface\AppPermissionServiceInterface as AppPermissionServiceInterface;
use application\model\AppPermission;

use application\service\AppUserRoleRolesService as AppUserRoleRolesServices;
use application\util\ControllerUtil as ControllerUtil;
class AppPermissionService extends DatabaseSupport implements AppPermissionServiceInterface
{
    protected $tableName = 'app_permission';

    public function __construct($dbConn){
        $this->setDbh($dbConn);
    }
    public function findAll($perpage=0, $q_parameter=array())
    {
        //if have param
        $data_bind_where = null;

        $query = "SELECT app_permission.`id` AS `id` ";
        $query .=",app_permission.`name` AS `name` ";
        $query .=",app_permission.`description` AS `description` ";
        $query .=",app_permission.`status` AS `status` ";
        $query .=",app_permission.`crud_table` AS `crud_table` ";
        $query .=",app_permission.`created_user` AS `created_user` ";
        $query .=",app_permission.`created_date` AS `created_date` ";
        $query .=",app_permission.`updated_user` AS `updated_user` ";
        $query .=",app_permission.`updated_date` AS `updated_date` ";

        $query .="FROM app_permission AS app_permission ";

        //where query
        $query .=" WHERE app_permission.`id` IS NOT NULL ";

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

        /*
        //START PREPARE WHERE QUERY AND BIND VALUE FOR PAGING HELPER
        if(isset($q_parameter['q_name']) && $q_parameter['q_name']!=''){
            //for query concat
            $query .=" AND app_permission.`name` LIKE :q_name  ";
            //for bind param
            $data_bind_where['q_name'] = "%".$q_parameter['q_name']."%";//bind param for like query
        }
        if(isset($q_parameter['q_description']) && $q_parameter['q_description']!=''){
            //for query concat
            $query .=" AND app_permission.`description` LIKE :q_description  ";
            //for bind param
            $data_bind_where['q_description'] = "%".$q_parameter['q_description']."%";//bind param for like query
        }
        //END PREPARE WHERE QUERY AND BIND VALUE FOR PAGING HELPER
        */

        /*
        //PREPARE SORT ORDER
        if(isset($q_parameter['sortField']) && $q_parameter['sortField']!=""){
            $query .=" ORDER BY app_permission.`".$q_parameter['sortField']."` ";
        }else{
            $query .= " ORDER BY app_permission.`id` ";
        }
        if(isset($q_parameter['sortMode']) && $q_parameter['sortMode']!=""){
            $query .=" ".$q_parameter['sortMode']." ";
        }else{
            $query .= " ASC ";
        }
        // END SORT ORDER
        */

        //paging buider
        if($perpage>0){
            $query .= $this->pagingHelper($query,$perpage,$data_bind_where);
        }
        //regular query
        $this->query($query);

        //START BIND VALUE FOR REGULAR QUERY
        //$this->bind(":q_name", "%".$q_parameter['q_name']."%");//bind param for 'LIKE'
        //$this->bind(":q_name", $q_parameter['q_name']);//bind param for '='

//        if(isset($q_parameter['q_name']) && $q_parameter['q_name']!=''){
//            $this->bind(":q_name", "%".$q_parameter['q_name']."%");
//        }
//        if(isset($q_parameter['q_description']) && $q_parameter['q_description']!=''){
//            $this->bind(":q_description", "%".$q_parameter['q_description']."%");
//        }
        //END BIND VALUE FOR REGULAR QUERY

        //bind param for search param
        $this->genBindParamAndWhereForListPage($data_bind_where);
        return $this->resultset();
    }

    public function findAll_COPY($perpage=0, $q_parameter=array())
    {
        //if have param
        $data_bind_where = null;

        $query = "SELECT app_permission.`id` AS `id` ";
        $query .=",app_permission.`name` AS `name` ";
        $query .=",app_permission.`description` AS `description` ";
        $query .=",app_permission.`status` AS `status` ";
        $query .=",app_permission.`crud_table` AS `crud_table` ";
        $query .=",app_permission.`created_user` AS `created_user` ";
        $query .=",app_permission.`created_date` AS `created_date` ";
        $query .=",app_permission.`updated_user` AS `updated_user` ";
        $query .=",app_permission.`updated_date` AS `updated_date` ";

        $query .="FROM app_permission AS app_permission ";

        //where query
        $query .=" WHERE app_permission.`id` IS NOT NULL ";

        //START PREPARE WHERE QUERY AND BIND VALUE FOR PAGING HELPER
        if(isset($q_parameter['q_name']) && $q_parameter['q_name']!=''){
            //for query concat
            $query .=" AND app_permission.`name` LIKE :q_name  ";
            //for bind param
            $data_bind_where['q_name'] = "%".$q_parameter['q_name']."%";//bind param for like query
        }
        if(isset($q_parameter['q_description']) && $q_parameter['q_description']!=''){
            //for query concat
            $query .=" AND app_permission.`description` LIKE :q_description  ";
            //for bind param
            $data_bind_where['q_description'] = "%".$q_parameter['q_description']."%";//bind param for like query
        }
        //END PREPARE WHERE QUERY AND BIND VALUE FOR PAGING HELPER

        //PREPARE SORT ORDER
        if(isset($q_parameter['sortField']) && $q_parameter['sortField']!=""){
            $query .=" ORDER BY app_permission.`".$q_parameter['sortField']."` ";
        }else{
            $query .= " ORDER BY app_permission.`id` ";
        }
        if(isset($q_parameter['sortMode']) && $q_parameter['sortMode']!=""){
            $query .=" ".$q_parameter['sortMode']." ";
        }else{
            $query .= " ASC ";
        }
        // END SORT ORDER

        //paging buider
        if($perpage>0){
            $query .= $this->pagingHelper($query,$perpage,$data_bind_where);
        }
        //regular query
        $this->query($query);

        //START BIND VALUE FOR REGULAR QUERY
        //$this->bind(":q_name", "%".$q_parameter['q_name']."%");//bind param for 'LIKE'
        //$this->bind(":q_name", $q_parameter['q_name']);//bind param for '='

        if(isset($q_parameter['q_name']) && $q_parameter['q_name']!=''){
            $this->bind(":q_name", "%".$q_parameter['q_name']."%");
        }
        if(isset($q_parameter['q_description']) && $q_parameter['q_description']!=''){
            $this->bind(":q_description", "%".$q_parameter['q_description']."%");
        }

        //END BIND VALUE FOR REGULAR QUERY
        $resaultList =  $this->resultset();
        if (is_array($resaultList) || is_object($resaultList)){
            $findList = array();
            foreach($resaultList as $obj){
                $singleObj = null;
                $singleObj = new AppPermission($obj);
                array_push($findList, $singleObj);
            }
            return $findList;
        }
        return false;
    }

    public function findById($id)
    {
        $query = "SELECT * FROM ".$this->tableName." WHERE id=:id";
        $this->query($query);
        $this->bind(":id", $id);
        $result =  $this->single();
        if (is_array($result) || is_object($result)){
            $appPermission = new AppPermission($result);
            return $appPermission;
        }
        return false;
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

    public function createPermissionRole($data_array)
    {
        return $this->insertHelper('app_permission_role', $data_array);
    }
    public function findPermissionListByTableName($tableName)
    {
        $query = "SELECT id FROM app_permission WHERE crud_table=:tableName";
        $this->query($query);
        $this->bind(":tableName", $tableName);
        return $this->resultset();
    }

    //permission role
    public function deletePermissionRoleByPermission($permissionId)
    {
        $query = "DELETE FROM app_permission_role WHERE permission=:permission";
        $this->query($query);
        $this->bind(":permission", $permissionId);
        return $this->execute();
    }

    public function deletePermissionRoleByRole($roleId)
    {
        $query = "DELETE FROM app_permission_role WHERE role=:role";
        $this->query($query);
        $this->bind(":role", $roleId);
        return $this->execute();
    }

    public function findPermissionListByRole($roleId)
    {
        $query = "SELECT permission FROM app_permission_role WHERE role=:role";
        $this->query($query);
        $this->bind(":role", $roleId);
        return $this->resultset();
    }
    public function findPermissionByRoleAndPermission($roleId, $permissionName)
    {

        $query = "SELECT permission ";
        $query .="FROM app_permission_role AS app_permission_role ";
        $query .="LEFT JOIN app_permission AS app_permission ON app_permission_role.permission =  app_permission.id ";
        $query .="WHERE app_permission_role.role=:role_id ";
        $query .="AND  app_permission.name=:permission_name";

        $this->query($query);
        $this->bind(":role_id", (int)$roleId);
        $this->bind(":permission_name", $permissionName);
        return $this->resultset();
    }
    public function checkPermissionByUserSessionId($permission)
    {
        $userId = ControllerUtil::getUserIdSession();
        if($userId>0){
            return self::isHavePermission($userId, $permission);
        }else{
            return false;
        }
    }

    public function checkPermissionByUserId($userId, $permission)
    {
        return self::isHavePermission($userId, $permission);
    }

    private function isHavePermission($userId, $permission){
        $appUserRoleRolesService = new AppUserRoleRolesServices($this->getDbh());
        $appUserRoleRolesList = $appUserRoleRolesService->findAppUserRoleRolesByApUser($userId);
        if (!empty($appUserRoleRolesList)){

            foreach ($appUserRoleRolesList as $appUserRoleRoles){
                $roleId = $appUserRoleRoles['app_user_role'];
                $permisionList = self::findPermissionByRoleAndPermission($roleId, $permission);
                if (!empty($permisionList)){
                    return true;
                    break;
                }
            }

        }else{
            return false;
        }
        return false;
    }

}