<?php

use Illuminate\Support\Facades\Route;

Route::get('/', 'Index\IndexController@index');

Route::prefix('/')->group(function () {
    Route::name('商家是否存在')->get('ifBusinessExist', 'Index\IndexController@ifBusinessExist');
    Route::name('商家基本信息')->get('baseInfo', 'Index\IndexController@baseInfo');
    Route::name('用户列表')->get('userList', 'Index\IndexController@userList');
    Route::name('订单列表')->get('orderList', 'Index\IndexController@orderList');
    Route::name('每日统计')->get('dailyStatistic', 'Index\IndexController@dailyStatistic');
});
