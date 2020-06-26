<?php
/**
 * 社区列表模型
*/

namespace app\common\model;


class Community extends Model
{

    public $softDelete = false;
    protected $name = 'wgy_community';
    protected $autoWriteTimestamp = true;

    //可搜索字段
    protected $searchField = ['name',];

    //可作为条件的字段
    protected $whereField = [];

    //可做为时间
    protected $timeField = [];


    //网格与列表
    public function wgys()
    {
        return $this->hasMany(Wgy::class);
    }

}
