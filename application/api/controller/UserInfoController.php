<?php

namespace app\api\controller;

use app\common\model\ThirdParty as ThirdPartyModel;
use app\common\model\Captcha;
use think\cache\driver\Redis;
use think\Request;
use Naixiaoxin\ThinkWechat\Facade;
use think\Db;
use app\common\controller\auth\Token;
use think\facade\Cache;
use think\facade\Config;
use app\decode\wxBizDataCrypt;
//use think\Controller;

/**
 * 操作前台用户
 */
class UserInfoController extends Base
{
    use Token;

    /**
     * 10000 首页用户登录
     * @param MUs $MUs [description]
     * @return [type]      [description]
     */
    public function login()
    {

        $data['nickPhoto']     = $this->request->param('nickPthoto/s', '');
        $data['nickName']      = $this->request->param('nickName/s', '');
        $data['gender']        = $this->request->param('gender/s', '');
        $data['code']          = $this->request->param('code/s', '');
        $data['encryptedData'] = $this->request->param('encryptedData/s', '');
        $data['iv']            = $this->request->param('iv/s', '');

        if (empty($data['code']) || empty($data['encryptedData']) || empty($data['iv'])) {
            return $this->showReturnCode(3001, '参数错误');
        }
        $code          = $data["code"];
        $appid         = config('app.xcx_user_appid');
        $secret        = config('app.xcx_user_secret');
        $url           = "https://api.weixin.qq.com/sns/jscode2session?appid=" . $appid . "&secret=" . $secret . "&js_code=" . $code . "&grant_type=authorization_code";
        $file_contents = simpleRequest($url);
        $rsult         = json_decode($file_contents, true);
        if (isset($rsult['errcode'])) {
            return $this->showReturnCode(2002, '登录失败');
        }

        $openid = $rsult['openid'];
        if (!array_key_exists('unionid', $rsult) || !$rsult['unionid']) {
            //解密获取unionId
            $pc      = new wxBizDataCrypt($appid, $rsult['session_key']);
            $errCode = $pc->decryptData($data['encryptedData'], $data['iv'], $decode_result);
            if ($errCode == 0) {
                $decode_result   = json_decode($decode_result, true);
                $union_id        = $decode_result['unionId'];
                $arr['union_id'] = $union_id;
            } else {
                return $this->showReturnCode(2002, '系统繁忙，请重试');
            }
        } else {
            $arr['union_id'] = $rsult['unionid'];
        }
        $arr['third_party'] = config('app.third_party_xcx_user');
        $wh['third_party']  = $arr['third_party'];
        $arr['user_id']     = 0;
        $arr['app_id']      = $appid;
        $wh['open_id']      = $arr['open_id'] = $openid;
        $arr['nick_name']   = $this->request->param('nickName');
        $arr['avatar']      = $this->request->param('nickPhoto');
        $arr['gender']      = $this->request->param('gender');

        //$arr['login_num'] = 0;
        $arr['near_login']  = time();
        $arr['session_key'] = $rsult['session_key'];

        $thirdparty     = new ThirdPartyModel;
        $thirdPartyData = $thirdparty->where($wh)->find();
        $return         = [];
        //往third_partary表插入数据，获取id
        if ($thirdPartyData) {
            $return['id']      = $thirdPartyData['id'];
            $return['user_id'] = $thirdPartyData['user_id'];
            $union_id          = $thirdPartyData['union_id'];
        } else {
            $return['user_id'] = 0;
            $arr['login_num']  = 0;
            $arr['created_at'] = time();
            $re                = $thirdparty->addUser($arr);
            $return['id']      = $thirdparty->id;
        }
        //根据user_id或union_id，获取tel
        $return['tel']           = '';
        $return['user_info']     = null;
        $return['Authorization'] = null;

        //返回信息，2000，根据结果集处理，tel为空，需要绑定手机号，绑定手机号的接口请上传id和手机号；
        //如果tel不为空，则登录成功，用返回的user_id直接作为登录信息

        if ($return['user_id'] > 0) {
            $url = config('app.passport_domain') . '/index/userapi/platgetuserbyid?id=' . $return['user_id'];
            unset($option_plat);
            $option_plat['header'][] = passheader();
            $exist_user              = simpleRequest($url, [], $option_plat);
            if (!$exist_user) {
                return $this->showReturnCode(2002, '登录失败，您的账号异常，请联系客服人员');
            }
            $exist_user = json_decode($exist_user, true);
            if ($exist_user['code'] == '2000') {

                $return['tel']       = $exist_user['data']['tel'];
                $return['user_info'] = $exist_user['data'];
            }

        } elseif ($return['user_id'] == 0 && $union_id) {//如果还没有user_id，且有unionid，去用户系统查询user_id是否存在
            $url = config('app.passport_domain') . '/index/userapi/platgetuserbyunionid?unionid=' . $union_id;
            unset($option_plat);
            $option_plat['header'][] = passheader();
            $exist_user              = simpleRequest($url, [], $option_plat);
            if ($exist_user) {
                $exist_user = json_decode($exist_user, true);
                if ($exist_user['code'] == '2000') {
                    //根据union_id绑定user_id
                    $thirdparty->bindUser($return['id'], $exist_user['data'], 2);
                    $return['user_id']   = $exist_user['data']['id'];
                    $return['tel']       = $exist_user['data']['tel'];
                    $return['user_info'] = $exist_user['data'];
                }
            }
        }
        if ($return['tel']) {
            //更新登录信息
            $re = $thirdparty->updateLoginInfo($return['id'], $return['user_id']);

        }
        if (is_array($return['user_info'])) {
            $return['user_info']     = $this->formatShowUserInfo($return['user_info']);
            $token                   = $this->getToken($return['user_id'], 'user');
            $data_user['aud']        = 'user';
            $data_user['uid']        = $return['user_id'];
            $res                     = Cache::store('redis')->set('zuoyouweilai:' . $token, $data_user, Config::get('app.user_token_exp'));
            $return['Authorization'] = $token;
        }

        return $this->showReturnCode(2000, '授权成功', $return);
    }


