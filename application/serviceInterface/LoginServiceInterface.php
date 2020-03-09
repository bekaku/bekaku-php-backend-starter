<?php
/**
 * Created by PhpStorm.
 * User: Bekaku
 * Date: 25/12/2015
 * Time: 3:02 PM
 */

namespace application\serviceInterface;


interface LoginServiceInterface
{
    public function isLogined();
    public function login($email, $password);
    public function logout();

}