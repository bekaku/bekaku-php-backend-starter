<?php


namespace application\service;


use application\core\BaseDatabaseSupport;

class EdrDatabaseService extends BaseDatabaseSupport
{
    public function __construct($dbConn)
    {
        $this->setDbh($dbConn);
    }

    public function findConfiguration()
    {
        $query = "SELECT *  FROM configuration ";
        $this->query($query);
        return $this->single();
    }

}