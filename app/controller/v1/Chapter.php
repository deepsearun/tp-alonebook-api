<?php


namespace app\controller\v1;

use app\BaseController;
use app\model\NovelChapter;
use app\validate\ChapterValidate;

/**
 * Class Chapter
 * @package app\controller\v1
 */
class Chapter extends BaseController
{
    /**
     * 获取章节内容
     * @return \think\response\Json
     * @throws \app\lib\BaseException
     */
    public function content()
    {
        (new ChapterValidate())->goCheck('content');
        $data = (new NovelChapter())->content();
        return self::showResCode('获取成功', $data);
    }

    /**
     * 获取章节列表
     * @return \think\response\Json
     * @throws \app\lib\BaseException
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function index()
    {
        (new ChapterValidate())->goCheck('list');
        $data = (new NovelChapter())->getChapterListByNid();
        return self::showResCode('获取成功', $data);
    }

    /**
     * 更新章节
     * @return \think\response\Json
     */
    public function update()
    {
        $data = (new NovelChapter())->updateChapter();
        return self::showResCode('获取成功', $data);
    }

    /**
     * 保存章节
     * @return \think\response\Json
     */
    public function save()
    {
        (new NovelChapter())->saveChapter();
        return self::showResCodeWithOutData('success');
    }
}