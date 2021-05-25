<?php


namespace app\validate;

/**
 * Class RecordValidate
 * @package app\validate
 */
class RecordValidate extends BaseValidate
{
    protected $rule = [
        'id' => 'require|number|>:0|isRecordIdExist',
        'chapter_id' => 'require|number|>:0|isChapterIdExist',
        'time' => 'require|number|>:0'
    ];

    protected $scene = [
        'delete' => ['id'],
        'time' => ['chapter_id', 'time']
    ];
}