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
use \app\middleware\UserAuth;
use app\middleware\UserStatus;

Route::group('api/:version', function () {
    // 用户
    Route::group('/user', function () {
        // 信息
        Route::get('/info', ':version.User/info');
        // 上传头像
        Route::post('/changeAvatar', ':version.User/changeAvatar');
        // 浏览记录
        Route::get('/record', ':version.Record/index');
        // 阅读时长
        Route::post('/readTime/:chapter_id/:time', ':version.Record/readTime');
        // 修改资料
        Route::post('/changeInfo', ':version.User/changeInfo');
        // 注销登录
        Route::get('/logout', ':version.User/logout');
    });
    // 书架
    Route::group('/bookshelf', function () {
        // 书架列表
        Route::get('/list', ':version.Bookshelf/index');
        // 添加书架
        Route::get('/add/:novel_id', ':version.Bookshelf/add');
        // 删除书架
        Route::get('/delete/:novel_id', ':version.Bookshelf/delete');
    });

    // 书评
    Route::group('/comment', function () {
        // 点赞
        Route::get('/support/:id', ':version.Comment/support');
        // 删除
        Route::get('/del/:id', ':version.Comment/del');
        // 发布
        Route::post('/add', ':version.Comment/add');
    });

})->middleware([UserAuth::class, UserStatus::class]);
