<?php

use yii\db\Migration;

/**
 * Class m200903_132107_backsef
 */
class m200903_132107_backsef extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        if ($this->db->driverName === 'sqlite') {
            $tableOptions = null;

            $this->createTable( '{{%bsef_route}}',[
                'route_id' => $this->bigPrimaryKey(),
                'crc' => $this->integer()->notNull()->defaultValue( 0 ),
                'route' => $this->string( 100 )->notNull()->defaultValue( "" ),
            ], $tableOptions );

            $this->createIndex( 'idx-crc-bsef_route', '{{%bsef_route}}', 'crc', false );

            $this->createTable( '{{%bsef_params}}',[
                'param_id' => $this->bigPrimaryKey(),
                'crc' => $this->integer()->notNull()->defaultValue( 0 ),
                'param' => $this->string( 60 )->notNull()->defaultValue( "" ),
            ], $tableOptions );

            $this->createIndex( 'idx-crc-bsef_params', '{{%bsef_params}}', 'crc', false );

            $this->createTable( '{{%bsef}}',[
                'id' => $this->bigPrimaryKey(),
                'route_id' => $this->bigInteger()->notNull()->defaultValue( 0 ),
                'sef_id' => $this->bigInteger()->notNull()->defaultValue( 0 ),
                'crc' => $this->integer()->notNull()->defaultValue( 0 ),
                'params' => $this->string( 60 )->notNull()->defaultValue( "" ),
            ], $tableOptions );

            $this->createIndex( 'idx-crc-bsef', '{{%bsef}}', 'crc', false );
            $this->createIndex( 'idx-route_id-bsef', '{{%bsef}}', 'route_id', false );
            $this->createIndex( 'idx-sef_id-bsef', '{{%bsef}}', 'sef_id', false );
        }
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropIndex( 'idx-sef_id-bsef', '{{%bsef}}' );
        $this->dropIndex( 'idx-route_id-bsef', '{{%bsef}}' );
        $this->dropIndex( 'idx-crc-bsef', '{{%bsef}}' );
        $this->dropIndex( 'idx-crc-bsef_params', '{{%bsef_params}}' );
        $this->dropIndex( 'idx-crc-bsef_route', '{{%bsef_route}}' );

        $this->dropTable( '{{%bsef_route}}' );
        $this->dropTable( '{{%bsef_params}}' );
        $this->dropTable( '{{%bsef}}' );
    }
}
