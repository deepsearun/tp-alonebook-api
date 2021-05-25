<?php

namespace app\model;

use app\lib\CmsApi;

/**
 * Class Novel
 * @package app\model
 */
class Novel extends BaseModel
{
    protected $globalScope = ['base'];

    /**
     * 定义全局的查询范围
     * @param $query
     */
    public function scopeBase($query)
    {
        $query->with(['category'])->where('status', 1);
    }

    /**
     * 关联分类
     * @return \think\model\relation\HasOne
     */
    public function category()
    {
        return $this->hasOne('category', 'id', 'category');
    }

    /**
     * 关联章节
     * @return \think\model\relation\HasOne
     */
    public function chapter()
    {
        return $this->hasOne('novelChapter', 'novel_id', 'id');
    }

    /**
     * 关联记录
     * @return \think\model\relation\HasOne
     */
    public function record()
    {
        return $this->hasOne('record', 'novel_id', 'id');
    }

    /**
     * 关联书架
     * @return \think\model\relation\HasOne
     */
    public function bookshelf()
    {
        return $this->hasOne('bookshelf', 'novel_id', 'id');
    }

    /**
     * 获取详情
     * @return array|\think\Model|null
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function detail()
    {
        $novelId = $this->param['id'];
        (new Record())->writeRead($novelId);
        $novel = $this->with(['chapter' => function ($query) {
            $query->field('id,novel_id,updated');
        }, 'record', 'category' => function ($query) {
            $query->field('id,title');
        }, 'bookshelf' => function ($query) {
            $query->field('novel_id');
        }])->find($novelId);
        $this->hits($novel);
        return $novel;
    }

    /**
     * 作者相关书籍
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function aboutAuthor()
    {
        $list = $this->where('author', $this->param['author'])
            ->order('hits desc')
            ->page($this->page, $this->pageSize)
            ->select();
        return $this->showResArr($list);
    }

    /**
     * 分类随机
     * @return array
     */
    public function categoryRand()
    {
        $id = (int)$this->param['cid'];
        $map = [];
        if ($id) {
            $map['category'] = $id;
        }
        $list = $this->where($map)
            ->limit($this->pageSize)
            ->orderRand()
            ->select();
        return $this->showResArr($list);
    }

    /**
     * 热度统计
     * @param $novel
     * @param int $step
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function hits($novel, $step = 1)
    {
        if (is_numeric($novel)) {
            $novel = $this->find($novel);
        }
        if (date('d', $novel->hits_time) == date('d', time())) {
            $novel->hits_day += $step;
        } else {
            $novel->hits_day = 1;
        }
        if (date('W', $novel->hits_time) == date('W', time())) {
            $novel->hits_week += $step;
        } else {
            $novel->hits_week = 1;
        }
        if (date('m', $novel->hits_time) == date('m', time())) {
            $novel->hits_month += $step;
        } else {
            $novel->hits_month = 1;
        }
        $novel->hits += $step;
        $novel->hits_time = time();
        $novel->save();
    }

    /**
     * 获取最新书籍列表
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function getNewBookList()
    {
        return $this->getList();
    }

    /**
     * 获取热度最高的书籍列表
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function getHitsBookList()
    {
        $rule = $this->createSelectRule();
        return $this->getList($rule['map'], $rule['order']);
    }

    /**
     * 猜你喜欢
     * @return array
     */
    public function getLikeBookList()
    {
        $cacheKey = $this->createCacheKey('like_book');
        $list = $this->cache($cacheKey, 600)->where('id', 'in', function ($query) {
            $query->name('record')->where('user_id', $this->userId)->field('novel_id');
        })->orderRand()->limit($this->pageSize)->select()->toArray();
        if (count($list) < $this->pageSize) {
            $list = $this->cache($cacheKey, 600)->orderRand()->limit($this->pageSize)->select()->toArray();
        }
        return $this->showResArr($list, $this->pageSize);
    }


