<?php


namespace app\controller\v1;

use app\BaseController;
use app\model\Record as RecordModel;
use app\validate\RecordValidate;

class Record extends BaseController
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
        $data = (new RecordModel())->getList();
        return self::showResCode('获取成功', $data);
    }

    /**
     * 阅读时长记录
     * @return \think\response\Json
     * @throws \app\lib\BaseException
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function readTime()
    {
        (new RecordValidate())->goCheck('time');
        $data = (new RecordModel())->writeTime();
        return self::showResCode('记录成功', ['timed' => $data]);
    }
}