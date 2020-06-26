<?php
namespace app\common\model;
/**
 * 平台端CMS数据表
 */
class UserInfo extends Base
{
    // 确定链接表名
    protected $table = 'user_info';
    protected $pk = 'user_id';
    /**
     * 添加用户信息
     */
    public function addUser($data)
    {
        return $this->save($data);
    }
    public function editUser($where, $data)
    {
        if ($where && $data){
            return $this->where($where)->update($data);
        }else{
            return false;
        }
    }
    public function getUserInfoById($user_id,$hugong_exist=true)
    {
        if($user_id>0)
        {
            $userinfo_exist = $this->get($user_id);
            if($hugong_exist && !$userinfo_exist)
            {
                //需要查当前系统的用户信息，如果不存在，则返回false
                return false;
            }

            $url = config('app.passport_domain').'/index/userapi/platgetuserbyid?id='.$user_id;

            $option_plat['header'][]=passheader();
            $exist_user = simpleRequest($url,array(),$option_plat);
            if(!$exist_user)
            {
                return false;
            }
            $exist_user = json_decode($exist_user,true);
            if($exist_user['code']=='2000')
            {
                $exist_user['data']['is_exist']=$userinfo_exist?true:false;
                return $exist_user['data'];
            }

        }
        else{
            return false;
        }
    }


}
