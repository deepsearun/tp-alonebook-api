<?php


namespace app\controller\v1;

use app\model\Comment as CommentModel;
use app\BaseController;
use app\validate\CommentValidate;

/**
 * Class Comment
 * @package app\controller\v1
 * @author Xiejiawei<forceboot@hotmail.com>
 */
class Comment extends BaseController
{
    /**
     * 获取列表
     * @return \think\response\Json
     * @throws \app\lib\BaseException
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function index()
    {
        (new CommentValidate())->goCheck('list');
        $data = (new CommentModel())->getList();
        return self::showResCode('获取成功', $data);
    }

    /**
     * 点赞
     * @return \think\response\Json
     * @throws \app\lib\BaseException
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function support()
    {
        (new CommentValidate())->goCheck('support');
        (new CommentModel())->support();
        return self::showResCodeWithOutData('点赞成功');
    }

    /**
     * 删除评论
     * @return \think\response\Json
     * @throws \app\lib\BaseException
     */
    public function del()
    {
        (new CommentValidate())->goCheck('del');
        (new CommentModel())->del();
        return self::showResCodeWithOutData('删除成功');
    }

    /**
     * 回复列表
     * @return \think\response\Json
     * @throws \app\lib\BaseException
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function childList()
    {
        (new CommentValidate())->goCheck('childList');
        $data = (new CommentModel())->childList();
        return self::showResCode('获取成功', $data);
    }

    /**
     * 发表评论
     * @return \think\response\Json
     * @throws \app\lib\BaseException
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function add()
    {
        (new CommentValidate())->goCheck('add');
        $data = (new CommentModel())->add();
        return self::showResCode('发表成功',$data);
    }
}