    /**
     * 21300 给用户发验证短信
     * @return [type] [description]
     */
    public function verificationCode()
    {
        if ($this->request->isPost()) {
            $param[] = 'tel';
            $data    = checkParam($param, $this->request->param());
            if (empty($data)) {
                return $this->showReturnCode(3001, '参数错误', '');
            }
            $code = createSixCode();
            // 保存 $code
            $Captcha        = new Captcha;
            $cWhere['tel']  = $data['tel'];
            $cData          = $Captcha->captchaFind($cWhere);
            $caData['code'] = $code;
            if ($cData) {
                // 更新验证码数据
                $caData['created_at'] = time();
                $SU                   = $Captcha->editCaptcha($cWhere, $caData);
            } else {
                // 没有 添加
                $caData['tel'] = $data['tel'];
                $SU            = $Captcha->addCaptcha($caData);
            }
            $msg = "【闪护】您的手机验证码是：$code";
            $res = $this->sendSMS($data['tel'], $msg);
            $res = json_decode($res, true);
            if ($SU && $res['code'] == 0) {
                // 发送短信
                return $this->showReturnCode(2000, '发送成功', '');
            } else {
                return $this->showReturnCode(2002, '发送失败', $res);
            }
        } else {
            return $this->showReturnCode(4001, '请求方式错误', '');
        }
    }


    /**
     * 发送短信
     *
     * @param string $mobile 手机号码
     * @param string $msg 短信内容
     * @param string $needstatus 是否需要状态报告
     */
    private function sendSMS($mobile, $msg, $needstatus = 'true')
    {
        $postArr            = [
            'account'  => 'N5563155',
            'password' => '4a3yA7EZo',
            'msg'      => urlencode($msg),
            'phone'    => $mobile,
            'report'   => $needstatus,
        ];
        $option['jsondata'] = 1;
        $option['header'][] = 'Content-Type: application/json; charset=utf-8';
        $result             = simpleRequest('http://smssh1.253.com/msg/send/json', $postArr, $option);
        return $result;
    }

