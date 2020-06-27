<?php
namespace app\common\model;
use app\common\model\ThirdPartyLog as TPLModel;
use app\common\model\UserInfo as UIModel;
use think\Db;
/**
 * 平台端CMS数据表
 */
class ThirdParty extends Base
{
    // 确定链接表名
    protected $table = 'third_party';
    protected $pk = 'id';
    protected $autoWriteTimestamp = true;
    /**
     * 添加用户信息
     */
    public function addUser($data)
    {
        //$rs = $this->allowField(true)->save($data);
        $rs = $this::create($data);
        $this->saveLog($rs->id,1);
        return $rs;
    }
    /**
     * 编辑用户信息
     */
    public function editUser($where, $data)
    {
        if ($where && $data){
            return $this->where($where)->update($data);
        }else{
            return false;
        }
    }
    public function bindUser($id,$user,$operate_type)
    {//绑定user_id;
        $user_id = $user['id'];
        $arr_update['user_id'] = $user_id;
        $wh['id'] = $id;
        $rs = $this->editUser($wh,$arr_update);
        $ui_model = new UIModel;
        $userinfo_data = $ui_model->get($user_id);
        if(!$userinfo_data)
        {//往user_info表插入记录
            $arr_userinfo['user_id'] = $user['id'];
            $arr_userinfo['user_created_at'] = $user['created_at'];
            $arr_userinfo['created_at'] = time();
            $ui_model->addUser($arr_userinfo);
        }
        //存log记录
        $this->saveLog($id,$operate_type);
        return $rs;
    }
    public function saveLog($id,$operate_type)
    {
        $row = $this->get($id);
        $arr = array();
        $row = $row->toArray();
        foreach($row as $k=>$v)
        {
            if($k=='login_num' || $k=='login_at')
            {
                continue;
            }
            $arr['tb_'.$k]=$v;
        }
        $arr['operate_type']=$operate_type;
        $arr['created_at'] = time();
        $tpl_model = new TPLModel;
        $rs = $tpl_model->addLog($arr);
        return $rs;
    }
    public function updateLoginInfo($id,$user_id)
    {
        $row = $this->get($id);
        $wh['id'] = $id;
        $arr_update['login_num'] = $row['login_num']+1;
        $arr_update['login_at'] = time();
        $re = $this->editUser($wh, $arr_update);
        $ui_model = new UIModel;
        $row_user = $ui_model->get($user_id);
        $wh_user['user_id']=$user_id;
        $arr_user['login_at']=time();
        $arr_user['login_num']=$row_user['login_num']+1;
        $re_user = $ui_model->editUser($wh_user,$arr_user);
        return $re;
    }

}
