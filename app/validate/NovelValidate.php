<?php

namespace app\validate;

/**
 * Class CategoryValidate
 * @package app\validate
 */
class NovelValidate extends BaseValidate
{
    protected $rule = [
        'cid' => 'require|number|>:0|isCategoryCidExist',
        'id' => 'require|number|>:0|isNovelIdExist',
        'keyword' => 'require'
    ];

    protected $scene = [
        'getBookByCid' => ['cid'],
        'detail' => ['id'],
        'search' => ['keyword']
    ];
}