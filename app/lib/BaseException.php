<?php

namespace app\lib;

use Exception;

/**
 * Class BaseException
 * @package app\lib\exception
 * @author Xiejiawei<forceboot@hotmail.com>
 */
class BaseException extends Exception
{

    /**
     * @var int 默认状态码
     */
    public $code = 400;

    /**
     * @var string 错误信息
     */
    public $msg = '接口异常';

    /**
     * @var int 错误状态码
     */
    public $errorCode = 9999;

    /**
     * BaseException constructor.
     * @param array $params
     */
    public function __construct(array $params = [])
    {
        if (array_key_exists('code', $params)) $this->code = $params['code'];
        if (array_key_exists('msg', $params)) $this->msg = $params['msg'];
        if (array_key_exists('errorCode', $params)) $this->errorCode = $params['errorCode'];
    }

}