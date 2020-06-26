<?php
namespace app\common\model\traits;

trait AddEditData
{
    public function add($data,$validate = '')
    {
        if(!$validate->scene('add')->check($data)){
            return ['code'=>3001,'msg'=>$validate->getError(),'data'=>[]];
        }else{
            try{
                $this->allowField(true)->save($data);
                return ['code'=>2000,'msg'=>'添加成功','data'=>$this];
            }catch(Exception $e){
                return ['code'=>2002,'msg'=>'添加失败','data'=>[]];
            }
        }
    }

    public function edit($data,$validate='')
    {
        if(!$validate->scene('edit')->check($data)){
            return ['code'=>3001,'msg'=>$validate->getError(),'data'=>[]];
        }else{
            try{
                $this->allowField(true)->save($data,['id'=>$data['id']]);
                return ['code'=>2000,'msg'=>'修改成功','data'=>$this];
            }catch(Exception $e){
                return ['code'=>2001,'msg'=>'修改失败','data'=>[]];
            }
        }
    }
}
