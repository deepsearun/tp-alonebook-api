<?php

namespace app\controller\v1;

use app\BaseController;
use app\validate\NovelValidate;
use app\model\Novel as NovelModel;

/**
 * Class Novel
 * @package app\controller\v1
 */
class Novel extends BaseController
{
    /**
     * 通过分类获取书籍列表
     * @return \think\response\Json
     * @throws \app\lib\BaseException
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function getBookByCid()
    {
        (new NovelValidate())->goCheck('getBookByCid');
        $data = (new NovelModel())->getBookListByCid();
        return self::showResCode('获取成功',$data);
    }

    /**
     * 最近更新
     * @return \think\response\Json
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function getNewBook()
    {
        $data = (new NovelModel())->getNewBookList();
        return self::showResCode('获取成功',$data);
    }

    /**
     * 人气推荐
     * @return \think\response\Json
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function getHitsBook()
    {
        $data = (new NovelModel())->getHitsBookList();
        return self::showResCode('获取成功',$data);
    }

    /**
     * 猜你喜欢
     * @return \think\response\Json
     */
    public function getLikeBook()
    {
        $data = (new NovelModel())->getLikeBookList();
        return self::showResCode('获取成功',$data);
    }

    /**
     * 搜索书籍
     * @return \think\response\Json
     * @throws \app\lib\BaseException
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function search()
    {
        (new NovelValidate())->goCheck('search');
        $data = (new NovelModel())->search();
        return self::showResCode('获取成功',$data);
    }

    /**
     * 搜索关键词补齐
     * @return \think\response\Json
     * @throws \app\lib\BaseException
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function searchComplete()
    {
        (new NovelValidate())->goCheck('search');
        $data = (new NovelModel())->searchCompleteWords();
        return self::showResCode('获取成功',$data);
    }

    /**
     * 搜索热词
     * @return \think\response\Json
     */
    public function searchHotWords()
    {
        $data = (new NovelModel())->searchHotWords();
        return self::showResCode('获取成功',$data);
    }

    /**
     * 书籍详情
     * @return \think\response\Json
     * @throws \app\lib\BaseException
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function detail()
    {
        (new NovelValidate())->goCheck('detail');
        $data = (new NovelModel())->detail();
        return self::showResCode('获取成功',$data);
    }

    /**
     * 分类随机推荐
     * @return \think\response\Json
     */
    public function categoryRand()
    {
        $data = (new NovelModel())->categoryRand();
        return self::showResCode('获取成功',$data);
    }

    /**
     * 作者相关作品
     * @return \think\response\Json
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function byAuthor()
    {
        $data = (new NovelModel())->aboutAuthor();
        return self::showResCode('获取成功',$data);
    }
}