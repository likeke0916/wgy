<?php
/**
 * 社区风采模型
*/

namespace app\common\model;

use think\model\concern\SoftDelete;

class Mien extends Model
{
    use SoftDelete;
    public $softDelete = true;
//    protected $
    protected $name = 'wgy_mien';
    protected $autoWriteTimestamp = false;

    //可搜索字段
    protected $searchField = ['title',];

    //可作为条件的字段
    protected $whereField = [];

    //可做为时间
    protected $timeField = [];

    

    

    
}