    /**
     * 获取列表
     * @param array $map
     * @param string $sort
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function getList($map = [], $sort = 'update_time desc')
    {
        $in = $this->parseLikeIn();
        $likeMap = [
            ['category', 'in', 18]
        ];
        if (is_string($in)) {
            $likeMap = [
                ['category', 'in', $in]
            ];
        }
        $likeCount = $this->where($likeMap)->count();
        if (!$likeCount && is_string($in)) {
            $likeMap = [];
        }
        $map = array_merge($map, $likeMap);
        $list = $this->with(['chapter' => function ($query) {
            $query->field('id,novel_id,updated');
        }, 'category'])->where($map)
            ->page($this->page, $this->pageSize)
            ->order($sort)
            ->select()
            ->toArray();
        return $this->showResArr($list);
    }

    /**
     * 通过分类获取书籍列表
     * @param int $cid
     * @return array|\think\Model|null|
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function getBookListByCid($cid = 0)
    {
        $cid = input('cid', $cid);
        $map[] = ['category', '=', $cid];
        $rule = $this->createSelectRule();
        $data = $this->where($rule['map'])
            ->where($map)
            ->page($this->page, $this->pageSize)
            ->order($rule['order'])
            ->select()->toArray();
        return $this->showResArr($data, $this->where($map)->count());
    }

    /**
     * 搜索书籍
     * @return array|\think\Model|null|
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function search()
    {
        $keyword = input('keyword');
        $rule = $this->createSelectRule();
        $data = $this->where('title|author', 'like', '%' . $keyword . '%')
            ->where($rule['map'])
            ->page($this->page, $this->pageSize)
            ->order($rule['order'])
            ->select();
        return $this->showResArr($data, count($data));
    }

    /**
     * 查询关键词补齐
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function searchCompleteWords()
    {
        $this->searchHotWords();
        $keyword = input('keyword');
        $list = [];
        $list['title'] = $this->withoutGlobalScope()->field('id,title,author')
            ->where('title', 'like', '%' . $keyword . '%')
            ->limit($this->pageSize)
            ->select();
        $list['author'] = $this->withoutGlobalScope()->field('id,author')
            ->where('author', 'like', '%' . $keyword . '%')
            ->limit($this->pageSize)
            ->select();
        return $this->showResArr($list);
    }

    /**
     * 搜索热词
     * @return array
     */
    public function searchHotWords()
    {
        if (!$list = cache('searchHotWords' . date('d'))) {
            $api = 'http://api.zhuishushenqi.com/book/search-hotwords';
            $json = file_get_contents($api);
            $arr = json_decode($json, true);
            $searchWords = $arr['searchHotWords'];
            $list = array_slice($searchWords, mt_rand(0, 84), 15);
            cache('searchHotWords' . date('d'), $list, 7200);
        }
        return $this->showResArr($list);
    }

    /**
     * 生成筛选规则
     * @return array
     */
    public function createSelectRule()
    {
        $map = [];
        $order = 'update_time desc';
        $serialize = input('serialize', 'all');
        $hits = input('hits', 'all');
        $word = input('word', 'all');
        $update = input('update', 'all');


        // 完结 ，连载
        if ($serialize != 'all') {
            if ($serialize != 1) {
                $map[] = ['serialize', '<>', 1];
            } else {
                $map[] = ['serialize', '=', 1];
            }
        }

        // 排序方式
        $hisAct = [
            'day',
            'week',
            'month',
            'all',
            'new',
            'update',
            'recommend',
            'rating'
        ];
        if (in_array($hits, $hisAct)) {
            $val = [
                'all' => 'hits',
                'day' => 'hits_day',
                'week' => 'hits_week',
                'month' => 'hits_month',
                'new' => 'create_time',
                'update' => 'update_time',
                'recommend' => 'recommend',
                'rating' => 'rating_count'
            ];
            $order = $val[$hits] . ' desc';
        }

        // 按字数
        if ($word != 'all') {
            $val = [
                [
                    ['word', '<', 300000]
                ],
                [
                    ['word', '>', 300000],
                    ['word', '<', 500000],
                ],
                [
                    ['word', '>', 500000],
                    ['word', '<', 1000000],
                ],
                [
                    ['word', '>', 1000000],
                    ['word', '<', 2000000],
                ],
                [
                    ['word', '>', 2000000]
                ]
            ];
            $map[] = $val[$word][0];

            if (isset($val[$word][1])) {
                $map[] = $val[$word][1];
            }
        }

        if ($update != 'all') {
            $map[] = ['update_time', '>=', strtotime(date('Y-m-d')) - 86400 * intval($update)];
        }

        return ['map' => $map, 'order' => $order];
    }

}
