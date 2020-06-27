<?php
/**
 * 用户验证器
 */

namespace app\common\validate;

class UserValidate extends Validate
{
    protected $rule = [
        'code' => 'require',
        'encryptedData' => 'require',
        'iv|代码' => 'require',
        'phone' => 'require',
        'verification_code' => 'require',

    ];

    protected $message = [
        'code.require' => 'Code不能为空',
        'encryptedData.require' => 'encryptedData不能为空',
        'iv.require' => 'iv不能为空',
        'phone.require' => '手机号不能为空',
        'verification_code.require' => '验证码不能为空'
    ];

    protected $scene = [
//        'api_login' => ['code', 'encryptedData','iv'],
        'api_login' => ['code'],
        'bind_phone' => ['phone','verification_code'],
    ];


}
