<?php
namespace app\common\controller\auth;

use think\Request;
use think\Validate;
use app\common\controller\auth\JWT;
use think\facade\Config;

trait Token
{
	public function getToken($user_id,$aud='all')
	{
		$key = Config::get('app.token_prefix');
        $token = array(
            "iss" => Config::get('app.token_prefix'),//签发者
            "aud" => $aud,//面向的用户
            "iat" => time(),//签发时间
            "nbf" => time(),//在什么时候jwt开始生效
            "exp" => time()+Config::get('app.'.$aud.'_token_exp'),//过期时间
            "uid" => $user_id,//记录的用户ID的信息
        );
        $jwt = JWT::encode($token, $key,'HS256');

        return $jwt;

	}

    public function decodeToken($jwt,$aud)
    {
        $key = Config::get('app.token_prefix');
        JWT::$leeway = Config::get('app.'.$aud.'_token_exp'); // $leeway in seconds
        $decoded = JWT::decode($jwt, $key, array('HS256'));
        return $decoded;
    }
}