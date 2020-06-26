<?php

use think\migration\Migrator;
use think\migration\db\Column;

class Community extends Migrator
{

    public function change()
    {
        $table = $this->table('wgy_community',array(['comment' => '社区', 'engine' => 'InnoDB', 'encoding' => 'utf8mb4', 'collation' => 'utf8mb4_unicode_ci']));
        $table->addColumn('admin_user_id', 'integer',array('limit' => 11,'comment'=>'后台id'))
            ->addColumn('name', 'string',array('limit' => 50,'comment'=>'社区名称'))
            ->addColumn('create_time', 'integer', ['limit' => 10, 'default' => 0, 'comment' => '创建时间'])
            ->addColumn('update_time', 'integer', ['limit' => 10, 'default' => 0, 'comment' => '更新时间'])
            ->addColumn('delete_time', 'integer', ['limit' => 10, 'default' => 0, 'comment' => '删除时间'])
            ->addIndex(['admin_user_id'], ['name' => 'index_admin_user_id'])
            ->create();
    }

    public function down()
    {
        $this->dropTable('wgy_community');
    }
}
