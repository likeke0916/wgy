<?php
namespace app\common\model\traits;
trait DeleteData
{
    public function del($data,$validate = '')
    {
        if(!$validate->scene('del')->check($data)){
            return ['code'=>3001,'msg'=>$validate->getError(),'data'=>[]];
        }else{
            try{
                $this->destroy($data['id']);
                return ['code'=>2000,'msg'=>'删除成功','data'=>[]];
            }catch(Exception $e){
                return ['code'=>2002,'msg'=>'删除失败','data'=>[]];
            }
        }
    }

    public function changeState($data,$validate = '')
    {
        if(!$validate->scene('change')->check($data)){
            return ['code'=>3001,'msg'=>$validate->getError(),'data'=>[]];
        }else{
            try{
                $this->allowField(true)->save($data,['id'=>$data['id']]);
                return ['code'=>2000,'msg'=>'删除成功','data'=>[]];
            }catch(Exception $e){
                return ['code'=>2002,'msg'=>'删除失败','data'=>[]];
            }
        }
    }

}
