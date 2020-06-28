<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006~2018 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------


use think\facade\Route;

/**
 * api模块路由，如果不需要路由的直接删掉就好
 * 示例的URL为/api/user
 */
Route::group('api', function () {

    //登录接口
    Route::post('auth/login','api/Auth/login');
    Route::get('index','api/Index/index');

    //自带示例，上线务必删除
    Route::resource('user','api/User') ->only(['index','save', 'read', 'update','delete']);
    Route::any('user/login','api/UserInfo/login');
    Route::post('verificationCode','api/UserInfo/verificationCode');
    Route::post('bindPhone','api/UserInfo/bindPhone');

    //公告列表
    Route::get('notice','api/Notice/index');

    //地区
    Route::get('area','api/Area/area');
    Route::post('area', 'api/Area/saveCommunity');

    //miss路由
    Route::miss(function (){
        return json([
            'code' => 404,
            'msg'  => '接口不存在',
            'data' => '',
        ], 404);
    });


});


return [

];
