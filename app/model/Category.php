<?php

namespace app\model;


/**
 * Class Category
 * @package app\model
 */
class Category extends BaseModel
{
    protected $globalScope = ['status'];
    protected $hidden = ['template_index', 'template_detail', 'template_filter', 'link'];

    /**
     * 定义全局的查询范围
     * @param $query
     */
    public function scopeStatus($query)
    {
        $query->where(['status' => 1, 'type' => 0]);
    }

    /**
     * 关联书籍
     * @return \think\model\relation\HasMany
     */
    public function novel()
    {
        return $this->hasMany('novel', 'category');
    }

    /**
     * 获取顶级分类
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function getTop()
    {
        $map = ['pid' => 0];
        $data = $this->where($map)
            ->cache($this->createCacheKey('top'), 3600)
            ->order('sort desc')
            ->select()->toArray();
        if (!$data) return [];
        return $this->showResArr($data);
    }

    /**
     * 热门分类
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function getHits()
    {
        $data = $this->cache('hits_category', 3600)
            ->withCount('novel')
            ->where('pid', '>', 0)
            ->limit($this->pageSize)
            ->order('sort desc')
            ->select()
            ->toArray();
        if (!$data) return [];
        return $this->showResArr($data);
    }

    /**
     * 获取子分类
     * @param int $pid
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function getSon($pid = 0)
    {
        $pid = input('pid', $pid);
        if (empty($pid)) {
            $map[] = ['pid', '>', 0];
        } else {
            $map[] = ['pid', '=', $pid];
        }
        $data = $this->where($map)
            ->cache($this->createCacheKey('allSon'), 3600)
            ->order('sort desc')
            ->select()->toArray();
        if (!$data) return [];
        return $this->showResArr($data);
    }
}
