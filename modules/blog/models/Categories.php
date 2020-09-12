<?php

/**
 * Файл содержит модель для работы с категориями блога.
 * @author M0r1arty <m0r1arty.nv@yandex.ru>
 */

namespace app\modules\blog\models;

use Yii;

use yii\behaviors\TimestampBehavior;
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

        Sef::deleteRoute( '/blog/blog/index', [ 'catid' => $this->category_id ]  );

        return true;
    }

    /**
     * Метод checkUniqueSlug имплементирует интерфейс \app\modules\sef\components\UniqueSlugInterface необходимый для проверки slug на уникальность.
     * @param string $attribute имя проверяемого slug-атрибута 
     * @return bool true - если slug уникален относительно корня sef дерева
     * @throws \app\modules\sef\exceptions\RouteNotFound если по какой-то причине не удалось найти маршрут для данной категори
     */
    public function checkUniqueSlug( $attribute )
    {
        $slugExists = Sef::slugExists( $this->attributes[ $attribute ], 1 );

        if ( $this->isNewRecord ) {
            return !$slugExists;
        }
        
        /* @var Route $hRoute */
        $hRoute = Sef::routeInstance( '/blog/blog/index', [ 'catid' => $this->category_id ] );

        if ( !$hRoute->load() ) {
            throw new \app\modules\sef\exceptions\RouteNotFound();
        }

        return !$slugExists || ( $slugExists && $this->attributes[ $attribute ] === $hRoute->slug() );
    }
}
