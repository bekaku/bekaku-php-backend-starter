<?php

/**
 * Created by PhpStorm.
 * User: Bekaku
 * Date: 24/12/2015
 * Time: 3:21 PM
 */

use application\core\Route as Route;

/*
 * param => middleware, url,Controller name, action in controller, permission if require
 */
/*
|--------------------------------------------------------------------------
| IndexController
|--------------------------------------------------------------------------
*/

Route::get([], "index", "IndexController", "index");
/*
|--------------------------------------------------------------------------
| AppTableController
|--------------------------------------------------------------------------
*/
Route::get([], "generateStarter", "AppTableController", "crudAdd");
Route::get([], "generateStarter", "AppTableController", "crudAdd");
Route::post([], "generateStarter", "AppTableController", "crudAddProcess");
Route::post([], "generateStarterApi", "AppTableController", "addApi");
/*
|--------------------------------------------------------------------------
| PermissionController
|--------------------------------------------------------------------------
*/
Route::get(['AuthApi', 'PermissionGrant'], "permission", "PermissionController", "crudList", "permission_list");
Route::post(['AuthApi', 'PermissionGrant'], "permission", "PermissionController", "crudAdd", "permission_add");
Route::put(['AuthApi', 'PermissionGrant'], "permission", "PermissionController", "crudEdit", "permission_edit");
Route::delete(['AuthApi', 'PermissionGrant'], "permission", "PermissionController", "crudDelete", "permission_delete");
Route::get(['AuthApi', 'PermissionGrant'], "permissionReadSingle", "PermissionController", "crudReadSingle", "permission_view");





Route::get(['AuthApi'], "findAllPermissionByPaging", "PermissionController", "findAllByPaging");
Route::get([], "permissionsCrudtbl", "PermissionController", "permissionsCrudtbl");
/*
|--------------------------------------------------------------------------
| RoleController
|--------------------------------------------------------------------------
*/
Route::get(['AuthApi', 'PermissionGrant'], "role", "RoleController", "crudList", "role_list");
Route::post(['AuthApi', 'PermissionGrant'], "role", "RoleController", "crudAdd", "role_add");
Route::get(['AuthApi', 'PermissionGrant'], "roleReadSingle", "RoleController", "crudReadSingle", "role_view");
Route::put(['AuthApi', 'PermissionGrant'], "role", "RoleController", "crudEdit", "role_edit");
Route::delete(['AuthApi', 'PermissionGrant'], "role", "RoleController", "crudDelete", "role_delete");
/*
|--------------------------------------------------------------------------
| ApiClientController
|--------------------------------------------------------------------------
*/
Route::get(['AuthApi', 'PermissionGrant'], "apiClient", "ApiClientController", "crudList", "api_client_list");
Route::post(['AuthApi', 'PermissionGrant'], "apiClient", "ApiClientController", "crudAdd", "api_client_add");
Route::get(['AuthApi', 'PermissionGrant'], "apiClientReadSingle", "ApiClientController", "crudReadSingle", "api_client_view");
Route::put(['AuthApi', 'PermissionGrant'], "apiClient", "ApiClientController", "crudEdit", "api_client_edit");
Route::put(['AuthApi', 'PermissionGrant'], "apiClientRefreshToken", "ApiClientController", "refreshToken", "api_client_edit");
Route::delete(['AuthApi', 'PermissionGrant'], "apiClient", "ApiClientController", "crudDelete", "api_client_delete");
/*
|--------------------------------------------------------------------------
| ApiClientIpController
|--------------------------------------------------------------------------
*/
Route::get(['AuthApi', 'PermissionGrant'], "apiClientIp", "ApiClientIpController", "crudList", "api_client_ip_list");
Route::post(['AuthApi', 'PermissionGrant'], "apiClientIp", "ApiClientIpController", "crudAdd", "api_client_ip_add");
Route::get(['AuthApi', 'PermissionGrant'], "apiClientIpReadSingle", "ApiClientIpController", "crudReadSingle", "api_client_ip_view");
Route::put(['AuthApi', 'PermissionGrant'], "apiClientIp", "ApiClientIpController", "crudEdit", "api_client_ip_edit");
Route::delete(['AuthApi', 'PermissionGrant'], "apiClientIp", "ApiClientIpController", "crudDelete", "api_client_ip_delete");
/*
|--------------------------------------------------------------------------
| UserController
|--------------------------------------------------------------------------
*/
Route::get(['AuthApi', 'PermissionGrant'], "user", "UserController", "crudList", "user_list");
Route::post(['AuthApi', 'PermissionGrant'], "user", "UserController", "crudAdd", "user_add");
Route::get(['AuthApi', 'PermissionGrant'], "userReadSingle", "UserController", "crudReadSingle", "user_view");
Route::put(['AuthApi', 'PermissionGrant'], "user", "UserController", "crudEdit", "user_edit");
Route::put(['AuthApi', 'PermissionGrant'], "resetUserPassword", "UserController", "resetPassword", "user_edit");
Route::delete(['AuthApi', 'PermissionGrant'], "user", "UserController", "crudDelete", "user_delete");
Route::post(['AuthApi'], "changeAvatar", "UserController", "changeAvatar");
/*
|--------------------------------------------------------------------------
| AuthController
|--------------------------------------------------------------------------
*/
Route::post([], "signin", "AuthController", "signin");
Route::post(['AuthApi'], "userLogout", "AuthController", "userLogout");
Route::get(['AuthApi'], "userCheckAuth", "AuthController", "userCheckAuth");
Route::post(['AuthApi'], "userChangePwd", "AuthController", "changePwd");
Route::post(['AuthApi'], "userCheckAuth", "AuthController", "userCheckAuth");


