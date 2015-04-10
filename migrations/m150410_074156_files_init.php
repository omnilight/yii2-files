<?php

use yii\db\Schema;
use yii\db\Migration;

class m150410_074156_files_init extends Migration
{
    const FILES_TABLE = '{{%files}}';

    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'ENGINE=InnoDB CHARSET=utf8';
        }

        $this->createTable(static::FILES_TABLE, [
            'id' => Schema::TYPE_PK,
            'original_name' => Schema::TYPE_STRING,
            'name' => Schema::TYPE_STRING,
            'file_size' => Schema::TYPE_INTEGER,
            'created_at' => Schema::TYPE_DATETIME,
            'updated_at' => Schema::TYPE_DATETIME,
        ], $tableOptions);
    }

    public function down()
    {
        $this->dropTable(static::FILES_TABLE);
    }
}
