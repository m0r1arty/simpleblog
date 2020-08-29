<?php

/**
 * Файл содержит модель записей.
 */
namespace app\modules\blog\models;

use Yii;

use yii\behaviors\TimestampBehavior;
use yii\behaviors\SluggableBehavior;
use yii\behaviors\AttributeBehavior;

/**
 * This is the model class for table "records".
 * Сгенерированная модель дополнена виртульными свойствами string $date, string $dateCreated и string $dateUpdated.
 * Так же к модели подключены поведения TimestampBehavior, SluggableBehavior и AttributeBehavior.
 *
 * @property int $record_id
 * @property int $user_id
 * @property string $title
 * @property string $preview
 * @property string $content
 * @property string $slug
 * @property int|null $created_at
 * @property int|null $updated_at
 * @property string $date
 * @property string $dateCreated
 * @property string $dateUpdated
 */
class Records extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'records';
    }

    /**
     * TimestampBehavior для автоматического заполнения полей created_at и updated_at
     * SluggableBehavior для автоматического заполнения поля slug
     * AttributeBehavior для автоматического заполнения user_id
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
            [
                'class' => AttributeBehavior::className(),
                'attributes' => [
                    \yii\db\ActiveRecord::EVENT_BEFORE_INSERT => 'user_id',
                ],
                'value' => function ($event) {
                    return Yii::$app->user->id;
                }
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['preview', 'content'], 'string'],
            [['created_at', 'updated_at'], 'integer'],
            [['user_id'], 'integer'],
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
            'record_id' => 'ID',
            'user_id' => 'User ID',
            'title' => 'Заголовок',
            'preview' => 'Превью',
            'content' => 'Запись',
            'slug' => 'ЧПУ',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }

    /**
     * Виртуальное свойство user.
     * @return yii\web\IdentityInterface модель пользователя - владельца записи
     * @throws yii\base\NotSupportedException если запрашивается пользователь для новой записи Records
     */
    public function getUser()
    {
        if ( $this->isNewRecord ) {
            throw new \yii\base\NotSupportedException();
        }
        /* @var $class \app\models\Users */
        $class = Yii::$app->user->identityClass;

        return $this->hasOne( $class::className(), [ 'id' => 'user_id' ]  );
    }

    /**
     * Виртуальное свойство date.
     * @param string $format строковый формат php функции date
     * @return string форматированная дата создания или последняя модификации записи
     * @throws yii\base\NotSupportedException если запрашивается пользователь для новой записи Records
     */
    public function getDate( $format = 'Y.m.d H:i:s' )
    {
        if ( $this->isNewRecord ) {
            throw new \yii\base\NotSupportedException();
        } else {
            $dt = ( $this->created_at !== $this->updated_at )? $this->updated_at : $this->created_at;
            return date( $format, $dt );
        }
    }

    /**
     * Виртуальное свойство dateCreated. Форматирует дату создания записи.
     * @see \app\modules\blog\models\Recods::getDate
     * @param string $format строковый формат php функции date
     * @return string форматированная дата создания или последняя модификации записи
     * @throws yii\base\NotSupportedException если запрашивается пользователь для новой записи Records
     */
    public function getDateCreated( $format = 'Y.m.d H:i:s' )
    {
        if ( $this->isNewRecord ) {
            throw new \yii\base\NotSupportedException();
        } else {
            return date( $format, $this->created_at );
        }
    }

    /**
     * Виртуальное свойство dateUpdated. Форматирует дату редактирования записи.
     * @see \app\modules\blog\models\Recods::getDate
     * @param string $format строковый формат php функции date
     * @return string форматированная дата создания или последняя модификации записи
     * @throws yii\base\NotSupportedException если запрашивается пользователь для новой записи Records
     */
    public function getDateUpdated( $format = 'Y.m.d H:i:s' )
    {
        if ( $this->isNewRecord ) {
            throw new \yii\base\NotSupportedException();
        } else {
            return date( $format, $this->updated_at );
        }
    }
}
