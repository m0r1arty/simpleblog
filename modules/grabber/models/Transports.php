<?php

namespace app\modules\grabber\models;

use Yii;

/**
 * This is the model class for table "transports".
 *
 * @property int $task_id
 * @property string $title
 * @property string $class
 */
class Transports extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'transports';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['class'], 'string'],
            [['title'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'transport_id' => 'Transport ID',
            'title' => 'Title',
            'class' => 'Class',
        ];
    }
}
