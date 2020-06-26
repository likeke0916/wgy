<?php

use think\migration\Migrator;
use think\migration\db\Column;

class Option extends Migrator
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
        $table = $this->table('wgy_option',array(['comment' => '问题选项', 'engine' => 'InnoDB', 'encoding' => 'utf8mb4', 'collation' => 'utf8mb4_unicode_ci']));
        $table->addColumn('issue_id', 'integer',array('limit' => 11,'comment'=>'问题id'))
            ->addColumn('option', 'string',array('limit' => 100, 'default'=> '','comment'=>'选项'))
            ->addColumn('create_time', 'integer', ['limit' => 10, 'default' => 0, 'comment' => '创建时间'])
            ->addColumn('update_time', 'integer', ['limit' => 10, 'default' => 0, 'comment' => '更新时间'])
            ->addColumn('delete_time', 'integer', ['limit' => 10, 'default' => 0, 'comment' => '删除时间'])
            ->addIndex(['issue_id'], ['name' => 'index_issue_id'])
            ->create();
    }

    public function down()
    {
        $this->dropTable('wgy_option');
    }
}
