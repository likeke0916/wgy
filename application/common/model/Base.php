<?php

namespace app\common\model;

use think\Model;
use app\common\model\traits\AddEditData;
use app\common\model\traits\DeleteData;
class Base extends Model
{
	use AddEditData, DeleteData;
}