<?php

use think\migration\Migrator;
use think\migration\db\Column;

class Affiche extends Migrator
{
    /**
     * Change Method.
     *
     * Write your reversible migrations using this method.
     *
     * More information on writing migrations is available here:
     * http://docs.phinx.org/en/latest/migrations.html#the-abstractmigration-class
     *
     * The following commands can be used in this method and Phinx will
     * automatically reverse them when rolling back:
     *
     *    createTable
     *    renameTable
     *    addColumn
     *    renameColumn
     *    addIndex
     *    addForeignKey
     *
     * Remember to call "create()" or "update()" and NOT "save()" when working
     * with the Table class.
     */
    public function change()
    {
        $table = $this->table('wgy_affiche',array(['comment' => '社区公告', 'engine' => 'InnoDB', 'encoding' => 'utf8mb4', 'collation' => 'utf8mb4_unicode_ci']));
        $table->addColumn('admin_user_id', 'integer',array('limit' => 11,'comment'=>'后台 id'))
            ->addColumn('title', 'string',array('limit' => 50,'comment'=>'标题'))
            ->addColumn('read_number', 'integer',array('limit' => 10,'default'=>0,'comment'=>'阅读量'))
            ->addColumn('content', 'text',array('comment'=>'内容'))
            ->addColumn('create_time', 'integer', ['limit' => 10, 'default' => 0, 'comment' => '创建时间'])
            ->addColumn('update_time', 'integer', ['limit' => 10, 'default' => 0, 'comment' => '更新时间'])
            ->addColumn('delete_time', 'integer', ['limit' => 10, 'default' => 0, 'comment' => '删除时间'])
            ->addIndex(['admin_user_id'], ['name' => 'index_admin_user_id'])
            ->create();

    }

    public function down()
    {
        $this->dropTable('wgy_affiche');
    }
}
