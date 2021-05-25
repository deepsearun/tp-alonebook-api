<?php


namespace app\middleware;

use app\lib\BaseException;

/**
 * Class UserAuth
 * @package app\middleware
 * @author Xiejiawei<forceboot@hotmail.com>
 */
class UserAuth
{
    /**
     * 用户授权请求
     * @param object $request
     * @param \Closure $next
     * @return mixed
     * @throws BaseException
     */
    public function handle($request, \Closure $next)
    {
        //获取请求头信息
        $param = $request->header();
        //不含token
        if (!array_key_exists('authorization', $param)) ApiException('非法token，禁止操作', 20003, 200);
        // 当前用户 是否登录
        $token = $param['authorization'];
        $user = cache($token);
        // 未登录或已过期
        if (!$user) ApiException('非法token，请重新登录', 20003, 200);
        $request->userToken = $token;
        $request->userId = $user['id'];
        $request->userTokenUserInfo = $user;

        return $next($request);
    }
}