<?php

namespace app\validate;

/**
 * Class CategoryValidate
 * @package app\validate
 */
class CategoryValidate extends BaseValidate
{
    protected $rule = [
        'pid' => 'number|isCategoryPidExist',
        'cid' => 'require|number|>:0|isCategoryCidExist'
    ];

    protected $scene = [
        'son' => ['pid'],
        'getBook' => ['cid']
    ];
}