<?php

namespace app\api\controller;

use app\common\model\Community;
use app\common\validate\AreaValidate;
use think\Request;
use think\Db;
use app\admin\model\AdminUser;

class AreaController extends Controller
{

    public function area(Request $request, AreaValidate $validate)
    {
       $param = $request->param();
        //数据验证
        $validate_result = $validate->scene('api_login')->check($param);
        if (!$validate_result) {
            return error($validate->getError(),'',3001);
        }
        switch ($param['level'])
        {
            case 1: //省
                $admin_user = AdminUser::where('province_id','>', 0)->group('province_id')->column('province_id');
                $data = Db::table('wgy_area')->field('id,area_name')->whereIn('id',$admin_user)->select();
                break;
            case 2: //市
                $admin_user = AdminUser::where('province_id',$param['id'])->group('city_id')->column('city_id');
                $data = Db::table('wgy_area')->field('id,area_name')->whereIn('id',$admin_user)->select();
               break;
            case 3: //区
                $admin_user = AdminUser::where('city_id',$param['id'])->group('area_id')->column('area_id');
                $data = Db::table('wgy_area')->field('id,area_name')->whereIn('id',$admin_user)->select();
                break;
            case 4: //街道
                $data = AdminUser::field('id,nickname')->where('area_id',$param['id'])->select();
                break;
            case 5: //社区
                $data = Community::field('id,name')->where('admin_user_id',$param['id'])->select();
                break;
            default:
                return error('level类型错误');
                break;

        }
        return success('成功',$data,2000);
    }


    function areaInfo($areaid,&$rs){
        //获取地区信息
        $areaOne = Db::table('wgy_area')->where('id',$areaid)->find();
        //继续查询上一级的数据 市数据
        if($areaOne['parent_id']!=-1){
            $rs[] = $areaOne;
            $this->areaInfo($areaOne['parent_id'],$rs);
        }else{
            $rs[]= $areaOne;
        }
        return $rs;
    }


}
