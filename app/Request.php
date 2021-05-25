<?php
namespace app;

// 应用请求对象类
class Request extends \think\Request
{
    /**
     * 定义全局过滤规则
     * @var array
     */
    protected $filter = ['daddslashes','strip_tags'];
}
