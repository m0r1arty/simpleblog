<?php

/**
 * Файл содержит модель Sef
 * @author M0r1arty <m0r1arty.nv@yandex.ru>
 */
namespace app\modules\sef\models;

use Yii;

/**
 * This is the model class for table "sef".
 *
 * @property int $id
 * @property int $parent_id
 * @property string $slug
 * @property string $childs
 */
class Sef extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'sef';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['parent_id', 'params' ], 'required'],
            [['parent_id'], 'integer'],
            [['params'], 'string'],
            [['slug'], 'string', 'max' => 60 ],
            [['parent_id', 'slug'], 'unique', 'targetAttribute' => ['parent_id', 'slug']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'parent_id' => 'Parent ID',
            'slug' => 'Slug',
            'params' => 'Params',
        ];
    }
}
