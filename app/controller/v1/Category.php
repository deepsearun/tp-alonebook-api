<?php

namespace app\controller\v1;

use app\BaseController;
use app\model\Category as CategoryModel;
use app\validate\CategoryValidate;

/**
 * 分类
 * @package app\controller\v1
 */
class Category extends BaseController
{
    /**
     * 获取父分类
     * @return \think\response\Json
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function getParents()
    {
        $data = (new CategoryModel())->getTop();
        return self::showResCode('获取成功', $data);
    }

    /**
     * 获取子分类
     * @return \think\response\Json
     * @throws \app\lib\BaseException
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function getSon()
    {
        (new CategoryValidate())->goCheck('son');
        $data = (new CategoryModel())->getSon();
        return self::showResCode('获取成功', $data);
    }

    /**
     * 热门分类
     * @return \think\response\Json
     */
    public function getHits()
    {
        $data = (new CategoryModel())->getHits();
        return self::showResCode('获取成功', $data);
    }
}