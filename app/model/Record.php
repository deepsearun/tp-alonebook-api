<?php


namespace app\model;

/**
 * Class Record
 * @package app\model
 * @author Xiejiawei<forceboot@hotmail.com>
 */
class Record extends BaseModel
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
            ->page($this->page, $this->pageSize)
            ->order('update_time desc')
            ->select();
        if (!$list) return [];
        foreach ($list as &$item) {
            $item['reading'] = (new NovelChapter())->getChapter($item['chapter_id'], $item['chapter_key']);
        }
        return $this->showResArr($list, $this->where($where)->count());
    }

    /**
     * 写入阅读记录
     * @param $nid
     * @param $id
     * @param $key
     * @return false
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function writeRead($nid, $id = 0, $key = '')
    {
        if (!$this->userId) return false;
        $row = $this->where('novel_id', $nid)->find();
        if (!$row) {
            $row = new Record();
            $row->user_id = $this->userId;
            $row->novel_id = $nid;
        }
        $row->chapter_id = $id;
        $row->chapter_key = $key;
        $row->update_time = time();
        return $row->save();
    }

    /**
     * 写入阅读时长
     * @param int $chapter_id
     * @param int $time
     * @return false
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function writeTime($chapter_id = 0, $time = 1)
    {
        if (!$this->userId) return false;
        $chapter_id = input('chapter_id', $chapter_id);
        $time = input('time', $time);
        $row = $this->where('chapter_id', $chapter_id)->find();
        if (!$row) return false;
        $row->read_time = $time;
        return $row->save() ? $row->read_time + $time : false;
    }
}