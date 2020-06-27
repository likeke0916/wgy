<?php
/**
 * 网格员管理验证器
 */

namespace app\admin\validate;

class WgyValidate extends Validate
{
    protected $rule = [
        'name|姓名'   => 'require',
        'phone|手机号' => 'require',

    ];

    protected $message = [
        'name.require'  => '姓名不能为空',
        'phone.require' => '手机号不能为空',

    ];

    protected $scene = [
        'add'  => ['name','phone'],
        'edit' => ['name','phone'],

    ];


}
