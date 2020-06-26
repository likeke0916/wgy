<?php
/**
 * 社区列表验证器
 */

namespace app\common\validate;

class CommunityValidate extends Validate
{
    protected $rule = [
            'name|社区名称' => 'require',

    ];

    protected $message = [
            'name.require' => '社区名称不能为空',

    ];

    protected $scene = [
        'add'  => ['name',],
'edit' => ['name',],

    ];

    

}
