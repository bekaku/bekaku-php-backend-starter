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
Route::post([], "generateStarter", "AppTableController", "crudAddProcess");
/*
|--------------------------------------------------------------------------
| PermissionController
|--------------------------------------------------------------------------
*/
Route::get(['AuthApi','PermissionGrant'],"permission","PermissionController","crudList","permission_list");
Route::post(['AuthApi','PermissionGrant'],"permission","PermissionController","crudAdd","permission_add");
Route::get(['AuthApi','PermissionGrant'],"permissionReadSingle","PermissionController","crudReadSingle","permission_view");
Route::put(['AuthApi','PermissionGrant'],"permission","PermissionController","crudEdit","permission_edit");
Route::delete(['AuthApi','PermissionGrant'],"permission","PermissionController","crudDelete","permission_delete");
/*
|--------------------------------------------------------------------------
| RoleController
|--------------------------------------------------------------------------
*/
Route::get(['AuthApi','PermissionGrant'],"role","RoleController","crudList","role_list");
Route::post(['AuthApi','PermissionGrant'],"role","RoleController","crudAdd","role_add");
Route::get(['AuthApi','PermissionGrant'],"roleReadSingle","RoleController","crudReadSingle","role_view");
Route::put(['AuthApi','PermissionGrant'],"role","RoleController","crudEdit","role_edit");
Route::delete(['AuthApi','PermissionGrant'],"role","RoleController","crudDelete","role_delete");
/*
|--------------------------------------------------------------------------
| ApiClientController
|--------------------------------------------------------------------------
*/
Route::get(['AuthApi','PermissionGrant'],"apiClient","ApiClientController","crudList","api_client_list");
Route::post(['AuthApi','PermissionGrant'],"apiClient","ApiClientController","crudAdd","api_client_add");
Route::get(['AuthApi','PermissionGrant'],"apiClientReadSingle","ApiClientController","crudReadSingle","api_client_view");
Route::put(['AuthApi','PermissionGrant'],"apiClient","ApiClientController","crudEdit","api_client_edit");
Route::delete(['AuthApi','PermissionGrant'],"apiClient","ApiClientController","crudDelete","api_client_delete");
/*
|--------------------------------------------------------------------------
| ApiClientIpController
|--------------------------------------------------------------------------
*/
Route::get(['AuthApi','PermissionGrant'],"apiClientIp","ApiClientIpController","crudList","api_client_ip_list");
Route::post(['AuthApi','PermissionGrant'],"apiClientIp","ApiClientIpController","crudAdd","api_client_ip_add");
Route::get(['AuthApi','PermissionGrant'],"apiClientIpReadSingle","ApiClientIpController","crudReadSingle","api_client_ip_view");
Route::put(['AuthApi','PermissionGrant'],"apiClientIp","ApiClientIpController","crudEdit","api_client_ip_edit");
Route::delete(['AuthApi','PermissionGrant'],"apiClientIp","ApiClientIpController","crudDelete","api_client_ip_delete");
/*
|--------------------------------------------------------------------------
| UserController
|--------------------------------------------------------------------------
*/
Route::get(['AuthApi','PermissionGrant'],"user","UserController","crudList","user_list");
Route::post(['AuthApi','PermissionGrant'],"user","UserController","crudAdd","user_add");
Route::get(['AuthApi','PermissionGrant'],"userReadSingle","UserController","crudReadSingle","user_view");
Route::put(['AuthApi','PermissionGrant'],"user","UserController","crudEdit","user_edit");
Route::delete(['AuthApi','PermissionGrant'],"user","UserController","crudDelete","user_delete");
/*
|--------------------------------------------------------------------------
| AuthController
|--------------------------------------------------------------------------
*/
Route::post([],"signin","AuthController","signin");
Route::post(['AuthApi'],"userLogout","AuthenApiController","userLogout");
Route::get(['AuthApi'],"userCheckAuth","AuthController","userCheckAuth");
Route::post(['AuthApi'],"userChangePwd","AuthController","changePwd");

/*
|--------------------------------------------------------------------------
| UtilController
|--------------------------------------------------------------------------
*/
Route::get([], "jsonGetServerDateAndTime", "UtilController", "jsonGetServerDateAndTime");
Route::get([], "jsongetuniqetoken", "UtilController", "jsonGetUniqeToken");

/* TestContronller*/
Route::get([], "test", "TestController", "index");
Route::get(['AuthApi'], "test-uri", "TestController", "index");
Route::post(['AuthApi'], "test", "TestController", "index");