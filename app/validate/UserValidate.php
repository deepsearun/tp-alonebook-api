<?php


namespace app\validate;

/**
 * Class UserValidate
 * @package app\validate
 * @author Xiejiawei<forceboot@hotmail.com>
 */
class UserValidate extends BaseValidate
{
    protected $rule = [
        'username|用户名' => 'require|min:4|max:25|isNoUsernameExist',
        'password|密码' => 'require|min:6|confirm',
        'email|邮箱' => 'require|email|isNoEmailExist|isEmailExist',
        'nickname|昵称' => 'require|max:15',
        'code|验证码' => 'require|number|checkEmailCode',
        'avatar' => 'file',
        'sex' => 'in:男,女,保密',
        'introduce' => 'max:50'
    ];

    public function sceneChangeInfo()
    {
        return $this->only(['nickname','email','sex','introduce'])
            ->remove('email',['isEmailExist']);
    }

    public function sceneAvatar()
    {
        return $this->only(['avatar']);
    }

    public function sceneSetNewPass()
    {
        return $this->only(['password', 'email', 'code'])
            ->remove('email', 'isNoEmailExist');
    }

    public function sceneGetCode()
    {
        return $this->only(['email'])
            ->remove('email', 'isNoEmailExist');
    }

    public function sceneLogin()
    {
        return $this->only(['username', 'password'])->remove('password', 'confirm')
            ->remove('username', 'isNoUsernameExist');
    }

    public function sceneFind()
    {
        return $this->only(['username'])->remove('username', 'isNoUsernameExist');
    }

    public function sceneReg()
    {
        return $this->only(['username', 'password', 'email'])->remove('email', 'isEmailExist');
    }
}