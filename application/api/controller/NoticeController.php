<?php

namespace app\api\controller;

use app\common\model\Affiche;
use app\common\model\User;
use app\common\validate\UserValidate;
use think\Request;

class NoticeController extends Controller
{

    public function index()
    {
        return $this->uid;
        //$user = User::user($this->uid)->find();
        //return $user;
    }

}
