<?php

use yii\db\Migration;

/**
 * Class m200822_082824_categories
 */
class m200822_082824_categories extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        if ($this->db->driverName === 'sqlite') {
            $tableOptions = null;

            $this->createTable( '{{%categories}}',[
                'category_id' => $this->bigPrimaryKey(),
                'title' => $this->string(255 )->notNull()->defaultValue( "" ),
                'slug' => $this->string( 60 )->notNull()->defaultValue( "" ),
                'created_at' => $this->bigInteger()->unsigned()->defaultValue( 0 ),
                'updated_at' => $this->bigInteger()->unsigned()->defaultValue( 0 ),
            ], $tableOptions );
        }
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable( '{{%categories}}' );
    }
}
