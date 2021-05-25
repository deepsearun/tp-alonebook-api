<?php

namespace app\controller\v1;

use app\BaseController;
use app\validate\UserValidate;
use app\model\User as UserModel;

/**
 * 用户操作
 * @package app\controller\v1
 */
class User extends BaseController
{
    /**
     * 用户登录
     * @return \think\response\Json
     * @throws \app\lib\BaseException
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function login()
    {
        (new UserValidate())->goCheck('login');
        $token = (new UserModel())->login();
        return self::showResCode('登录成功', ['token' => $token]);
    }

    /**
     * 用户注册
     * @return \think\response\Json
     * @throws \app\lib\BaseException
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function reg()
    {
        (new UserValidate())->goCheck('reg');
        (new UserModel())->reg();
        return self::showResCodeWithOutData('注册成功');
    }

    /**
     * 获取用户信息
     * @return \think\response\Json
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function info()
    {
        $data = (new UserModel())->info();
        return self::showResCode('获取成功', $data);
    }

    /**
     * 找回密码
     * @return \think\response\Json
     * @throws \app\lib\BaseException
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function find()
    {
        (new UserValidate())->goCheck('find');
        $data = (new UserModel())->findPass();
        return self::showResCode('邮件已发送', $data);
    }

    /**
     * 设置新密码
     * @return \think\response\Json
     * @throws \app\lib\BaseException
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function setNewPass()
    {
        (new UserValidate())->goCheck('setNewPass');
        (new UserModel())->setNewPass();
        return self::showResCodeWithOutData('设置新密码成功');
    }

    /**
     * 获取验证码
     * @return \think\response\Json
     * @throws \app\lib\BaseException
     */
    public function getCode()
    {
        (new UserValidate())->goCheck('getCode');
        (new UserModel())->sendCode();
        return self::showResCodeWithOutData('发送验证码成功');
    }

    /**
     * 修改头像
     * @return \think\response\Json
     * @throws \app\lib\BaseException
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function changeAvatar()
    {
        (new UserValidate())->goCheck('avatar');
        $url = (new UserModel())->changeAvatar();
        return self::showResCode('修改成功', ['url' => $url]);
    }

    /**
     * 修改资料
     * @return \think\response\Json
     * @throws \app\lib\BaseException
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function changeInfo()
    {
        (new UserValidate())->goCheck('changeInfo');
        (new UserModel())->changeInfo();
        return self::showResCode('修改成功');
    }

    /**
     * 注销登录
     * @return \think\response\Json
     * @throws \app\lib\BaseException
     */
    public function logout()
    {
        (new UserModel())->logout();
        return self::showResCodeWithOutData('注销登录成功');
    }
}