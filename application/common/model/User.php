<?php
/**
 * 用户模型
 */

namespace app\common\model;


class User extends Model
{
    public $softDelete = false;
    protected $name = 'user';
    protected $autoWriteTimestamp = true;


    public function scopeUser($query,$user_id)
    {
        $query->where('user_id',$user_id);
    }

    /**
     * 用户登录
     * @param $param
     * @return mixed
     * @throws \Exception
     */
    public static function login($param)
    {
        $username = $param['username'];
        $password = $param['password'];
        $user     = self::get(['username' => $username]);
        if (!$user) {
            exception('用户不存在');
        }

        if (!password_verify($password, base64_decode($user->password))) {
            exception('密码错误');
        }

        if ((int)$user->status !== 1) {
            exception('用户被冻结');
        }
        return $user;
    }

    //加密字符串，用在登录的时候加密处理
    protected function getSignStrAttr($value, $data)
    {
        $ua = request()->header('user-agent');
        return sha1($data['id'] . $data['username'] . $ua);
    }
}
