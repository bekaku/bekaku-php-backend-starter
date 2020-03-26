<?php
/**
 * Created by PhpStorm.
 * User: Bekaku
 * Date: 24/12/2015
 * Time: 3:21 PM
 */
use application\core\Route as Route;

/*
 * param => url,Controller name, action in controller, permission if require
 */
/*
|--------------------------------------------------------------------------
| IndexController
|--------------------------------------------------------------------------
*/
Route::get([],"index","IndexController","index");
/*
|--------------------------------------------------------------------------
| AppTableController
|--------------------------------------------------------------------------
*/
//Route::get("apptablelist","AppTable","crudList","app_table_list");
//Route::get("apptableadd","AppTable","crudAdd","app_table_add");
//Route::post("apptableadd","AppTable","crudAddProcess","app_table_add");
//Route::post("apptabledelete","AppTable","crudDelete","app_table_delete");
//Route::get("apptableedit","AppTable","crudEdit","app_table_edit");
//Route::post("apptableedit","AppTable","crudEditProcess","app_table_edit");
Route::get([],"generateStarter","AppTableController","crudAdd");
Route::post([],"generateStarter","AppTableController","crudAddProcess");
/*
|--------------------------------------------------------------------------
| AppPermissionController
|--------------------------------------------------------------------------
*/
Route::get(['AuthApi'],"apppermission","AppPermissionController","crudList","app_permission_list");
Route::post(['AuthApi'],"apppermission","AppPermissionController","crudAdd","app_permission_add");
Route::delete(['AuthApi'],"apppermission","AppPermissionController","crudDelete","app_permission_delete");
Route::put(['AuthApi'],"apppermission","AppPermissionController","crudEdit","app_permission_edit");

/*
|--------------------------------------------------------------------------
| AppUserRoleController
|--------------------------------------------------------------------------
*/
Route::get(['AuthApi'],"appuserrole","AppUserRoleController","crudList","app_user_role_list");
Route::post(['AuthApi'],"appuserrole","AppUserRoleController","crudAdd","app_user_role_add");
Route::delete(['AuthApi'],"appuserrole","AppUserRoleController","crudDelete","app_user_role_delete");
Route::put(['AuthApi'],"appuserrole","AppUserRoleController","crudEdit","app_user_role_edit");

Route::post(['AuthApi'],"appuserrolepermission","AppUserRoleController","rolePermission","app_user_role_add");
/*
|--------------------------------------------------------------------------
| AppUserController
|--------------------------------------------------------------------------
*/
Route::get(['AuthApi'],"appuser","AppUserController","crudList","app_user_list");
Route::post(['AuthApi'],"appuser","AppUserController","crudAdd","app_user_add");
Route::put(['AuthApi'],"appuser","AppUserController","crudEdit","app_user_edit");
Route::delete(['AuthApi'],"appuserdelete","AppUserController","crudDelete","app_user_delete");
Route::post(['AuthApi'],"appuseruploadimage","AppUserController","uploadImage","app_user_add");
Route::put(['AuthApi'],"appUserChangePwd","AppUserController","changePwd","app_user_add");
/*
|--------------------------------------------------------------------------
| AuthenApiController
|--------------------------------------------------------------------------
*/
Route::post([],"appUserAuthen","AuthenApiController","appUserAuthen");
Route::get(['AuthApi'],"appUserLogout","AuthenApiController","appUserLogout");
Route::get(['AuthApi'],"checkUserAuthenApi","AuthenApiController","checkUserAuthenApi");
/*
|--------------------------------------------------------------------------
| UtilController
|--------------------------------------------------------------------------
*/
Route::post([],"changeSystemLocale","UtilController","changeSystemLocale");
Route::get([],"changeSystemLocale","UtilController","changeSystemLocale");
Route::get([],"jsongetuniqetoken","UtilController","jsonGetUniqeToken");

/* TestContronller*/
Route::get([],"test","TestController","index");
Route::get(['AuthApi'],"test-uri","TestController","index");
Route::post(['AuthApi'],"test","TestController","index");