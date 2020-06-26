<?php
/**
 * 社区公告模型
*/

namespace app\common\model;


class Affiche extends Model
{

    public $softDelete = false;
    protected $name = 'wgy_affiche';
    protected $autoWriteTimestamp = true;

    //可搜索字段
    protected $searchField = ['title',];

    //可作为条件的字段
    protected $whereField = [];

    //可做为时间
    protected $timeField = [];

    

    

    
}
