<?php

/**
 * Файл содержит модель для работы с категориями блога.
 */

namespace app\modules\blog\models;

use Yii;

use yii\behaviors\TimestampBehavior;
use yii\behaviors\SluggableBehavior;

/**
 * This is the model class for table "categories".
 * К модели подключены поведения TimestampBehavior, SluggableBehavior.
 * @property int $category_id
 * @property string $title
 * @property string $slug
 * @property int|null $created_at
 * @property int|null $updated_at
 */
class Categories extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'categories';
    }

    /**
     * TimestampBehavior для автоматического заполнения полей created_at и updated_at
     * SluggableBehavior для автоматического заполнения поля slug
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            TimestampBehavior::className(),
            [
                'class' => SluggableBehavior::className(),
                'attribute' => 'title',
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['created_at', 'updated_at'], 'integer'],
            [['title'], 'string', 'max' => 255],
            [['slug'], 'string', 'max' => 60],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'category_id' => 'ID',
            'title' => 'Название',
            'slug' => 'slug',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }
}
