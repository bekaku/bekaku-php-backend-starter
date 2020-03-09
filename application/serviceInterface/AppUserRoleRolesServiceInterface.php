<?php
namespace application\serviceInterface;

use application\core\AppBaseInterface as BaseServiceInterface;
interface AppUserRoleRolesServiceInterface extends BaseServiceInterface
{
    public function findAppUserRoleRolesByApUser($appUserId);
}