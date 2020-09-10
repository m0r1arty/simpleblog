<?php

namespace app\modules\sef\models;

use Yii;

/**
 * This is the model class for table "bsef_params".
 *
 * @property int $param_id
 * @property int $crc
 * @property string $param
 */
class BSefParams extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'bsef_params';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['crc'], 'integer'],
            [['param'], 'string', 'max' => 60],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'param_id' => 'Param ID',
            'crc' => 'Crc',
            'param' => 'Param',
        ];
    }
}
