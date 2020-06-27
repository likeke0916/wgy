<?php
/**
 * Api身份验证
 */

namespace app\api\traits;

//use app\common\controller\auth\JWT;
use \Firebase\JWT\JWT;
use Exception;
use Lcobucci\JWT\Builder;
use Lcobucci\JWT\Parser as TokenParser;
use Lcobucci\JWT\Signer\Hmac\Sha256;
use Lcobucci\JWT\Signer\Key;
use think\exception\HttpResponseException;
use think\facade\Config;

trait ApiAuth
{

    protected $config = [
        //token在header中的name
        'name'                   => 'Authorization',
        //加密使用的secret
        'secret'                 => 'zuoyouwelai',
        //颁发者
        'iss'                    => 'zuoyouwelai',
        //使用者
        'aud'                    => 'all',
        //过期时间，以秒为单位，默认2小时
        'ttl'                    => 30,
        //刷新时间，以秒为单位，默认14天，以
        'refresh_ttl'            => 1209600,
        //是否自动刷新，开启后可自动刷新token，附在header中返回，name为`Authorization`,字段为`Bearer `+$token
        'auto_refresh'           => true,
        //黑名单宽限期，以秒为单位，首次token刷新之后在此时间内原token可以继续访问
        'blacklist_grace_period' => 60,

    ];

    protected $token;

    public function jwtInit()
    {
        $config = config('jwt.');
        if ($config) {
            $this->config = $config;
        }
    }

    /**
     * 检查token
     */
    public function checkToken()
    {
        $config = $this->config;
        if (!in_array($this->request->action(), $this->authExcept, true)) {

            $token = $this->request->header($config['name']);
            //缺少token
            if (empty($token)) {
                throw new HttpResponseException(error('缺少token'));
            }

            $this->token  = $token;
            $token_verify = true;

            try {
                JWT::$leeway = 60;//当前时间减去60，把时间留点余地
                $decoded = JWT::decode($token, $config['key'], ['HS256']); //HS256方式，这里要和签发的时候对应
                //验证成功后给当前uid赋值
                $this->uid = $decoded->uid;
                //如果为自动刷新
//                if ($config['auto_refresh']) {
//                    $token        = $this->refreshToken();
//                    $token_verify = $token;
//                }
            } catch(\Firebase\JWT\SignatureInvalidException $e) {  //签名不正确
                $token_verify     = false;
                $token_verify_msg = $e->getMessage();
            }catch(\Firebase\JWT\BeforeValidException $e) {  // 签名在某个时间点之后才能用
                $token_verify     = false;
                $token_verify_msg = $e->getMessage();
            }catch(\Firebase\JWT\ExpiredException $e) {  // token过期
                $token_verify     = false;
                $token_verify_msg = $e->getMessage();
            }catch(Exception $e) {  //其他错误
                $token_verify     = false;
                $token_verify_msg = $e->getMessage();
            }
            //统一处理token相关错误，返回401
            if (!$token_verify) {
                throw new HttpResponseException(unauthorized('token验证错误,错误信息:'.$token_verify_msg));
            }

        }
    }


    /**
     * 获取token
     * @param $uid int 用户ID
     * @param array $data 更多数据
     * @return string
     * @throws Exception
     */
    public function getToken($uid, $data = [])
    {
        $config = $this->config;
        //追加用户信息
        $config['uid'] = $uid;
        //发放token
        $jwt = JWT::encode($config, $config['key'],'HS256');

        return (string)$jwt;
    }


    /**
     * 刷新token
     * @param $data
     * @return string
     */
    public function refreshToken()
    {
        $result        = false;
        $claim_protect = [
            'iss', 'aud', 'jti', 'iat', 'exp', 'nbf', 'uid'
        ];

        $time         = time();
        $jwt          = (new TokenParser())->parse((string)$this->token);
        $jti          = $jwt->getClaim('jti');
        $nbf_time     = $jwt->getClaim('nbf');
        $refresh_time = $nbf_time + $this->config['refresh_ttl'];

        if ($time >= $nbf_time && $time <= $refresh_time) {
            $blacklist_time = cache('token_blacklist_' . $jti);
            if ($blacklist_time) {
                $grace_period = $blacklist_time + $this->config['blacklist_grace_period'];
                if ($time < $grace_period) {
                    $result = true;
                }

            } else {
                //颁发新的token
                //将过期的token存到缓存中
                $claims = $jwt->getClaims();
                $data   = [];
                foreach ($claims as $key => $value) {
                    $name = $value->getName();
                    if (!in_array($name, $claim_protect)) {
                        $data[$name] = $value->getValue();
                    }
                }

                $token = $this->getToken($this->uid, $data);
                cache('token_blacklist_' . $jti, $time, $refresh_time - $time + 1);
                header('Authorization:Bearer ' . $token);
                $result = true;
            }
        }

        return $result;
    }
}
