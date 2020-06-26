<?php
/**
 * 报事分类模型
*/

namespace app\common\model;

use think\model\concern\SoftDelete;

class CategoryThing extends Model
{
    use SoftDelete;
    public $softDelete = true;
//    protected $
    protected $name = 'wgy_category_thing';
    protected $autoWriteTimestamp = false;

    //可搜索字段
    protected $searchField = [];

    //可作为条件的字段
    protected $whereField = [];

    //可做为时间
    protected $timeField = [];

    

    

    
}
