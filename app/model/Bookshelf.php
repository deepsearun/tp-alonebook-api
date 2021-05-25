<?php

namespace app\model;

use app\lib\CmsApi;

/**
 * Class Bookshelf
 * @package app\model
 */
class Bookshelf extends BaseModel
{
    protected $autoWriteTimestamp = true;
    protected $globalScope = ['user'];

    /**
     * 定义全局的查询范围
     * @param $query
     */
    public function scopeUser($query)
    {
        $query->where(['user_id' => $this->userId]);
    }

    /**
     * 关联影片
     * @return \think\model\relation\HasOne
     */
    public function novel()
    {
        return $this->hasOne('novel', 'id', 'novel_id');
    }

    /**
     * 关联章节
     * @return \think\model\relation\HasOne
     */
    public function novelChapter()
    {
        return $this->hasOne('NovelChapter', 'novel_id', 'novel_id');
    }

    /**
     * 获取列表
     * @param array $where
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function getList($where = [])
    {
        $list = $this->with(['novel' => function ($query) {
            $query->field('id,category,title,author,pic,word');
        }, 'novelChapter' => function ($query) {
            $query->field('id,novel_id,updated');
        }])->where($where)
            ->order('update_time desc')
            ->select();
        if (!$list) return [];
        foreach ($list as &$item) {
            if (!$isUpdate = cache('chapterUpdate' . $item['novel_id'])) {
                $isUpdate = (new CmsApi())->updateChapter($item['novel_id']);
                if ($isUpdate == true) {
                    cache('chapterUpdate' . $item['novel_id'], true);
                }
            }
            $item['isUpdate'] = $isUpdate;
            $item['reading'] = (new NovelChapter())->getChapter($item['chapter_id'], $item['chapter_key']);
            $item['surNum'] = (new NovelChapter())->getSurplusChapter($item['chapter_id'], $item['chapter_key']);
        }
        return $this->showResArr($list, $this->where($where)->count());
    }

    /**
     * 添加书架
     * @return bool
     * @throws \app\lib\BaseException
     */
    public function add()
    {
        $map = [
            'user_id' => $this->userId,
            'novel_id' => $this->param['novel_id']
        ];
        if ($this->where($map)->find()) {
            ApiException('该书已在书架中');
        }
        $res = $this->create($map);
        if (!$res) ApiException('添加书架失败');
        return true;
    }

    /**
     * 通过ID删除
     * @return bool
     */
    public function deleteById()
    {
        return $this->where('id', $this->param['id'])->delete();
    }

    /**
     * 通过nid删除
     * @return bool
     */
    public function deleteByNId()
    {
        return $this->where('novel_id', $this->param['novel_id'])->delete();
    }

    /**
     * 更新阅读章节
     * @param $novel_id
     * @param $chapter_id
     * @param $chapter_key
     * @return bool
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function updateChapter($novel_id, $chapter_id, $chapter_key)
    {
        $shelf = $this->where('novel_id', $novel_id)->find();
        if (!$shelf) return false;
        $shelf->chapter_id = $chapter_id;
        $shelf->chapter_key = $chapter_key;
        return $shelf->save();
    }
}