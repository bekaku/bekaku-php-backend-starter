<?php

namespace application\controller;

use application\core\AppController;
use application\util\UploadUtil;

class DemoController extends AppController
{
    public function index()
    {
        $list = array('a', 'b', 'c');
        jsonResponse([
            'test' => 'Hello word',
            'test2' => 'Chanavee',
            'list' => $list
        ]);
    }
    public function testUploadImage()
    {
        $uid = 'guest';
        if (isset($_FILES['fileName']) && is_uploaded_file($_FILES['fileName']['tmp_name'])) {
            $newName = UploadUtil::getUploadFileName($uid);
            $imagName = UploadUtil::uploadImgFiles($_FILES['fileName'], null, 0, $newName);
            if ($imagName) {
                jsonResponse([
                    'imageName' => $imagName,
                ]);
            }
        }
        jsonResponse([
            'error' => 'Upload fail',
        ]);
    }
}