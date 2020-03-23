<?php
namespace application\service;

use application\core\DatabaseSupport;
use application\serviceInterface\AppUserServiceInterface;
use application\model\AppUser;
use application\util\UploadUtil;

class AppUserService extends DatabaseSupport implements AppUserServiceInterface
{
    protected $tableName = 'app_user';

    public function __construct($dbConn){
        $this->setDbh($dbConn);
    }
    public function findAll($perpage=0, $q_parameter=array())
    {
        //if have param
        $data_bind_where = null;

        $query = "SELECT app_user.`id` AS `id` ";
        $query .=",app_user.`username` AS `username` ";
        $query .=",app_user.`email` AS `email` ";
//        $query .=",app_user.`login_password` AS `login_password` ";
//        $query .=",app_user.`salt` AS `salt` ";
        $query .=",app_user.`status` AS `status` ";
        $query .=",app_user.`img_name` AS `img_name` ";
        $query .=",app_user.`created_user` AS `created_user` ";
        $query .=",app_user.`created_date` AS `created_date` ";
        $query .=",app_user.`updated_user` AS `updated_user` ";
        $query .=",app_user.`updated_date` AS `updated_date` ";

        $query .="FROM app_user AS app_user ";

		//where query
        $query .=" WHERE app_user.`id` IS NOT NULL ";

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

        $list =array();
        $listTmp =  $this->resultset();
         foreach ($listTmp AS $item){
             $item["img_api"] = UploadUtil::getUserAvatarApi($item['img_name'], $item['created_date']);
             $roles = $this->findUserRoleById($item["id"]);
             $userRoles = array();
             foreach ($roles AS $role){
                 array_push($userRoles, $role['app_user_role']);
             }
             $item["roles"] = $userRoles;
             array_push($list, $item);
         }

        return $list;
    }

    public function findById($id)
    {
        $query = "SELECT app_user.`id` AS `id` ";
        $query .=",app_user.`username` AS `username` ";
        $query .=",app_user.`email` AS `email` ";
        $query .=",app_user.`login_password` AS `login_password` ";
        $query .=",app_user.`salt` AS `salt` ";
        $query .=",app_user.`status` AS `status` ";
        $query .=",app_user.`img_name` AS `img_name` ";
        $query .=",app_user.`created_user` AS `created_user` ";
        $query .=",app_user.`created_date` AS `created_date` ";
        $query .=",app_user.`updated_user` AS `updated_user` ";
        $query .=",app_user.`updated_date` AS `updated_date` ";

        $query .="FROM app_user AS app_user ";
        $query .="WHERE app_user.`id`=:id ";

        $this->query($query);
        $this->bind(":id", (int)$id);
        $result =  $this->single();
		if (is_array($result) || is_object($result)){
            $appUser = new AppUser($result);
            return $appUser;
        }
        return false;
    }
    public function findByIdArr($id)
    {
        $query = "SELECT ";
        $query .="app_user.`username` AS `username` ";
        $query .=",app_user.`email` AS `email` ";
        $query .=",app_user.`status` AS `status` ";
        $query .=",app_user.`img_name` AS `img_name` ";
        $query .="FROM app_user AS app_user ";
        $query .="WHERE app_user.`id`=:id ";

        $this->query($query);
        $this->bind(":id", (int)$id);
        return $this->single();
    }
    public function findUserRoleById($id)
    {
        $query = "SELECT app_user_role from app_user_role_roles ";
        $query .="WHERE app_user=:id ";

        $this->query($query);
        $this->bind(":id", (int)$id);
        return $this->resultset();
    }
    public function findByUsername($userName)
    {
        $query = "SELECT app_user.`id` AS `id` ";
        $query .=",app_user.`username` AS `username` ";
        $query .=",app_user.`email` AS `email` ";
        $query .=",app_user.`login_password` AS `login_password` ";
        $query .=",app_user.`salt` AS `salt` ";
        $query .=",app_user.`status` AS `status` ";
        $query .=",app_user.`img_name` AS `img_name` ";
        $query .=",app_user.`created_user` AS `created_user` ";
        $query .=",app_user.`created_date` AS `created_date` ";
        $query .=",app_user.`updated_user` AS `updated_user` ";
        $query .=",app_user.`updated_date` AS `updated_date` ";

        $query .="FROM app_user AS app_user ";
        $query .="WHERE username=:loginName ";
        $this->query($query);
        $this->bind(":loginName", $userName);
        $result =  $this->single();
        if (is_array($result) || is_object($result)){
            $appUser = new AppUser($result);
            return $appUser;
        }
        return false;
    }

    public function findByEmail($email)
    {
        $query = "SELECT app_user.`id` AS `id` ";
        $query .=",app_user.`username` AS `username` ";
        $query .=",app_user.`email` AS `email` ";
        $query .=",app_user.`login_password` AS `login_password` ";
        $query .=",app_user.`salt` AS `salt` ";
        $query .=",app_user.`status` AS `status` ";
        $query .=",app_user.`img_name` AS `img_name` ";
        $query .=",app_user.`created_user` AS `created_user` ";
        $query .=",app_user.`created_date` AS `created_date` ";
        $query .=",app_user.`updated_user` AS `updated_user` ";
        $query .=",app_user.`updated_date` AS `updated_date` ";

        $query .="FROM app_user AS app_user ";
        $query .="WHERE email=:email ";
        $this->query($query);
        $this->bind(":email", $email);
        $result =  $this->single();
        if (is_array($result) || is_object($result)){
            $appUser = new AppUser($result);
            return $appUser;
        }
        return false;
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