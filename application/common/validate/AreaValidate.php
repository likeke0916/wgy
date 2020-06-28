<?php
/**
 * 用户验证器
 */

namespace app\common\validate;

class AreaValidate extends Validate
{
    protected $rule = [
        'level'        => 'require',
        'id'           => 'require',
        'community_id' => 'require',
    ];

    protected $message = [
        'level.require'        => '等级不能为空',
        'id.require'           => 'id不能为空',
        'community_id.require' => '社区idid不能为空',
    ];

    protected $scene = [
        'api_area'  => ['level', 'id'],
        'save_area' => ['community_id'],
    ];


}
