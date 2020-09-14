<?php

namespace app\modules\grabber\models;

use Yii;

/**
 * This is the model class for table "parsers".
 *
 * @property int $task_id
 * @property string $title
 * @property string $class
 */
class Parsers extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'parsers';
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
            'parser_id' => 'Parser ID',
            'title' => 'Title',
            'class' => 'Class',
        ];
    }
}
