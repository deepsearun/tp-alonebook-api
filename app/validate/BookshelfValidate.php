<?php


namespace app\validate;

/**
 * Class BookshelfValidate
 * @package app\validate
 */
class BookshelfValidate extends BaseValidate
{
    protected $rule = [
        'id' => 'require|number|>:0|isBookshelfIdExist',
        'novel_id' => 'require|number|>:0|isBookshelfNidExist|isNovelIdExist'
    ];

    protected $scene = [
        'add' => ['novel_id'],
    ];

    public function sceneDelete()
    {
        return $this->only(['novel_id'])
            ->remove('novel_id',['isBookshelfNidExist']);
    }
}