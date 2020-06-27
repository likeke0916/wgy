<?php
/**
 * 社区公告验证器
 */

namespace app\admin\validate;

class AfficheValidate extends Validate
{
    protected $rule = [
        'title|标题'   => 'require',
        'content|内容' => 'require',

    ];

    protected $message = [
        'title.require'   => '标题不能为空',
        'content.require' => '内容不能为空',

    ];

    protected $scene = [
        'add'  => ['title', 'content',],
        'edit' => ['title', 'content',],

    ];


}
