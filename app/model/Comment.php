<?php


namespace app\model;

use think\exception\ValidateException;

/**
 * Class Comment
 * @package app\model
 * @author Xiejiawei<forceboot@hotmail.com>
 */
class Comment extends BaseModel
{
    protected $autoWriteTimestamp = true;
    protected $updateTime = false;
    protected $globalScope = ['base'];

    /**
     * 定义全局的查询范围
     * @param $query
     */
    public function scopeBase($query)
    {
        $query->with(['user' => function ($query) {
            $query->field('id,username,headimgurl,nickname');
        }]);
    }

    /**
     * 关联用户
     * @return \think\model\relation\HasOne
     */
    public function user()
    {

        return $this->hasOne('user', 'id', 'uid');
    }

    /**
     * 内容敏感词过滤
     * @param $value
     * @param $data
     * @return false|mixed|string
     */
    public function getContentAttr($value, $data)
    {
        return $this->setContentAttr($data['content']);
    }


    /**
     * 发布评论
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function add()
    {
        try {
            $create = [
                'uid' => $this->userId,
                'content' => $this->param['content'],
                'pid' => input('pid', 0),
                'mid' => $this->param['novel_id'],
                'rate' => $this->param['rate'],
                'type' => input('type', 'novel')
            ];
            (new Novel())->hits($create['mid'], $create['rate']);
            $ins = self::create($create);
            $newData = $this->find($ins['id'])->toArray();

            // 同步书籍信息
            $novel = (new Novel())->find($create['mid']);
            $novel->rating = $this->getRate($create['mid']);
            $novel->rating_count = $this->getCommentNum($create['mid']);
            $novel->save();

            $newData['child'] = [];
            $newData['childNum'] = $this->where('pid', $ins['id'])->count();
        } catch (ValidateException $e) {
            ApiException('评论发布失败', 30005);
        }
        return $newData ?? [];
    }

    /**
     * 点赞
     * @throws \app\lib\BaseException
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function support()
    {
        $id = input('id');
        if ($this->isSupport($id)) {
            ApiException('已经点过赞了');
        }
        $comment = $this->find($this->param['id']);
        $comment->up += 1;
        $comment->save();
        return cache('support_comment_' . $this->userId . $id, true);
    }

    /**
     * 是否已点赞
     * @param $id
     * @return mixed|object|\think\App
     */
    public function isSupport($id)
    {
        $cacheName = 'support_comment_' . $this->userId . $id;
        return cache($cacheName) ? true : false;
    }

    /**
     * 获取评分
     * @param $novel_id
     * @return float
     */
    public function getRate($novel_id)
    {
        $row = $this->where([
            'mid' => $novel_id,
            'pid' => 0
        ])->avg('rate');
        if (!$row) return '0.0';
        return number_format($row, 1);
    }

    /**
     * 获取评论数量
     * @param $novel_id
     * @return int
     */
    public function getCommentNum($novel_id)
    {
        return $this->where([
            'pid' => 0,
            'mid' => $novel_id
        ])->count();
    }

    /**
     * 获取列表
     * @param int $novel_id
     * @param string $type
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function getList($novel_id = 0, $type = 'novel')
    {
        $mid = input('novel_id', $novel_id);
        $desc = input('desc', 'id');
        $map = ['mid' => $mid, 'type' => $type, 'pid' => 0];
        $list = $this->where($map)
            ->page($this->page, $this->pageSize)
            ->order($desc . ' desc')
            ->select()
            ->toArray();
        foreach ($list as &$item) {
            $item['isSupport'] = $this->isSupport($item['id']);
            $child = $this->where('pid', $item['id'])
                ->order('id desc')
                ->limit(5)
                ->select();
            foreach ($child->toArray() as $row) {
                $row['child'] = [];
                array_push($list, $row);
            }
            $item['child'] = [];
            $item['childNum'] = $this->where('pid', $item['id'])->count();
        }
        $list = list_to_tree($list, $pk = 'id', $pid = 'pid', $child = 'child');
        return $this->showResArr($list, count($list));
    }

    /**
     * 回复列表
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function childList()
    {
        $map = [
            'pid' => $this->param['id']
        ];
        $list = $this->where($map)
            ->page($this->page, $this->pageSize)
            ->order('id desc')
            ->select()
            ->toArray();
        foreach ($list as &$item) {
            $item['isSupport'] = $this->isSupport($item['id']);
            $child = $this->where('pid', $item['id'])
                ->order('id desc')
                ->limit(5)
                ->select();
            foreach ($child->toArray() as $row) {
                array_push($list, $row);
            }
        }
        $total = count($list);
        $list = list_to_tree($list, $pk = 'id', $pid = 'pid', $child = 'child', $this->param['id']);
        return $this->showResArr($list, $total);
    }

    /**
     * 删除评论
     * @return bool
     */
    public function del()
    {
        return $this->where([
            'uid' => $this->userId,
            'id' => $this->param['id']
        ])->delete();
    }

    /**
     * 通过PID获取
     * @param $pid
     * @return array|\think\Model|null
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function findByPid($pid)
    {
        return $this->where('pid', $pid)->find();
    }

    /**
     * 评论敏感词屏蔽
     * @param $value
     * @return string|string[]
     */
    protected function setContentAttr($value)
    {
        $str = htmlspecialchars($value);
        $comment_key = preg_split('/[\r\n]+/', trim(config('web.comment_key'), "\r\n"));
        $str = str_replace($comment_key, '***', $str);
        return $str;
    }
}