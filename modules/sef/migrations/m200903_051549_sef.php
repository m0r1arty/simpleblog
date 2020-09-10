<?php

use yii\db\Migration;

/**
 * Class m200903_051549_sef
 */
class m200903_051549_sef extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        if ($this->db->driverName === 'sqlite') {
            $tableOptions = null;

            $this->createTable( '{{%sef}}',[
                'id' => $this->bigPrimaryKey(),
                'parent_id' => $this->bigInteger()->notNull(),
                'slug' => $this->string( 60 )->notNull()->defaultValue( "" ),
                'params' => $this->text()->notNull(),
            ], $tableOptions );

            $this->createIndex( 'idx-uniq-parent_id-slug', '{{%sef}}', 'parent_id, slug', true );

            /**
             * Сразу добавляем корень
             */
            $model = new \app\modules\sef\models\Sef();

            $model->parent_id = 0;
            $model->slug = '';
            $model->params = json_encode( [] );

            if ( !$model->save() ) {
                $this->dropIndex( 'idx-uniq-parent_id-slug', '{{%sef}}' );
                $this->dropTable( '{{%sef}}' );
                return false;
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropIndex( 'idx-uniq-parent_id-slug', '{{%sef}}' );
        $this->dropTable( '{{%sef}}' );
    }
}
