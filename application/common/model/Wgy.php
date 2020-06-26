<?php
/**
 * 网格员管理模型
 */

namespace app\common\model;


class Wgy extends Model
{

    public $softDelete = false;
    protected $name = 'wgy_wgy';
    protected $autoWriteTimestamp = true;

    const TYPE_EPHOR   = 1;
    const TYPE_SOLDIER = 0;

    public static $winType = [
        self::TYPE_EPHOR   => '网格长',
        self::TYPE_SOLDIER => '网格员',
    ];

    //可搜索字段
    protected $searchField = ['name',];

    //可作为条件的字段
    protected $whereField = [];

    //可做为时间
    protected $timeField = [];


    //关联社区
    public function community()
    {
        return $this->belongsTo(Community::class);
    }


}
