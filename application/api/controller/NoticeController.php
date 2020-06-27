<?php

namespace app\api\controller;

use app\common\model\Affiche;
use app\common\model\User;
use app\common\model\UserInfo;
use app\common\validate\UserValidate;
use think\Request;

class NoticeController extends Controller
{

    public function index()
    {
        //return $this->uid;
        $user = UserInfo::get($this->uid);
        return $user;
        //$user = User::user($this->uid)->find();
        //return $user;
    }

}
