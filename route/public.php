<?php

use think\facade\Route;
use \app\middleware\GetUserId;

Route::group('api/:version', function () {
    // 获取轮播图
    Route::get('/slider', ':version.Slider/index');

    // 分类
    Route::group('/category', function () {
        Route::get('/parents', ':version.Category/getParents');
        Route::get('/son', ':version.Category/getSon');
        Route::get('/hits', ':version.Category/getHits');
    });

    // 用户
    Route::group('/user', function () {
        // 登录
        Route::post('/login', ':version.User/login');
        // 注册
        Route::post('/reg', ':version.User/reg');
        // 找回
        Route::post('/find', ':version.User/find');
        // 设置密码
        Route::post('/setNewPass', ':version.User/setNewPass');
        // 验证码
        Route::get('/getCode', ':version.User/getCode');
    });

    // 书籍
    Route::group('/novel', function () {
        // 书籍详情
        Route::get('/detail/:id', ':version.Novel/detail');
        // 通过分类获取
        Route::get('/ByCid/:cid', ':version.Novel/getBookByCid');
        // 最近更新
        Route::get('/new', ':version.Novel/getNewBook');
        // 人气推荐
        Route::get('/hits', ':version.Novel/getHitsBook');
        // 猜你喜欢
        Route::get('/like', ':version.Novel/getLikeBook');
        // 搜索
        Route::get('/search/:keyword', ':version.Novel/search');
        // 搜索补全
        Route::get('/searchComplete/:keyword', ':version.Novel/searchComplete');
        // 搜索热词
        Route::get('/searchHotWords', ':version.Novel/searchHotWords');
        // 分类随机书籍
        Route::get('/categoryRand/:cid', ':version.Novel/categoryRand');
        // 作者相关作品
        Route::get('/byAuthor/:author', ':version.Novel/byAuthor');
    })->middleware(GetUserId::class);

    // 书评
    Route::group('/comment', function () {
        // 评论列表
        Route::get('/list/:novel_id', ':version.Comment/index');
        // 评论回复列表
        Route::get('/childList/:id', ':version.Comment/childList');
    })->middleware(GetUserId::class);


    // 章节
    Route::group('/chapter', function () {
        // 章节内容
        Route::get('/content/:id/:key', ':version.Chapter/content');
        // 获取列表
        Route::get('/list/:nid', ':version.Chapter/index');
        // 更新章节
        Route::get('/update/:id', ':version.Chapter/update');
        // 保存章节
        Route::get('/save/:id/:key', ':version.Chapter/save');
    })->middleware(GetUserId::class);

});