<?php

use Illuminate\Support\Facades\Route;

Route::get('/', 'Index\IndexController@index');

Route::prefix('account')->group(function () {
 Route::name('登录')->post('login', 'Account\AccountController@login');
});
Route::name('首页数据')->get('homepage', 'Index\IndexController@homepage');
Route::prefix('sys')->group(function () {
    Route::name('系统用户列表')->get('sysUserList', 'Sys\UserController@sysUserList');
    Route::name('保存系统用户')->post('saveSysUser', 'Sys\UserController@saveSysUser');
    Route::name('删除系统用户')->post('deleteSysUser', 'Sys\UserController@deleteSysUser');
    Route::name('设置角色')->post('setUserRole', 'Sys\UserController@setUserRole');
    Route::name('获取用户的角色')->get('userRoleList', 'Sys\UserController@userRoleList');

    Route::name('菜单列表')->get('menuList', 'Sys\MenuController@menuList');
    Route::name('获取菜单下的角色列表')->get('menuRoleList', 'Sys\MenuController@menuRoleList');
    Route::name('保存菜单')->post('saveMenu', 'Sys\MenuController@saveMenu');
    Route::name('删除菜单')->post('deleteMenu', 'Sys\MenuController@deleteMenu');
    Route::name('保存菜单下的角色')->post('saveMenuRole', 'Sys\MenuController@saveMenuRole');

    Route::name('角色列表')->get('roleList', 'Sys\RoleController@roleList');
    Route::name('保存角色')->post('saveRole', 'Sys\RoleController@saveRole');
    Route::name('删除角色')->post('deleteRole', 'Sys\RoleController@deleteRole');
    Route::name('获取角色下菜单列表')->get('getRoleMenuList', 'Sys\RoleController@getRoleMenuList');
    Route::name('保存角色菜单')->post('saveRoleMenu', 'Sys\RoleController@saveRoleMenu');
});
Route::prefix('user')->group(function () {
    Route::name('用户列表')->get('userList', 'Fanli\UserController@userList');
    Route::name('商家列表')->get('businessList', 'Fanli\UserController@businessList');
    Route::name('商家详情')->get('businessDetail', 'Fanli\UserController@businessDetail');
});
Route::prefix('statistic')->group(function () {
    Route::name('用户统计')->get('userDailyStatistic', 'Fanli\StatisticController@userDailyStatistic');
    Route::name('用户消费排行')->get('userOrderRank', 'Fanli\StatisticController@userOrderRank');
    Route::name('商家订单排行')->get('businessOrderRank', 'Fanli\StatisticController@businessOrderRank');

    Route::name('单日订单流水')->get('orderDailySummary', 'Fanli\StatisticController@orderDailySummary');
});
