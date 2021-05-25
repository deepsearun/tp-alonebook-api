<?php


namespace app\model;

/**
 * Class Config
 * @package app\model
 */
class Config extends BaseModel
{
    /**
     * 初始化配置项
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public static function initConfig()
    {
        $list = self::cache('config_data', 86400)->select();
        $arrs = [];
        foreach ($list as $key => $value) {
            $arrs[$value['name']] = $value['value'];
        }
        config($arrs, 'web');
    }
}