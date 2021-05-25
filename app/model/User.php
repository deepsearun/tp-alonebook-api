<?php

namespace app\model;

use app\lib\BaseException;
use think\exception\ValidateException;
use think\facade\Cache;
use think\model\relation\HasMany;
use think\facade\Filesystem;

/**
 * Class User
 * @package app\model
 * @author Xiejiawei<forceboot@hotmail.com>
 */
class User extends BaseModel
{
    protected $autoWriteTimestamp = true;

    protected $hidden = ['password'];

    /**
     * 关联记录
     * @return HasMany
     */
    public function record()
    {
        return $this->hasMany('Record', 'user_id', 'id');
    }

    /**
     * 判断用户是否存在
     * @param array $arr
     * @return array|bool|\think\Model|null
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function isUserExists($arr = [])
    {
        if (array_key_exists('uid', $arr)) { //用户ID
            return $this->find($arr['uid']);
        }
        if (array_key_exists('username', $arr)) { //用户名
            return $this->where('username', $arr['username'])->find();
        }
        if (array_key_exists('email', $arr)) { //邮箱
            return $this->where('email', $arr['email'])->find();
        }

        return false;
    }

    /**
     * 用户登录
     * @return string
     * @throws BaseException
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function login()
    {
        $user = $this->isUserExists([
            checkEmail($this->param['username']) ? 'email' : 'username' => $this->param['username']
        ]);
        //用户不存在
        if (!$user) ApiException('该用户不存在', 20000, 200);
        //是否被禁用
        $this->checkStatus($user->toArray());
        //验证密码
        $this->checkPassword($this->param['password'], $user['password']);
        //更新账户登录数据
        $user->login_ip = request()->ip();
        $user->login_time = time();
        $user->login += 1;
        $user->save();
        //登录成功
        return $this->createSaveToken($user->toArray());
    }

    /**
     * 获取用户信息
     * @return array|\think\Model|null
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function info()
    {
        return $this->withCount(['record' => function ($query, &$alias) {
            $alias = 'read_count';
            $query->where('chapter_id', '<>', 0);
        }])->withSum(['record' => 'read_time'], 'read_time')
            ->field('id,username,email,nickname,sex,province,city,headimgurl,introduce,status,exp,integral,recommend')
            ->find($this->userId);
    }

    /**
     * 修改用户资料
     * @return bool
     * @throws BaseException
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function changeInfo()
    {
        $user = $this->find($this->userId);
        $addr = explode('-', input('addr'));
        $sexArr = ['男' => 2, '女' => 3, '保密' => 1];
        $user->nickname = input('nickname');
        $user->sex = $sexArr[input('sex', '保密')];
        $user->introduce = input('introduce');
        $user->email = input('email');
        if (count($addr) == 3) {
            $user->province = $addr[0];
            $user->city = $addr[1];
            $user->area = $addr[2];
        }
        $res = $user->save();
        if (!$res) ApiException('资料修改失败', 20007);
        return $res;
    }

    /**
     * 修改头像
     * @return mixed|string
     * @throws BaseException
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function changeAvatar()
    {
        $file = request()->file('avatar');
        try {
            validate(['avatar' => 'fileExt:jpg,jpeg,png,gif|fileSize:10485760'])->check([
                'avatar' => $file,
            ]);
            $saveName = Filesystem::disk('public')->putFile('avatar', $file);
            $user = $this->find($this->userId);
            $user->headimgurl = getUploadFileNameUrl($saveName);
            $user->save();
            return $user->headimgurl;
        } catch (ValidateException $e) {
            ApiException($e->getMessage(), 20006);
        }
    }

    /**
     * 用户注册
     * @throws BaseException
     */
    public function reg()
    {
        $result = self::create([
            'username' => $this->param['username'],
            'password' => $this->createPassword($this->param['password']),
            'email' => $this->param['email'],
            'nickname' => '书友' . mt_rand(1000000, 9999999),
            'country' => 'CN',
            'status' => 1
        ]);
        if (!$result) ApiException('注册失败', 20004);
    }

    /**
     * 发送邮件验证码
     * @throws BaseException
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function findPass()
    {
        $user = $this->isUserExists(['username' => $this->param['username']]);
        // 用户不存在
        if (!$user) ApiException('该用户不存在', 20000, 200);
        // 用户未绑定邮箱
        if (!$user->email) ApiException('用户未绑定邮箱', 30005, 200);
        // 发送验证码
        $this->sendCode($user->email);
        return $user->email;
    }

    /**
     * 发送验证码
     * @param $email
     * @throws BaseException
     */
    public function sendCode($email = '')
    {
        $email = input('email', $email);
        //判断是否满足发送条件
        if (cache('sendCode_' . $email)) ApiException('操作频繁', 30001, 200);
        $code = random_int(1000, 9999);
        sendEmail($email, '验证码', '你的验证码为：[' . $code . ']，5分钟内有效，请及时完成后续操作');
        cache('sendCode_' . $email, $code, 60);
    }

    /**
     * 设置新密码
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function setNewPass()
    {
        $user = $this->isUserExists(['email' => $this->param['email']]);
        $user->password = $this->createPassword($this->param['password']);
        $user->save();
    }

    /**
     * 判断用户是否被禁用
     * @param array $arr
     * @return array
     * @throws BaseException
     */
    public function checkStatus($arr = [])
    {
        if ($arr['status'] == 0) ApiException('该账户已被停用', 20001, 200);
        return $arr;
    }

    /**
     * 验证密码是否正确
     * @param $input
     * @param $password
     * @return bool
     * @throws BaseException
     */
    public function checkPassword($input, $password)
    {
        if (think_ucenter_md5($input) != $password) ApiException('密码错误', 20002, 200);
        return true;
    }

    /**
     * 生成md5密码
     * @param $pass
     * @return string
     */
    public function createPassword($pass)
    {
        return think_ucenter_md5($pass);
    }

    /**
     * 创建并保存Token
     * @param array $arr
     * @return string
     * @throws BaseException
     */
    public function createSaveToken($arr = [])
    {
        // 生成token
        $token = createUniqueKey('token');
        $arr['token'] = $token;
        // 登录过期时间
        $expire = array_key_exists('expires_in', $arr) ? $arr['expires_in'] : 0;
        // 保存到缓存中
        if (!cache($token, $arr, $expire)) ApiException();
        // 返回token
        return $token;
    }

    /**
     * 注销登录
     * @return bool
     * @throws BaseException
     */
    public function logout(): bool
    {
        if (!Cache::pull(request()->userToken)) ApiException('您已经退出了', 30004, 200);
        return true;
    }
}