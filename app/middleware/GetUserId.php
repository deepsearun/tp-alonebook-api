<?php


namespace app\middleware;

/**
 * Class GetUserId
 * @package app\middleware
 */
class GetUserId
{
    /**
     * 获取用户userId
     * @param object $request
     * @param \Closure $next
     * @return mixed
     */
    public function handle($request, \Closure $next)
    {
        // 获取头部信息
        $param = $request->header();
        if (array_key_exists('authorization', $param)) {
            if ($user = cache($param['authorization'])) {
                $request->userId = $user['id'];
            }
        }
        return $next($request);
    }
}