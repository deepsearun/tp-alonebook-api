<?php


namespace app\controller\v1;

use app\BaseController;
use app\model\Bookshelf as BookshelfModel;
use app\validate\BookshelfValidate;

/**
 * Class Bookshelf
 * @package app\controller\v1
 */
class Bookshelf extends BaseController
{
    /**
     * 获取列表
     * @return \think\response\Json
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function index()
    {
        $data = (new BookshelfModel())->getList();
        return self::showResCode('获取成功', $data);
    }

    /**
     * 新增书架
     * @return \think\response\Json
     * @throws \app\lib\BaseException
     */
    public function add()
    {
        (new BookshelfValidate())->goCheck('add');
        (new BookshelfModel())->add();
        return self::showResCodeWithOutData('添加成功');
    }

    /**
     * 删除书架
     * @return \think\response\Json
     * @throws \app\lib\BaseException
     */
    public function delete()
    {
        (new BookshelfValidate())->goCheck('delete');
        (new BookshelfModel())->deleteByNId();
        return self::showResCodeWithOutData('删除成功');
    }
}