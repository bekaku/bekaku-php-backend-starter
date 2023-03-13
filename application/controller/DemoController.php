<?php

namespace application\controller;

use application\core\AppController;

class DemoController extends AppController{

   public function index(){

    $list = array('a', 'b','c');

    jsonResponse([
        'test'=>'Hello word',
        'test2'=>'Chanavee',
        'list'=>$list
    ]);
   }

}