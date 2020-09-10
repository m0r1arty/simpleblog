<?php

namespace app\modules\sef\models;

use Yii;

/**
 * This is the model class for table "bsef".
 *
 * @property int $id
 * @property int $route_id
 * @property int $sef_id
 * @property int $crc
 * @property string $params
 */
class BSef extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'bsef';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['route_id', 'sef_id', 'crc'], 'integer'],
            [['params'], 'string', 'max' => 60],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'route_id' => 'Route ID',
            'sef_id' => 'Sef ID',
            'crc' => 'Crc',
            'params' => 'Params',
        ];
    }
}
