<?php

use yii\db\Schema;
use yii\db\Migration;

class m150904_120456_add_files_fields extends Migration
{
    const FILES_TABLE = '{{%files}}';

    public function up()
    {
        $this->addColumn(static::FILES_TABLE, 'file_original_name', $this->string());
        $this->addColumn(static::FILES_TABLE, 'file_name', $this->string());
        $this->addColumn(static::FILES_TABLE, 'file_size', $this->string());
    }

    public function down()
    {
        $this->dropColumn(static::FILES_TABLE, 'file_size');
        $this->dropColumn(static::FILES_TABLE, 'file_name');
        $this->dropColumn(static::FILES_TABLE, 'file_original_name');
    }
}
