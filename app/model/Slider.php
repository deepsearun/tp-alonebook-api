<?php

namespace app\model;

/**
 * Class SliderModel
 * @package app\model
 */
class Slider extends BaseModel
{
    protected $globalScope = ['status'];

    /**
     * 定义全局的查询范围
     * @param $query
     */
    public function scopeStatus($query)
    {
        $query->where('status', 1);
    }

    /**
     * 获取列表
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function list()
    {
        $map = isset($this->param['type']) ? ['type' => intval($this->param['type'])] : [];
        $data = $this->field('id,title,picpath,type,link')
            ->cache($this->createCacheKey('slider'), 3600)
            ->where($map)
            ->order('sort desc')
            ->select()->toArray();

        if (!$data) return [];

        // 解析数据字段link
        foreach ($data as $key => &$row) {
            if (is_numeric($row['link'])) continue;
            $arr = explode('/', trim($row['link'], '/'));
            $row['link'] = trim($arr['1'], '.html');
            $row['image_url'] = isHttpAdd(config('web.url')) . $row['picpath'];
        }

        return $this->showResArr($data, count($data));
    }
}
