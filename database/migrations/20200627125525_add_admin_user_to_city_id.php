<?php

use think\migration\Migrator;
use think\migration\db\Column;

class AddAdminUserToCityId extends Migrator
{

    public function change()
    {
        $table = $this->table('admin_user');
        $table->addColumn('area_id', 'integer', array('limit' => 10, 'default' => 0, 'comment' => '区、县id'))
            ->update();
    }


}
