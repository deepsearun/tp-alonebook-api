<?php

namespace app\validate;

/**
 * Class CommentValidate
 * @package app\validate
 */
class CommentValidate extends BaseValidate
{
    protected $rule = [
        'id' => 'require|number|>:0|isCommentIdExist',
        'novel_id' => 'require|number|>:0|isNovelIdExist',
        'content' => 'require|max:100',
        'pid' => 'number|>:0|isCommentIdExist'
    ];

    protected $scene = [
        'support' => ['id'],
        'del' => ['id'],
        'list' => ['novel_id'],
        'add' => ['novel_id', 'content'],
        'childList' => ['id']
    ];
}