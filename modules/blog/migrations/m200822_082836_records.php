<?php

use yii\db\Migration;

/**
 * Class m200822_082836_records
 */
class m200822_082836_records extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        if ($this->db->driverName === 'sqlite') {
            $tableOptions = null;

            $this->createTable( '{{%records}}',[
                'record_id' => $this->bigPrimaryKey(),
                'user_id' => $this->bigInteger()->defaultValue( 0 ),
                'title' => $this->string( 255 )->notNull()->defaultValue( "" ),
                'preview' => $this->text()->notNull()->defaultValue( "" ),
                'content' => $this->text()->notNull()->defaultValue( "" ),
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
        $this->dropTable( '{{%records}}' );
    }
}
