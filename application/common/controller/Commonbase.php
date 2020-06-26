<?php
/**
 * @author zhaobin
 * @DateTime 2019-07-15T10:19:14+0800
 */
namespace app\common\controller;

use think\Controller;
use think\Request;
use think\facade\Config;

//如果需要设置允许所有域名发起的跨域请求，可以使用通配符 *
header('Access-Control-Allow-Origin:*');   // 指定允许其他域名访问
header('Access-Control-Allow-Headers:x-requested-with,content-type,Authorization');// 响应头设置
header('Access-Control-Allow-Methods: GET,PUT,POST,DELETE,OPTIONS');//指定允许的提交方式
class Commonbase extends Controller
{
    protected $request = null;
    protected $num = 0;
    public function __construct(Request $request)
    {
        parent::__construct();//调用一下父类的构造函数
        $this->request = $request;
    }
    /**
     * 构造请求数据
     * @param array $array [<数据规则>]
     * @return array [<description>]
     */
    protected function buildParam($array)
    {
        $data = [];
        foreach($array as $item => $value){
            $data[$item] = $this->request->param($value);
        }
        return $data;
    }

    /**
     * 返回信息
     * @param string $code [<错误编码>]
     * @param array  $data [<返回数据>]
     * @param string $msg  [<提示信息>]
     * @return array [<description>]
     */
    public function showReturnCode($code = 0,$msg = '',$data = array())
    {
        $return_data = [
            'code' => '500',
            'msg' => '信息未定义',
            'data' => $data,
        ];
        if ($code == 0){
            return $return_data;
        }
        $return_data['code'] = $code;
        if (!empty($msg)){
            $return_data['msg'] = $msg;
        }
        else{
            $return_data['msg'] = return_code($code);
        }


        return json($return_data,null,['token_uid'=>isset($this->request->token_uid)?$this->request->token_uid:0]);


    }
    //空操作
    public function _empty()
    {
        $return['code'] = 3005;
        $return['msg'] = '';
        return $this->showReturnCode($return['code'],$return['msg']);
    }
}
