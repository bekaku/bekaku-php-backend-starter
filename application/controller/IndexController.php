<?php
/**
 * Created by PhpStorm.
 * User: Bekaku
 * Date: 29/12/2015
 * Time: 10:30 AM
 */

namespace application\controller;

use application\core\AppController;
use application\util\i18next;

class IndexController extends AppController
{
    public function __construct($databaseConnection)
    {
        $this->setDbConn($databaseConnection);
    }

    public function index()
    {
        $this->pushDataToView = $this->setResponseStatus([], true, i18next::getTranslation('app.system_name'));
        jsonResponse($this->pushDataToView);
    }
}