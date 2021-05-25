<?php


namespace app\model;

use app\lib\CmsApi;

/**
 * Class NovelChapter
 * @package app\model
 */
class NovelChapter extends BaseModel
{
    protected $autoWriteTimestamp = true;

    private $baseField = 'id,novel_id,chapter,status,collect_id,updated';

    /**
     * 最新数据获取器
     * @param $value
     * @param $data
     * @return false|mixed|string
     */
    public function getUpdatedAttr($value, $data)
    {
        return json_decode($data['updated'], true);
    }

    /**
     * 章节获取器
     * @param $value
     * @param $data
     * @return false|mixed|string
     */
    public function getChapterAttr($value, $data)
    {
        return $this->deChapter($data['chapter']);
    }

    /**
     * 解码章节列表
     * @param $content
     * @return false|mixed|string
     */
    public function deChapter($content)
    {
        if (config('web.data_save_compress')) {
            ini_set("memory_limit", "-1");
            $content = @gzuncompress(base64_decode($content));
        }
        $content = json_decode($content, true);
        return $content;
    }

    /**
     * 获取章节内容
     * @return mixed|object|\think\App
     * @throws \app\lib\BaseException
     */
    public function content()
    {
        $cacheKey = 'chapter_content_' . $this->param['key'];
        if (!$result = cache($cacheKey)) {
            $result = (new CmsApi())->getChapter($this->param['id'], $this->param['key']);
            if (!$result) ApiException('章节内容读取失败');
            cache($cacheKey, $result);
        }
        return $result;
    }

    /**
     * 更新章节
     * @return mixed
     */
    public function updateChapter()
    {
        return (new CmsApi())->updateChapter($this->param['id']);
    }

    /**
     * 保存章节
     * @return mixed
     */
    public function saveChapter()
    {
        return (new CmsApi())->saveChapter($this->param['id'], $this->param['key']);
    }

    /**
     * 获取指定章节信息
     * @param $id
     * @param string $key
     * @return array|mixed
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function getChapter($id, $key = '')
    {
        if (!$key) return [];
        $row = $this->field($this->baseField)->find($id);
        if (!$row) return [];
        return isset($row['chapter'][$key]) ? $row['chapter'][$key] : [];
    }

    /**
     * 获取未读章节数量
     * @param $id
     * @param string $key
     * @return false|int|string
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function getSurplusChapter($id, $key = '')
    {
        if (!$key || !$id) return false;
        $row = $this->field($this->baseField)->find($id);
        if (!$row) return false;
        $arr = array_keys($row['chapter']);
        $index = array_search($key, $arr);
        return count($arr) - ($index + 1);
    }

    /**
     * 通过ID获取章节列表
     * @param $id
     * @return array|\think\Model|null
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function getChapterListById($id = '')
    {
        $id = input('id', $id);
        return $this->field($this->baseField)->find($id);
    }

    /**
     * 通过书籍ID获取章节列表
     * @param $id
     * @return array|\think\Model|null
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function getChapterListByNid($id = '')
    {
        $id = input('nid', $id);
        return $this->cache('getChapterListByNid' . $id, 1800)
            ->field($this->baseField)->where('novel_id', $id)->find()->toArray();
    }
}