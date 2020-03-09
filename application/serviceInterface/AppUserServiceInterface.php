<?php namespace application\serviceInterface;

use application\core\AppBaseInterface;
interface AppUserServiceInterface extends AppBaseInterface{
    public function findByUsername($userName);
    public function findByEmail($email);
}