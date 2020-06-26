<?php

namespace app\common\model;

/**
 * 手机验证码表
 */
class Captcha extends Base
{
    protected $table = 'captcha';
    protected $pk = 'id';
    protected $autoWriteTimestamp = true;
    protected $createTime = 'created_at';

    protected $updateTime = false;

    /**
     * 通过条件获取手机验证码信息列表
     */
    // public function captchaSelect(array $where)
    // {
    //     if ($where) {
    //         $data = $this->where($where)->order('id desc')->select();
    //         if ($data) {
    //             $data = $data->toArray();
    //         }
    //         return $data;
    //     }else{
    //         return null;
    //     }
    // }

    /**
     * 通过条件获取单个手机验证码信息
     */
    public function captchaFind(array $where)
    {
        if ($where) {
            $data = $this->where($where)->find();
            if ($data) {
                $created_at = $data->getData('created_at');
                $data = $data->toArray();
                $data['created_at'] = $created_at;
            }
            return $data;
        }else{
            return null;
        }
    }
    /**
     * 添加手机验证码
     */
    public function addCaptcha(array $data)
    {
        $is = $this->save($data);
        if ($is) {
            return $this->id;
        }else{
            return null;
        }
    }
    /**
     * 编辑手机验证码
     */
    public function editCaptcha(array $where,array $data)
    {
        if ($where && $data){
            return $this->where($where)->update($data);
        }else{
            return null;
        }
    }
}
