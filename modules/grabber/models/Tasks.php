<?php

namespace app\modules\grabber\models;

use Yii;

/**
 * This is the model class for table "tasks".
 *
 * @property int $task_id
 * @property string $title
 * @property string $class
 */
class Tasks extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'tasks';
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
            'task_id' => 'Task ID',
            'title' => 'Title',
            'class' => 'Class',
        ];
    }
}
