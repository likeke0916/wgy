<?php

namespace app\api\controller;

use think\Request;
use think\Db;

class AreaController extends Controller
{

    public function area(Request $request)
    {

        switch ($request->level)
        {
            case 1:
                $data = Db::name('wgy_area')->where('lever',$request->lever)->select();
                break;
            case 2:

               break;
            default:

        }
    }

}