    private function formatShowUserInfo($user)
    {
        $fields = ['id', 'gender', 'nick_name', 'nick_photo', 'tel'];
        $rs     = [];
        foreach ($user as $k => $v) {
            if (in_array($k, $fields)) {
                $rs[$k] = $v;
            }
        }
        return $rs;
    }


    /**
     * 用户绑定手机
     * 3001,需要重新刷新页面，走login流程
     * 10100，需要重新发送验证码
     * 2000登录成功
     */
    public function bindPhone()
    {
        if ($this->request->isPost()) {
            $param[] = 'tel';
            $param[] = 'id';
            $param[] = 'code';
            //$param[] = 'source';
            $data = checkParam($param, $this->request->param());
            if (empty($data)) {
                //需要重新刷新页面，走login流程
                return $this->showReturnCode(3001, '参数错误', '');
            }
            // 校验code
            $Captcha        = new Captcha;
            $caWhere['tel'] = $data['tel'];
            $caData         = $Captcha->captchaFind($caWhere);
            $outTime        = $caData['created_at'] + 300;
            $ThirdParty     = new ThirdPartyModel;
            $thirdpartydata = $ThirdParty->get($data['id']);
            if (!$thirdpartydata) {
                return $this->showReturnCode(3001, '参数错误', '');
            }
            if ($caData['code'] != $data['code'] || time() >= $outTime) {//需要重新发送验证码，输入的验证码错误或超时
                return $this->showReturnCode(3001, '验证码超时或错误', '');
            }
            $return['id']            = $data['id'];
            $return['user_id']       = '';
            $return['tel']           = '';
            $return['user_info']     = null;
            $return['Authorization'] = null;
            if ($thirdpartydata->user_id > 0) {//已经绑定过user_id

                $return['user_id'] = $thirdpartydata->user_id;

                $url = config('app.passport_domain') . '/index/userapi/platgetuserbyid?id=' . $thirdpartydata->user_id;
                unset($option_plat);
                $option_plat['header'][] = passheader();
                $exist_user              = simpleRequest($url, [], $option_plat);
                if (!$exist_user) {
                    return $this->showReturnCode(2002, '系统异常，请重试', '');
                }
                $exist_user = json_decode($exist_user, true);
                if ($exist_user['code'] == '2000') {//用户存在
                    if ($exist_user['data']['tel']) {
                        //之前已经绑定过手机号，本次不需要再绑定
                        $return['tel'] = $exist_user['data']['tel'];
                        if ($return['tel'] == $data['tel']) {
                            $return['user_info']     = $this->formatShowUserInfo($exist_user['data']);
                            $token                   = $this->getToken($return['user_id'], 'user');
                            $data_user['aud']        = 'user';
                            $data_user['uid']        = $return['user_id'];
                            $res                     = Cache::store('redis')->set('hguser:' . $token, $data_user, Config::get('app.user_token_exp'));
                            $return['Authorization'] = $token;
                            return $this->showReturnCode(2000, '登录成功', $return);
                        } else {
                            return $this->showReturnCode(2002, '您之前已经绑定过手机号，请重新登录', $return);
                        }
                    } else {
                        //绑定过user_id,但是user表手机号为空，绑定本次录入的手机号
                        $url = config('app.passport_domain') . '/index/userapi/platbindtel?id=' . $thirdpartydata->user_id . '&tel=' . $data['tel'];
                        unset($option_plat);
                        $option_plat['header'][] = passheader();
                        $bind_tel                = simpleRequest($url, [], $option_plat);
                        if (!$bind_tel) {
                            return $this->showReturnCode(2002, '系统异常，请重试', '');
                        }
                        $bind_tel = json_decode($bind_tel, true);
                        if ($bind_tel['code'] == '2000') {
                            //绑定成功
                            $return['tel']           = $data['tel'];
                            $return['user_info']     = $this->formatShowUserInfo($bind_tel['data']);
                            $token                   = $this->getToken($return['user_id'], 'user');
                            $data_user['aud']        = 'user';
                            $data_user['uid']        = $return['user_id'];
                            $res                     = Cache::store('redis')->set('hguser:' . $token, $data_user, Config::get('app.user_token_exp'));
                            $return['Authorization'] = $token;
                            return $this->showReturnCode(2000, '登录成功', $return);
                        } elseif ($bind_tel['code'] == '2001') {
                            //该手机号已经被其他人使用，需要重新输入手机号
                            return $this->showReturnCode(3001, $bind_tel['msg'], $return);
                        } else {//其他情况，需要重新刷新页面

                            return $this->showReturnCode(2002, '系统异常，绑定失败', $return);
                        }
                    }
                } else {
                    //user_id字段存在，但是user表用户不存在
                    return $this->showReturnCode(2002, '账户异常', $return);
                }

            } else {
                //护工平台无user_id，需要根据本次录入的手机号绑定user_id
                $url = config('app.passport_domain') . '/index/userapi/platgetuserbytel?tel=' . $data['tel'];
                unset($option_plat);
                $option_plat['header'][] = passheader();
                $exist_user              = simpleRequest($url, [], $option_plat);

                if (!$exist_user) {
                    return $this->showReturnCode(2002, '系统异常，请重试', '');
                }
                $exist_user = json_decode($exist_user, true);
                if ($exist_user['code'] == '2000') {
                    //根据手机号绑定user_id
                    $ThirdParty->bindUser($data['id'], $exist_user['data'], 3);

                    $return['user_id']       = $exist_user['data']['id'];
                    $return['tel']           = $exist_user['data']['tel'];
                    $return['user_info']     = $this->formatShowUserInfo($exist_user['data']);
                    $token                   = $this->getToken($return['user_id'], 'user');
                    $data_user['aud']        = 'user';
                    $data_user['uid']        = $return['user_id'];
                    $res                     = Cache::store('redis')->set('zuoyouweilai:' . $token, $data_user, Config::get('app.user_token_exp'));
                    $return['Authorization'] = $token;
                    return $this->showReturnCode(2000, '登录成功', $return);
                } else {
                    //用户不存在，需要用手机号新注册用户
                    $register['tel']       = $data['tel'];
                    $register['nickPhoto'] = $thirdpartydata->avatar;
                    $register['nickName']  = $thirdpartydata->nick_name;
                    $register['gender']    = $thirdpartydata->gender;
                    $register['unionid']   = $thirdpartydata->union_id;

                    $url = config('app.passport_domain') . '/index/userapi/platregistertel';
                    unset($option_plat);
                    $option_plat['header'][] = passheader();
                    $register_tel            = simpleRequest($url, $register, $option_plat);
                    if (!$register_tel) {
                        return $this->showReturnCode(2002, '系统异常，请重试', '');
                    }
                    $register_tel = json_decode($register_tel, true);
                    if ($register_tel['code'] == '2000') {
                        //注册成功
                        //根据手机号绑定user_id
                        $ThirdParty->bindUser($data['id'], $register_tel['data'], 3);

                        $return['user_id']       = $register_tel['data']['id'];
                        $return['tel']           = $register_tel['data']['tel'];
                        $token                   = $this->getToken($return['user_id'], 'user');
                        $data_user['aud']        = 'user';
                        $data_user['uid']        = $return['user_id'];
                        $res                     = Cache::store('redis')->set('zuoyouweilai:' . $token, $data_user, Config::get('app.user_token_exp'));
                        $return['Authorization'] = $token;
                        $return['user_info']     = $this->formatShowUserInfo($register_tel['data']);

                        return $this->showReturnCode(2000, '登录成功', $return);
                    } else {//其他情况，需要重新刷新页面
                        return $this->showReturnCode(2002, '系统异常，绑定失败', $return);
                    }
                }

            }


        } else {
            return msg(4001, '请求方式错误', '');
        }
    }

}


