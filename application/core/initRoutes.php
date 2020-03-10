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
Route::get("index","Index","index");
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
Route::get("generateStarter","AppTable","crudAdd");
Route::post("generateStarter","AppTable","crudAddProcess");
/*
|--------------------------------------------------------------------------
| AppPermissionController
|--------------------------------------------------------------------------
*/
Route::get("apppermissionlist","AppPermission","crudList","app_permission_list");
Route::post("apppermissionadd","AppPermission","crudAdd","app_permission_add");
Route::get("apppermissiondelete","AppPermission","crudDelete","app_permission_delete");
Route::post("apppermissionedit","AppPermission","crudEdit","app_permission_edit");

/*
|--------------------------------------------------------------------------
| AppUserRoleController
|--------------------------------------------------------------------------
*/
Route::get("appuserrolelist","AppUserRole","crudList","app_user_role_list");
Route::post("appuserroleadd","AppUserRole","crudAdd","app_user_role_add");
Route::get("appuserroledelete","AppUserRole","crudDelete","app_user_role_delete");
Route::post("appuserroleedit","AppUserRole","crudEdit","app_user_role_edit");

Route::post("appuserrolepermission","AppUserRole","rolePermission","app_user_role_add");
/*
|--------------------------------------------------------------------------
| AppUserController
|--------------------------------------------------------------------------
*/
Route::get("appuserlist","AppUser","crudList","app_user_list");
Route::post("appuseradd","AppUser","crudAdd","app_user_add");
Route::post("appuseredit","AppUser","crudEdit","app_user_edit");
Route::get("appuserdelete","AppUser","crudDelete","app_user_delete");
Route::post("appuseruploadimage","AppUser","uploadImage","app_user_add");
Route::post("appUserChangePwd","AppUser","changePwd","app_user_add");
/*
|--------------------------------------------------------------------------
| AuthenApiController
|--------------------------------------------------------------------------
*/
Route::post("appUserAuthen","AuthenApi","appUserAuthen");
Route::get("checkUserAuthenApi","AuthenApi","checkUserAuthenApi");
/*
|--------------------------------------------------------------------------
| UtilController
|--------------------------------------------------------------------------
*/
Route::post("changeSystemLocale","Util","changeSystemLocale");
Route::get("changeSystemLocale","Util","changeSystemLocale");
Route::get("jsongetuniqetoken","Util","jsonGetUniqeToken");

/* TestContronller*/
Route::get("test","Test","index");
Route::get("test-uri","Test","index");
Route::post("test","Test","index");