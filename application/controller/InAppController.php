<?php
/**
 * Created by PhpStorm.
 * User: developers
 * Date: 3/4/2019
 * Time: 2:53 PM
 */

namespace application\controller;

use application\core\AppController;
use application\util\SystemConstant;

class InAppController extends AppController
{
    public function __construct($databaseConnection){
        $this->setDbConn($databaseConnection);
        $this->isAuthRequired = false;
        $this->headerContentType = SystemConstant::CONTENT_TYPE_TEXT_HTML;
    }
    public function __destruct()
    {
        $this->setDbConn(null);
    }


}