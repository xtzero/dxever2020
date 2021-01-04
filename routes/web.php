<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

// 页面
Route::get('/', "Course\CourseController@main")->name("主页面");
Route::view('bindJwAccount', 'Blades.Course.bind')->name("绑定教务账号");
Route::get('settings', "Course\CourseController@settings")->name("设置菜单页面");
Route::get('subscribeSms', "Course\CourseController@subscribeSmsView")->name("设置短信提醒页面");
Route::view('subscribeRule', 'Blades.Course.subscribeRule')->name("订阅规则");
Route::view('about', 'Blades.Course.about')->name("关于");
Route::get('feedback', 'Course\CourseController@feedbackView')->name("意见反馈");


// 接口
Route::prefix('course')->group(function () {
    // 课表
    Route::name('验证教务处账号')->post('loginJiaowu', 'Course\CourseController@loginJiaowu');
    Route::name('抓取课表')->get('curlGetCourse', 'Course\CourseController@curlGetCourse');
    Route::name('获取课表')->get('getCourse', 'Course\CourseController@getCourse');

    // 短信
    Route::name('发送验证码')->post('subscribeSendVerifycode', 'Course\CourseController@subscribeSendVerifycode');
    Route::name('订阅短信提醒')->post('subscribeSms', 'Course\CourseController@subscribeSms');
    Route::name('取消订阅短信提醒')->post('unsubscribeSms', 'Course\CourseController@unsubscribeSms');
    Route::name('获取短信点数')->post('getSmsAmount', 'Course\CourseController@getSmsAmount');

    // 反馈
    Route::name('反馈')->post('feedback', 'Course\CourseController@feedback');

    // 返利卡广告
    Route::name('不再显示返利卡广告')->post('setNomoreVipAd', 'Course\CourseController@setNomoreVipAd');    
});

