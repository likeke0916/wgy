<?php

use think\migration\Migrator;
use think\migration\db\Column;

class Issue extends Migrator
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
        $table = $this->table('wgy_issue',array(['comment' => '问题', 'engine' => 'InnoDB', 'encoding' => 'utf8mb4', 'collation' => 'utf8mb4_unicode_ci']));
        $table->addColumn('survey_id', 'integer',array('limit' => 11,'comment'=>'调研id'))
            ->addColumn('title', 'string',array('limit' => 200, 'default'=> '','comment'=>'标题'))
            ->addColumn('type', 'integer', array('limit'=> 1, 'default'=>0 ,'comment'=> '0单选 1多选 2输入框'))
            ->addColumn('sort', 'integer', array('limit'=> 4, 'default'=>0 ,'comment'=> '排序'))
            ->addColumn('create_time', 'integer', ['limit' => 10, 'default' => 0, 'comment' => '创建时间'])
            ->addColumn('update_time', 'integer', ['limit' => 10, 'default' => 0, 'comment' => '更新时间'])
            ->addColumn('delete_time', 'integer', ['limit' => 10, 'default' => 0, 'comment' => '删除时间'])
            ->addIndex(['survey_id'], ['name' => 'index_survey_id'])
            ->create();
    }

    public function down()
    {
        $this->dropTable('wgy_issue');
    }
}
