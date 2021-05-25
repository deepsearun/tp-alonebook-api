<?php

namespace app\validate;

/**
 * Class CategoryValidate
 * @package app\validate
 */
class ChapterValidate extends BaseValidate
{
    protected $rule = [
        'id' => 'require|number|>:0|isChapterIdExist',
        'key' => 'require',
        'nid' => 'require|number|>:0|isNovelIdExist'
    ];

    protected $scene = [
        'content' => ['id', 'key'],
        'list' => ['nid']
    ];
}