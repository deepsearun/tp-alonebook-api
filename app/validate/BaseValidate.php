<?php
declare (strict_types=1);

namespace app\validate;

use app\lib\BaseException;
use app\model\Bookshelf;
use app\model\Category;
use app\model\Comment;
use app\model\Novel;
use app\model\NovelChapter;
use app\model\Record;
use app\model\User;
use think\Validate;

/**
 * Class BaseValidate
 * @package app\validate
 */
class BaseValidate extends Validate
{
    /**
     * 通用数据验证 支持验证场景
     * @param string $scene 验证场景
     * @return bool
     * @throws BaseException
     */
    public function goCheck(string $scene = ''): bool
    {
        //获取所有请求参数
        $params = input();
        //是否需要验证场景
        $check = $scene ? $this->scene($scene)->check($params) : $this->check($params);
        if (!$check) {
            ApiException($this->getError(), 10000, 400);
        }
        return true;
    }

    /**
     * 判断PID是否存在
     * @param $value
     * @param string $rule
     * @param string $data
     * @param string $field
     * @return bool|string
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    protected function isCategoryPidExist($value, $rule = '', $data = '', $field = '')
    {
        if ($value == 0) return true;
        if (Category::field('pid')->where('pid', $value)->find()) {
            return true;
        }
        return "pid不存在";
    }

    /**
     * 判断CID是否存在
     * @param $value
     * @param string $rule
     * @param string $data
     * @param string $field
     * @return bool|string
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    protected function isCategoryCidExist($value, $rule = '', $data = '', $field = '')
    {
        if ($value == 0) return true;
        if (Category::field('id,pid')->where([
            ['pid', '>', 0],
            ['id', '=', $value]
        ])->find()) {
            return true;
        }
        return "cid不存在";
    }

    /**
     * 判断邮箱是否不存在
     * @param $value
     * @param string $rule
     * @param string $data
     * @param string $field
     * @return bool|string
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    protected function isNoEmailExist($value, $rule = '', $data = '', $field = '')
    {
        $user = (new User())->isUserExists(['email' => $value]);
        if (!$user) {
            return true;
        } elseif ($user['id'] == request()->userId) {
            return true;
        }
        return "该邮箱已被使用";
    }

    /**
     * 判断邮箱是否存在
     * @param $value
     * @param string $rule
     * @param string $data
     * @param string $field
     * @return bool|string
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    protected function isEmailExist($value, $rule = '', $data = '', $field = '')
    {
        if ((new User())->isUserExists(['email' => $value])) {
            return true;
        }
        return "邮箱不存在";
    }


    /**
     * 判断用户名是否不存在
     * @param $value
     * @param string $rule
     * @param string $data
     * @param string $field
     * @return bool|string
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    protected function isNoUsernameExist($value, $rule = '', $data = '', $field = '')
    {
        if (!(new User())->isUserExists(['username' => $value])) {
            return true;
        }
        return "该用户名已存在";
    }

    /**
     * 判断用户名是否存在
     * @param $value
     * @param string $rule
     * @param string $data
     * @param string $field
     * @return bool|string
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    protected function isUsernameExist($value, $rule = '', $data = '', $field = '')
    {
        if (User::field('username')->where([
            'username' => $value
        ])->find()) {
            return true;
        }
        return "用户名不存在";
    }

    /**
     * 验证验证码
     * @param $value
     * @param string $rule
     * @param string $data
     * @param string $field
     * @return bool|string
     */
    protected function checkEmailCode($value, $rule = '', $data = [], $field = '')
    {
        $beforeCode = cache('sendCode_' . $data['email']);
        if (!$beforeCode) return '验证码不存在';
        if ($value != $beforeCode) return '验证码错误';
        return true;
    }

    /**
     * 记录ID是否存在
     * @param $value
     * @param string $rule
     * @param array $data
     * @param string $field
     * @return bool
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    protected function isRecordIdExist($value, $rule = '', $data = [], $field = '')
    {
        if (Record::find($value)) {
            return true;
        }
        return 'ID不存在';
    }

    /**
     * 书籍ID是否存在
     * @param $value
     * @param string $rule
     * @param array $data
     * @param string $field
     * @return bool
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    protected function isNovelIdExist($value, $rule = '', $data = [], $field = '')
    {
        if (Novel::find($value)) {
            return true;
        }
        return 'ID不存在';
    }

    /**
     * 章节ID是否存在
     * @param $value
     * @param string $rule
     * @param array $data
     * @param string $field
     * @return bool
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    protected function isChapterIdExist($value, $rule = '', $data = [], $field = '')
    {
        if (NovelChapter::find($value)) {
            return true;
        }
        return 'ID不存在';
    }

    /**
     * 书架ID是否存在
     * @param $value
     * @param string $rule
     * @param array $data
     * @param string $field
     * @return bool|string
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    protected function isBookshelfIdExist($value, $rule = '', $data = [], $field = '')
    {
        if (Bookshelf::find($value)) {
            return true;
        }
        return 'ID不存在';
    }

    /**
     * 书籍是否存在书架
     * @param $value
     * @param string $rule
     * @param array $data
     * @param string $field
     * @return bool|string
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    protected function isBookshelfNidExist($value, $rule = '', $data = [], $field = '')
    {
        if (!Bookshelf::where('novel_id', $value)->find()) {
            return true;
        }
        return '该书已存在书架中';
    }

    /**
     * 评论是否存在
     * @param $value
     * @param string $rule
     * @param array $data
     * @param string $field
     * @return bool|string
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    protected function isCommentIdExist($value, $rule = '', $data = [], $field = '')
    {
        if (Comment::find($value)) {
            return true;
        }
        return '评论不存在';
    }
}
