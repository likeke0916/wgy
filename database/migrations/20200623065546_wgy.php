<?php

use think\migration\Migrator;
use think\migration\db\Column;

class Wgy extends Migrator
{

    public function change()
    {
        $table = $this->table('wgy_wgy',array(['comment' => '社区', 'engine' => 'InnoDB', 'encoding' => 'utf8mb4', 'collation' => 'utf8mb4_unicode_ci']));
        $table->addColumn('community_id', 'integer',array('limit' => 11,'comment'=>'社区id'))
            ->addColumn('avatar', 'string',array('limit' => 100, 'default'=> '','comment'=>'头像'))
            ->addColumn('nickname', 'string',array('limit' => 100,'default'=> '','comment'=>'昵称'))
            ->addColumn('openid', 'string',array('limit' => 100,'default'=> '','comment'=>'openid'))
            ->addColumn('unionid', 'string',array('limit' => 100,'default'=> '','comment'=>'unionid'))
            ->addColumn('name', 'string',array('limit' => 50,'comment'=>'姓名'))
            ->addColumn('phone', 'string',array('limit' => 50,'comment'=>'电话'))
            ->addColumn('qr_code', 'string',array('limit' => 100,'default'=> '','comment'=>'二维码'))
            ->addColumn('type', 'integer',array('limit' => 1,'default'=> 0,'comment'=>'0:网格员1：网格长'))
            ->addColumn('create_time', 'integer', ['limit' => 10, 'default' => 0, 'comment' => '创建时间'])
            ->addColumn('update_time', 'integer', ['limit' => 10, 'default' => 0, 'comment' => '更新时间'])
            ->addColumn('delete_time', 'integer', ['limit' => 10, 'default' => 0, 'comment' => '删除时间'])
            ->addIndex(['community_id'], ['name' => 'index_community_id'])
            ->create();
    }

    public function down()
    {
        $this->dropTable('wgy_wgy');
    }
}
