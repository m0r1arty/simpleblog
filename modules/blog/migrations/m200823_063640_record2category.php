<?php

use yii\db\Migration;

/**
 * Class m200823_063640_record2category
 */
class m200823_063640_record2category extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        if ($this->db->driverName === 'sqlite') {
            $tableOptions = null;

            $this->createTable( '{{%record2category}}',[
                'id' => $this->bigPrimaryKey(),
                'record_id' => $this->bigInteger()->unsigned()->defaultValue(0),
                'category_id' => $this->bigInteger()->unsigned()->defaultValue(0),
            ], $tableOptions );
        }
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable( '{{%record2category}}' );
    }
}
