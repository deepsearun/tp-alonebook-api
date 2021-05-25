<?php


namespace app\middleware;


use app\lib\BaseException;
use app\model\User;

/**
 * Class UserStatus
 * @package app\middleware
 */
class UserStatus
{
    /**
     * @param $request
     * @param \Closure $next
     * @return mixed
     * @throws BaseException
     */
    public function handle($request, \Closure $next)
    {
        $param = $request->userTokenUserInfo;
        (new User())->checkStatus($param);
        return $next($request);
    }
}