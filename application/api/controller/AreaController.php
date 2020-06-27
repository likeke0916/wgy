<?php

namespace app\api\controller;

use think\Request;
use think\Db;
use app\admin\model\AdminUser;

class AreaController extends Controller
{

    public function area(Request $request)
    {
        $level = $request['level'];
        switch ($level)
        {
            case $level >= 3: //省市区
                $data = Db::name('wgy_area')->where('lever',$level)->select();
                break;
            case 4: //街道

               break;
            default:
                $data = 123;
                break;

        }
        return success('成功',$data,2000);
    }

}
