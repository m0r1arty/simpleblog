<?php

/**
 * Файл содержит модель для связки записей и категорий по типу один ко многим(запись по категорями)
 * @author M0r1arty <m0r1arty.nv@yandex.ru>
 */
namespace app\modules\blog\models;

use Yii;

/**
 * This is the model class for table "record2category".
 *
 * @property int $id
 * @property int|null $record_id
 * @property int|null $category_id
 */
class Record2Category extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'record2category';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['record_id', 'category_id'], 'integer'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'record_id' => 'Record ID',
            'category_id' => 'Category ID',
        ];
    }
}
