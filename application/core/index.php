<?php
include __SITE_PATH.'/application/core/init.php';
//Handling CORS requests properly is a tad more involved. Here is a function that will respond more fully (and properly).
cors();

use application\core\Route as Route;
use application\util\FilterUtils as FilterUtil;

$_APP_MODULE_URL = null;
if(FilterUtil::filterGetString(__BASEMODULE)){
	$_APP_MODULE_URL = FilterUtil::filterGetString(__BASEMODULE);
}else if(FilterUtil::filterPostString(__BASEMODULE)){
	$_APP_MODULE_URL = FilterUtil::filterPostString(__BASEMODULE);
}else{
//	$_APP_MODULE_URL = Route::$DEFAULT_URL_HOME;
	$_APP_MODULE_URL = 'index';
}
//sec_session_start();
Route::route($_APP_MODULE_URL);