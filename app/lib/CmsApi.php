<?php


namespace app\lib;


class CmsApi
{
    private $api;

    public $param = [];

    /**
     * 初始化
     * NovelChapter constructor.
     */
    public function __construct()
    {
        $this->api = config('web.url') . '/api/';
        $this->param = input();
    }

    /**
     * 发起请求
     * @param $path
     * @param array $param
     * @return mixed
     */
    public function request($path, $param = [])
    {
        $apiKey = config('web.api_key');
        $params = array_merge([
            'api_key' => $apiKey
        ], $param);
        $api = $this->api . $path . '?' . http_build_query($params);
        $req = Http::doGet($api);
        if ($this->isJson($req)) {
            return json_decode($req, true);
        }
        return $req;
    }

    /**
     * 是否是json格式
     * @param $string
     * @return bool
     */
    public function isJson($string)
    {
        json_decode($string);
        return (json_last_error() == JSON_ERROR_NONE);
    }

    /**
     * 获取章节内容
     * @param $id
     * @param $key
     * @return mixed
     */
    public function getChapter($id, $key)
    {
        return $this->request('novel/chapter', [
            'id' => $id,
            'key' => $key
        ]);
    }

    /**
     * 更新章节
     * @param $id
     * @return mixed
     */
    public function updateChapter($id)
    {
        return $this->request('source/index', [
            'id' => $id,
        ]);
    }

    /**
     * 保存章节
     * @param $id
     * @param $key
     * @return mixed
     */
    public function saveChapter($id, $key)
    {
        return $this->request('source/save_chapter/index', [
            'id' => $id,
            'key' => $key
        ]);
    }

    /**
     * 计划任务
     * @param $id
     * @param $key
     * @return mixed
     */
    public function cronTab($id, $key)
    {
        return $this->request('crontab/index');
    }
}