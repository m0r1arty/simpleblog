<?php

namespace app\modules\sef\models;

use Yii;

/**
 * This is the model class for table "bsef_route".
 *
 * @property int $route_id
 * @property int $crc
 * @property string $route
 */
class BSefRoute extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'bsef_route';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['crc'], 'integer'],
            [['route'], 'string', 'max' => 100],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'route_id' => 'Route ID',
            'crc' => 'Crc',
            'route' => 'Route',
        ];
    }
}