//Application

/*
|--------------------------------------------------------------------------
| UtilController
|--------------------------------------------------------------------------
*/
Route::get([], "jsonGetServerDateAndTime", "UtilController", "jsonGetServerDateAndTime");
Route::get([], "jsongetuniqetoken", "UtilController", "jsonGetUniqeToken");
Route::get([], "getSiteMetadata", "UtilController", "getSiteMetadata");
Route::post([], "uploadImageApi", "UtilController", "uploadImageApi");

/* TestContronller*/
Route::get([], "test", "TestController", "index");
Route::get(['AuthApi'], "test-uri", "TestController", "index");
Route::post(['AuthApi'], "test", "TestController", "index");



Route::get([], "demo", "DemoController", "index");
Route::post([], "testUploadImage", "DemoController", "testUploadImage");

Route::get(['AuthApi'], 'student', 'StudentController', 'crudList', null);
Route::post(['AuthApi'], 'student', 'StudentController', 'crudAdd', null);
Route::post(['AuthApi'], 'studentV2', 'StudentController', 'crudAddV2', null);
Route::get(['AuthApi'], 'studentReadSingle', 'StudentController', 'crudReadSingle', null);
Route::put(['AuthApi'], 'student', 'StudentController', 'crudEdit', null);
Route::delete(['AuthApi'], 'student', 'StudentController', 'crudDelete', null);
Route::post([], "studentUploadImage", "StudentController", "studentUploadImage");
Route::post([], "studentUploadMultiImage", "StudentController", "studentUploadMultiImage");
Route::get([], "testChartData", "StudentController", "testChartData");


Route::get(['AuthApi'], 'major', 'MajorController', 'crudList', null);
Route::post(['AuthApi'], 'major', 'MajorController', 'crudAdd', null);
Route::get(['AuthApi'], 'majorReadSingle', 'MajorController', 'crudReadSingle', null);
Route::put(['AuthApi'], 'major', 'MajorController', 'crudEdit', null);
Route::delete(['AuthApi'], 'major', 'MajorController', 'crudDelete', null);

Route::get(['AuthApi'], 'studentImage', 'StudentImageController', 'crudList', null);
Route::post(['AuthApi'], 'studentImage', 'StudentImageController', 'crudAdd', null);
Route::get(['AuthApi'], 'studentImageReadSingle', 'StudentImageController', 'crudReadSingle', null);
Route::put(['AuthApi'], 'studentImage', 'StudentImageController', 'crudEdit', null);
Route::delete(['AuthApi'], 'studentImage', 'StudentImageController', 'crudDelete', null);
