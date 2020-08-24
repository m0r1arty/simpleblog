<?php

use yii\db\Migration;

/**
 * Class m200821_130055_users
 */
class m200821_130055_users extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        if ($this->db->driverName === 'sqlite') {
            $tableOptions = null;

            $this->createTable( '{{%users}}',[
                'id' => $this->integer()->unsigned(),
                'login' => $this->string( 20 )->notNull()->defaultValue( "" ),
                'password' => $this->string( 32 )->notNull()->defaultValue( "" ),
                'token' => $this->string( 64 )->notNull()->defaultValue( "" ),
                'PRIMARY KEY ([[id]])',
            ], $tableOptions );

            $this->createIndex( 'idx-users-login', '{{%users}}', 'login' );
            $this->createIndex( 'idx-users-token', '{{%users}}', 'token' );
        }
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable( '{{%users}}' );
    }
}
