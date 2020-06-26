<?php
/**
 * Api基础控制器
 */

namespace app\api\controller;

use think\facade\Cache;
use think\Request;
use app\api\traits\ApiAuth;
use app\common\controller\auth\Token;
class Controller
{
    use ApiAuth;
//    use Token;

    //无需验证登录的方法，禁止在此处修改,请在具体业务Controller中修改
    protected $authExcept = [];

    //当前访问的用户
    protected $uid = 0;

    //当前页码
    protected $page;

    //每页数据量
    protected $limit;

    /**
     * @var Request
     */
    protected $request;


    //当前请求的参数，get/post都在其中
    protected $param;

    //当前请求数据的ID
    protected $id;

    public function __construct(Request $request)
    {
//        $token = $request->header('Authorization');
//        //$request->token_uid = '0';
//        if($token && Cache::store('redis')->get('hguser:'.$token)){
//            $res = $this->decodeToken($token,'user');
//            if($res['code']=='4004' && $res['data']->aud=='user') {
//               // $request->token_uid=$res['data']->uid;
//                $this->uid = $res['data']->uid;
//            }
//        }
//        //$request->token_uid=366;
//        if(!$this->uid)
//        {
//            return json(['code'=>4003,'msg'=>'请重新登录~','data'=>[]]);
//        }

        $this->request = $request;

       //jwt验证
        $this->jwtInit();
        $this->checkToken();



        //初始化基本数据
        $this->param = $request->param();
        $this->page  = $this->param['page'] ?? 1;
        $this->limit = $this->param['limit'] ?? 10;
        $this->id    = $this->param['id'] ?? 0;

        //limit防止过大处理
        $this->limit = $this->limit <= 100 ? $this->limit : 100;
    }

}
