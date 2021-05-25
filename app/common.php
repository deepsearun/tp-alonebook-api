<?php
// 应用公共文件
use app\lib\BaseException;
use PHPMailer\PHPMailer\PHPMailer;

/**
 * API异常输出
 * @param string $msg 错误信息
 * @param int $errorCode 错误代码
 * @param int $code 状态码
 * @throws BaseException
 */
function ApiException($msg = '接口异常', $errorCode = 999, $code = 400)
{
    throw new BaseException([
        'code' => $code,
        'msg' => $msg,
        'errorCode' => $errorCode
    ]);
}

/**
 * 生成唯一key
 * @param string $param 附加参数
 * @return string
 */
function createUniqueKey(string $param = 'token'): string
{
    $md5 = md5(uniqid(md5(microtime(true)), true));
    return sha1($md5 . md5($param));
}

/**
 * 获取文件完整url
 * @param string $url
 * @param string|null $domain
 * @return string
 */
function getFileUrl($url = '', $domain = null)
{
    if (!$url) return '';
    $domain = !is_null($domain) ? $domain . '/storage/' : config('app')['app_host'] . '/storage/';
    return $domain . $url;
}

/**
 * 获取URL地址文件后缀
 * @param $url
 * @return string|string[]
 */
function getUrlExt($url)
{
    return pathinfo($url, PATHINFO_EXTENSION);
}

/**
 * 字符串转义
 * @param $string
 * @param int $force
 * @param bool $strip
 * @return array|string
 */
function daddslashes($string, $force = 0, $strip = FALSE)
{
    !defined('MAGIC_QUOTES_GPC') && define('MAGIC_QUOTES_GPC', get_magic_quotes_gpc());
    if (!MAGIC_QUOTES_GPC || $force) {
        if (is_array($string)) {
            foreach ($string as $key => $val) {
                $string[$key] = daddslashes($val, $force, $strip);
            }
        } else {
            $string = addslashes($strip ? stripslashes($string) : $string);
        }
    }
    return $string;
}

/**
 * md5加密
 * @param $str
 * @param string $key
 * @return string
 */
function think_ucenter_md5($str, $key = 'HXyiyuanhuanlego')
{
    return '' === $str ? '' : md5(sha1($str) . $key);
}

/**
 * 验证邮箱格式
 * @param $mail
 * @return mixed
 */
function checkEmail($mail)
{
    return filter_var($mail, FILTER_VALIDATE_EMAIL);
}


/**
 * 获取当前IP所在地
 * @param $ip
 * @return array
 */
function getIpCity($ip = ''): array
{
    $ip = empty($ip) ? request()->ip() : $ip;
    $url = 'http://whois.pconline.com.cn/ipJson.jsp?json=true&ip=';
    $result = file_get_contents($url . $ip);
    $result = mb_convert_encoding($result, "UTF-8", "GB2312");
    $result = json_decode($result, true);
    return $result;
}


/**
 * 发送邮件
 * @param string $to 收件邮箱
 * @param string title 邮件标题
 * @param string $content 邮件内容(html模板渲染后的内容)
 * @return bool
 * @throws BaseException
 */
function sendEmail($to, $title = '', $content = '')
{
    $mail = new PHPMailer();
    $config = config('api.smtp');
    try {
        //服务器配置
        $mail->CharSet = $config['charset'];                     //设定邮件编码
        $mail->SMTPDebug = $config['debug'];                        // 调试模式输出
        $mail->isSMTP();                             // 使用SMTP
        $mail->Host = $config['host'];                // SMTP服务器
        $mail->SMTPAuth = true;                      // 允许 SMTP 认证
        $mail->Username = $config['user'];                // SMTP 用户名  即邮箱的用户名
        $mail->Password = $config['pass'];             // SMTP 密码  部分邮箱是授权码(例如163邮箱)
        $mail->SMTPSecure = 'ssl';                    // 允许 TLS 或者ssl协议
        $mail->Port = $config['port'];                            // 服务器端口 25 或者465 具体要看邮箱服务器支持
        $mail->setFrom($config['addr'], $config['from']);  //发件人
        $mail->addAddress($to);  // 收件人
        $mail->addReplyTo($config['addr'], $config['from']); //回复的时候回复给哪个邮箱 建议和发件人一致
        //Content
        $mail->isHTML(true);                                  // 是否以HTML文档格式发送  发送后客户端可直接显示对应HTML内容
        $mail->Subject = $title;
        $mail->Body = $content;
        $mail->AltBody = '如果邮件客户端不支持HTML则显示此内容';
        $mail->send();
        return true;
    } catch (Exception $e) {
        ApiException('邮件发送失败', 30003, 500);
    }
}

/**
 * 获取文件上传路径地址
 * @param $path
 * @return string
 */
function getUploadFileNameUrl($path)
{
    return request()->domain() . '/storage/' . $path;
}

/**
 * 生成随机字符
 * @param int $lenght
 * @return false|string
 * @throws Exception
 */
function uniqidReal($lenght = 13)
{
    if (function_exists("random_bytes")) {
        $bytes = random_bytes(ceil($lenght / 2));
    } elseif (function_exists("openssl_random_pseudo_bytes")) {
        $bytes = openssl_random_pseudo_bytes(ceil($lenght / 2));
    } else {
        $bytes = uniqid(rand(10, 99));
    }
    return substr(bin2hex($bytes), 0, $lenght);
}

/**
 * 判断域名是否携带http
 * @param $url
 * @return string
 */
function isHttpAdd($url)
{
    if (!preg_match('/^(http|https)/is', $url)) {
        $url = "http://" . $url;
    }
    return $url;
}

/**
 * 数组排序
 * @param $data
 * @param $keys
 * @param null $keys2
 * @return array
 */
function listSort($data, $keys, $keys2 = null)
{
    if (empty($data)) return [];

    $data = $data->toArray();
    $change = [];
    foreach ($data as $k => $v) {
        $change[] = is_null($keys2) ? $data[$k][$keys] : $data[$k][$keys][$keys2];
    }

    if (empty($change)) return [];

    array_multisort($change, SORT_DESC, SORT_NUMERIC, $data);
    return $data;
}

/**
 * 关键词替换
 * @param $string
 * @param $keyword
 * @return mixed
 */
function keywordReplace(&$string, $keyword): string
{
    $string = str_ireplace($keyword, '|' . $keyword . '|', $string);
    return true;
}


/**
 * 把返回的数据集转换成Tree
 * @param $list
 * @param string $pk
 * @param string $pid
 * @param string $child
 * @param int $root
 * @return array
 */
function list_to_tree($list, $pk='id', $pid = 'pid', $child = '_child', $root = 0) {
    // 创建Tree
    $tree = [];
    if(is_array($list)) {
        // 创建基于主键的数组引用
        $refer = [];
        foreach ($list as $key => $data) {
            $refer[$data[$pk]] =& $list[$key];
        }
        foreach ($list as $key => $data) {
            // 判断是否存在parent
            $parentId =  $data[$pid];
            if ($root == $parentId) {
                $tree[] =& $list[$key];
            }else{
                if (isset($refer[$parentId])) {
                    $parent =& $refer[$parentId];
                    $parent[$child][] =& $list[$key];
                }
            }
        }
    }
    return $tree;
}