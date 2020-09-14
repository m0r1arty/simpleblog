<?php

use yii\db\Migration;

/**
 * Class m200913_102522_grabber_tables
 */
class m200913_102522_grabber_tables extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        if ($this->db->driverName === 'sqlite') {
            $tableOptions = null;

            $this->createTable( '{{%tasks}}',[
                'task_id' => $this->bigPrimaryKey(),
                'title' => $this->string( 255 )->notNull()->defaultValue( "" ),
                'class' => $this->text()->notNull()->defaultValue( "" ),
            ], $tableOptions );

            $this->createTable( '{{%taskinstances}}',[
                'id' =>$this->bigPrimaryKey(),
                'task_id' => $this->bigInteger()->notNull(),
                'transport_id' => $this->bigInteger()->notNull(),
                'parser_id' => $this->bigInteger()->notNull(),
                'params' => $this->text()->notNull()->defaultValue( "" ),
            ], $tableOptions );

            $this->createTable( '{{%transports}}',[
                'transport_id' => $this->bigPrimaryKey(),
                'title' => $this->string( 255 )->notNull()->defaultValue( "" ),
                'class' => $this->text()->notNull()->defaultValue( "" ),
            ], $tableOptions );

            $this->createTable( '{{%parsers}}',[
                'parser_id' => $this->bigPrimaryKey(),
                'title' => $this->string( 255 )->notNull()->defaultValue( "" ),
                'class' => $this->text()->notNull()->defaultValue( "" ),
            ], $tableOptions );

            /* @var \app\modules\grabber\models\Tasks $model */
            $model = new \app\modules\grabber\models\Tasks();
            $model->title = \app\modules\grabber\tasks\HttpTask::taskTitle();
            $model->class = '\app\modules\grabber\tasks\HttpTask';
            $model->save();

            $model = new \app\modules\grabber\models\Tasks();
            $model->title = \app\modules\grabber\tasks\ScanDirTask::taskTitle();
            $model->class = '\app\modules\grabber\tasks\ScanDirTask';
            $model->save();

            /* @var \app\modules\grabber\models\Transports $model */
            $model = new \app\modules\grabber\models\Transports();
            $model->title = \app\modules\grabber\transports\HttpTransport::transportTitle();
            $model->class = '\app\modules\grabber\transports\HttpTransport';
            $model->save();

            $model = new \app\modules\grabber\models\Transports();
            $model->title = \app\modules\grabber\transports\DirTransport::transportTitle();
            $model->class = '\app\modules\grabber\transports\DirTransport';
            $model->save();

            /* @var \app\modules\grabber\models\Parsers */
            $model = new \app\modules\grabber\models\Parsers();
            $model->title = \app\modules\grabber\parsers\XmlParser::parserTitle();
            $model->class = '\app\modules\grabber\parsers\XmlParser';
            $model->save();

            $model = new \app\modules\grabber\models\Parsers();
            $model->title = \app\modules\grabber\parsers\JsonParser::parserTitle();
            $model->class = '\app\modules\grabber\parsers\JsonParser';
            $model->save();

            $model = new \app\modules\grabber\models\Parsers();
            $model->title = \app\modules\grabber\parsers\ChelseaBluesParser::parserTitle();
            $model->class = '\app\modules\grabber\parsers\ChelseaBluesParser';
            $model->save();
        }
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable( '{{%parsers}}' );
        $this->dropTable( '{{%transports}}' );
        $this->dropTable( '{{%taskinstances}}' );
        $this->dropTable( '{{%tasks}}' );
    }
}
