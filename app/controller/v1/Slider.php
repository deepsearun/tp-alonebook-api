<?php

namespace app\controller\v1;

use app\BaseController;
use app\model\Slider as SliderModel;

/**
 * 轮播图
 * @package app\controller\v1
 */
class Slider extends BaseController
{
    /**
     * 获取幻灯图
     * @return \think\response\Json
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function index()
    {
        $data = (new SliderModel())->list();
        return self::showResCode('获取成功', $data);
    }
}