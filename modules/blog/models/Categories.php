<?php

/**
 * Файл содержит модель для работы с категориями блога.
 * @author M0r1arty <m0r1arty.nv@yandex.ru>
 */

namespace app\modules\blog\models;

use Yii;

use yii\behaviors\TimestampBehavior;

use app\modules\blog\models\Records;
use app\modules\blog\models\Record2Category;

use app\modules\sef\behaviors\SluggableBehavior;
use app\modules\sef\helpers\Sef;
use app\modules\sef\helpers\Route;

/**
 * This is the model class for table "categories".
 * К модели подключены поведения TimestampBehavior, SluggableBehavior.
 * @property int $category_id
 * @property string $title
 * @property string $slug
 * @property int|null $created_at
 * @property int|null $updated_at
 */
class Categories extends \yii\db\ActiveRecord implements \app\modules\sef\components\UniqueSlugInterface
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'categories';
    }

    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            TimestampBehavior::className(),
            [
                'class' => SluggableBehavior::className(),
                'attribute' => 'title',
                'ensureUnique' => true,
                'sefAllowedScenarios' => [ 'default' ],
                'uniqueValidator' => [
                    'class' => 'app\modules\sef\validators\UniqueSlugValidator',
                ],
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

    /**
     * {@inheritdoc}
     */
    public function afterSave( $insert, $changedAttributes )
    {
        parent::afterSave( $insert, $changedAttributes );

        if ( $insert ) {
            Sef::registerRoute( '/blog/blog/index', [ 'catid' => $this->category_id ], $this->slug, 1 );
        }
    }

    /**
     * {@inheritdoc}
     */
    public function beforeSave( $insert )
    {
        if ( !parent::beforeSave( $insert ) ) {
            return false;
        }

        if ( !$insert ) {
            if ( $this->isAttributeChanged( 'slug' ) ) {
                Sef::moveSlug( '/blog/blog/index', [ 'catid' => $this->category_id ], $this->slug, 1 );
            }
        }

        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function beforeDelete()
    {
        if ( !parent::beforeDelete() ) {
            return false;
        }

        foreach ( $this->records as $record ) {
            /* @var int $record_id */
            $record_id = intval( $record->record_id );
            /* @var int $count */
            $count = $record->delete();

            if ( $count !== 1 ) {
                throw new \Exception( 'Record(' . $record_id . ') not deleted' );
            }
        }

        Sef::deleteRoute( '/blog/blog/index', [ 'catid' => $this->category_id ]  );

        return true;
    }

    /**
     * Связь records.
     */
    public function getRecords()
    {
        return $this->hasMany( Records::className(), [ 'record_id' => 'record_id' ] )->via( 'record2Category' );
    }

    /**
     * Связь record2Category.
     */
    public function getRecord2Category()
    {
        return $this->hasMany( Record2Category::className(), [ 'category_id' => 'category_id' ] );
    }

    /**
     * Метод checkUniqueSlug имплементирует интерфейс \app\modules\sef\components\UniqueSlugInterface необходимый для проверки slug на уникальность.
     * @param string $attribute имя проверяемого slug-атрибута 
     * @param \app\modules\sef\validators\UniqueSlugValidator $validator текущий экземпляр валидатора
     * @return bool true - если slug уникален относительно корня sef дерева
     * @throws \app\modules\sef\exceptions\RouteNotFoundException если по какой-то причине не удалось найти маршрут для данной категори
     */
    public function checkUniqueSlug( $attribute, $validator )
    {
        return !Sef::slugExists( $this->attributes[ $attribute ], 1 );
    }
}
