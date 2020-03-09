<?php
/**
 * Created by PhpStorm.
 * User: Bekaku
 * Date: 3/1/2016
 * Time: 8:18 PM
 */

namespace application\core;


interface AppBaseInterface
{
    /**
     * @param int $perpage
     * @return mixed
     */
    public function findAll($perpage=0, $q_parameter=array());

    /**
     * Base find by id
     * @param $id
     * @return mixed Model Class
     */
    public function findById($id);

    /**
     * Base Delete by id
     * @param $id
     * @return mixed
     */
    public function deleteById($id);

    /**
     * Base Insert By Data Array
     * @param $data_array
     * @return mixed last insert id
     */
    public function createByArray($data_array);

    /**
     * Base Insert By Model Class
     * @param $oject
     * @return mixed last insert id
     */
    public function createByObject($oject);

    /**
     * Base Update By Data Array
     * @param $data_array
     * @param $where_array
     * @param string $whereType
     * @return mixed
     */
    public function update($data_array, $where_array, $whereType='AND');

    /**
     * Base Update By Model Class
     * @param $object
     * @param $where_array
     * @param string $whereType
     * @return mixed
     */
    public function updateByObject($object, $where_array, $whereType = 'AND');

}