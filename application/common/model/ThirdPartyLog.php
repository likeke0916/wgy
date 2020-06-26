<?php
namespace app\common\model;
/**
 * 平台端CMS数据表
 */
class ThirdPartyLog extends Base
{
    // 确定链接表名
    protected $table = 'third_party_log';
    protected $pk = 'id';
    /**
     * 添加用户信息
     */
    public function addLog($data)
    {
        return $this->save($data);
    }


}
