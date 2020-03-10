<?php
include __SITE_PATH.'/application/core/init.php';
//Handling CORS requests properly is a tad more involved. Here is a function that will respond more fully (and properly).
cors();

use application\core\Route;
use application\util\FilterUtils;

$_APP_MODULE_URL = null;
if(FilterUtils::filterGetString(__BASEMODULE)){
	$_APP_MODULE_URL = FilterUtils::filterGetString(__BASEMODULE);
}else if(FilterUtils::filterPostString(__BASEMODULE)){
	$_APP_MODULE_URL = FilterUtils::filterPostString(__BASEMODULE);
}else{
//	$_APP_MODULE_URL = Route::$DEFAULT_URL_HOME;
	$_APP_MODULE_URL = 'index';
}
//sec_session_start();
Route::route($_APP_MODULE_URL);