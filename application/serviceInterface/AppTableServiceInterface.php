<?php
/**
 * Created by PhpStorm.
 * User: Bekaku
 * Date: 3/1/2016
 * Time: 8:16 PM
 */

namespace application\serviceInterface;

use application\core\AppBaseInterface as BaseServiceInterface;
interface AppTableServiceInterface extends BaseServiceInterface
{

    public function getTableColunm($tableName);
    public function dropTable($tableName);
    public function findByTableName($tableName);
}