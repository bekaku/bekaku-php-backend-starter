<?php
namespace application\serviceInterface;

use application\core\AppBaseInterface as BaseServiceInterface;
interface AppPermissionServiceInterface extends BaseServiceInterface
{
    public function createPermissionRole($data_array);
    public function deletePermissionRoleByPermission($permissionId);
    public function deletePermissionRoleByRole($roleId);
    public function findPermissionListByRole($roleId);
    public function findPermissionListByTableName($tableName);
    public function checkPermissionByUserSessionId($permission);
    public function checkPermissionByUserId($userId, $permission);
}