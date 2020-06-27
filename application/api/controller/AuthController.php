<?php


namespace app\api\controller;

use app\common\model\ThirdParty;
use app\common\model\User;
use app\common\validate\UserValidate;
use app\decode\wxBizDataCrypt;
use Exception;
use think\Request;
use think\response\Json;

class AuthController extends Controller
{

    protected $authExcept = [
        'login',
    ];

    /**d
     * 登录并发放token
     * @param Request $request
     * @param User $model
     * @param UserValidate $validate
     * @return Json|void
     */
    public function login(Request $request, UserValidate $validate, ThirdParty $thirdParty)
    {
        $param = $request->param();
        //数据验证
        $validate_result = $validate->scene('api_login')->check($param);
        if (!$validate_result) {
            return error($validate->getError(),'',3001);
        }
        $appId = config('app.xcx_user_appid');
        $secret = config('app.xcx_user_secret');
        $url = "https://api.weixin.qq.com/sns/jscode2session?appid=" . $appId . "&secret=" . $secret . "&js_code=" . $param['code'] . "&grant_type=authorization_code";
        $file_contents = simpleRequest($url);
        $result = json_decode($file_contents, true);
        if (isset($result['errcode'])) {
            return error('登录失败','',2002);
        }

        if (!array_key_exists('unionid', $result) || !$result['unionid']) {
            //解密获取unionId
            $pc = new wxBizDataCrypt($appId, $result['session_key']);
            $errCode = $pc->decryptData($param['encryptedData'], $param['iv'], $decode_result);
            if ($errCode == 0) { //0是解密成功
                $decode_result = json_decode($decode_result, true);
                $param['union_id'] = $decode_result['unionId'];
            } else {
                return error('系统繁忙，请重试','',2002);
            }
        }
//        $param['union_id']  = '1231';
        $param['app_id'] = $appId;
        $param['open_id'] = $result['openid'];

        if (!$thirdPartyData = $thirdParty->where(['open_id' => $param['open_id'], 'third_party' => config('app.third_party_xcx_user')])->find()) {
            //如果没有就插入数据
            $thirdPartyData = $thirdParty->addUser($param);
            $thirdPartyData['user_id'] = 0;
        }
        $url = config('app.passport_domain') . '/index/userapi/platgetuserbyunionid?unionid=' . $param['union_id'];
        $option_plat['header'][] = passheader();
        $exist_user = simpleRequest($url, [], $option_plat);
        $exist_user = json_decode($exist_user, true);
        if ($exist_user['code'] == '2000') {
            //根据union_id绑定user_id
            if (!$thirdPartyData['user_id']) {
                $thirdParty->bindUser($thirdPartyData['id'], $exist_user['data'], 2);
            }
            if ($thirdPartyData['tel'] = $exist_user['data']['tel']) {
                //更新登录信息
                $thirdPartyData->updateLoginInfo($thirdPartyData['id'], $exist_user['data']['id']);
            }
        }
        $token = $this->getToken($thirdPartyData['user_id']);

        //返回数据
        return success('登录成功', ['token' => $token, 'bound_phone' => empty($thirdPartyData['tel']) ? false : true ,'id'=> $thirdPartyData['id']],2000);
    }



}