<?php

/**
 * Файл содержит модель записей.
 * @author M0r1arty <m0r1arty.nv@yandex.ru>
 */
namespace app\modules\blog\models;

use Yii;

use yii\helpers\Url;

use yii\behaviors\TimestampBehavior;
use app\modules\sef\behaviors\SluggableBehavior;
use yii\behaviors\AttributeBehavior;

use app\modules\blog\models\Categories;
use app\modules\blog\traits\CategoryIDsTrait;
use app\modules\blog\traits\RetriveCategoriesTrait;
use app\modules\blog\components\CategoriesFilterEvent;
use app\modules\sef\helpers\Sef;

/**
 * This is the model class for table "records".
 * Сгенерированная модель дополнена виртульными свойствами string $date, string $dateCreated и string $dateUpdated.
 * Существует виртуальный атрибут categoryIDs, который содержит все категории, привязанные к записи.
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
class Records extends \yii\db\ActiveRecord implements \app\modules\sef\components\UniqueSlugInterface, \app\modules\blog\components\RetriveCategoriesInterface
{
    /**
     * Событие при запросе категорий для виджета категорий. Предполагается, что фильтр CategoriesFilterBehavior изменит статус категорий(например, default станет error - значит, что с текущим slug выбрать её нельзя, active станет "active error" - выбрана, но не должна быть выбрана(так произойдёт, если записи пытаются менять slug при этом другая запись с таким же slug`ом уже принадлежит выбранной категории ) )
     */
    const EVENT_AFTER_CATEGORIES_FOR_WIDGET_FIND = 'categoriesForWidgetFind';
    /**
     * Сценарий, который позволит отобразить ошибку, вместо автоматического присвоения уникального slug
     */
    const SCENARIO_WEB = 'web';
    /**
     * Трейт, который подключает метод парсинга строки в массив категорий
     */
    use CategoryIDsTrait;
    /**
     * Трейт для traitCategoriesForWidget
     */
    use RetriveCategoriesTrait;
    /**
     * @var string
     */
    private $_categoryIDs = '';
    /**
     * @var string
     */
    private $_oldCategoryIDs = '';
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
     * CategoriesFilterBehavior для ограничения выбора категорий в виджете категорий
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
                'sefAllowedScenarios' => [ self::SCENARIO_WEB ],
                'sefShowErrorsInScenarios' => [ self::SCENARIO_WEB ],
                'uniqueValidator' => [
                    'class' => 'app\modules\sef\validators\UniqueSlugValidator',
                ],
            ],
            'userBehavior' => [
                'class' => AttributeBehavior::className(),
                'attributes' => [
                    \yii\db\ActiveRecord::EVENT_BEFORE_INSERT => 'user_id',
                ],
                'value' => function ($event) {
                    return Yii::$app->user->id;
                }
            ],
            [
                'class' => \app\modules\blog\behaviors\CategoriesFilterBehavior::className(),
            ],
        ];
    }

    /**
     */
    public function scenarios()
    {
        return [
            'default' => [ 'title', 'preview', 'content', 'slug', 'user_id', 'categoryIDs', 'created_at', 'updated_at' ],
            self::SCENARIO_WEB => [ 'title', 'preview', 'content', 'slug', 'user_id', 'categoryIDs', 'created_at', 'updated_at' ],
        ];
    }

    /**
     * Геттер категорий с которыми связана запись блога.
     * @return string пустая строка, если нет привязки к категориям; иначе строка идентификаторов категорий разделённых запятыми.
     */
    public function getCategoryIDs()
    {
        return $this->_categoryIDs;
    }

    /**
     * Сеттер категорий с которыми связана запись блога.
     * @param string $newval строка, содержащая разделённые запятыми категориями с которыми связана запись блога
     */
    public function setCategoryIDs( $newval )
    {
        $this->_oldCategoryIDs = $this->_categoryIDs;
        $this->_categoryIDs = $newval;
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
            /**
             * виртуальный атрибут categoryIDs
             * валидатор categoryRequired применяется через short name, т.е. модуль блога должен его зарегистрировать в методе [[\app\modules\blog\Module::bootstrap]]
             * skipOnEmpty = false необходим, потому что валидатор проверяет, что хотя бы одна категория задана, если skipOnEmpty == true, то будет опущен вызов валидатора
             */
            [['categoryIDs'], 'categoryRequired', 'skipOnEmpty' => false ],
            [['title'], 'required', 'message' => '{attribute} не может быть пустым' ],
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
            'categoryIDs' => 'Маппинг на категории',
            'slug' => 'ЧПУ',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function afterFind()
    {
        parent::afterFind();

        /* @var int[] $ids */
        $ids = $this->retriveCategoryIDs( $this->record_id );

        $this->_categoryIDs = implode( ',', $ids );
    }

    /**
     * {@inheritdoc}
     */
    public function beforeSave( $insert )
    {
        if ( !parent::beforeSave( $insert ) ) {
            return false;
        }
        
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function afterSave( $insert, $changedAttributes )
    {
        parent::afterSave( $insert, $changedAttributes );

        $this->processLinkingCategories( $insert );
    }

    /**
     * {@inheritdoc}
     */
    public function beforeDelete()
    {
        if ( !parent::beforeDelete() ) {
            return false;
        }

        /* @var int[] $ids */
        $ids = [];

        $this->parseCategoryIDs( $this->_categoryIDs, $ids );

        foreach ( $ids as $catid ) {
            Sef::deleteRoute( '/blog/blog/view', [ 'catid' => $catid, 'id' => $this->record_id ] );
        }

        return true;
    }

    /**
     * {@inheritdoc}
     * @throws \Exception если при удалении связей record<->category возникла ошибка
     */
    public function afterDelete()
    {
        Record2Category::deleteAll( [ 'record_id' => $this->record_id ] );
    }

    /**
     * Связь user.
     * @return yii\web\IdentityInterface модель пользователя - владельца записи
     * @throws yii\base\NotSupportedException если запрашивается пользователь для новой записи Records
     */
    public function getUser()
    {
        if ( $this->isNewRecord ) {
            throw new \yii\base\NotSupportedException();
        }
        /* @var \app\models\Users $class */
        $class = Yii::$app->user->identityClass;

        return $this->hasOne( $class::className(), [ 'id' => 'user_id' ]  );
    }

    /**
     * Связь categories. Предоставляет доступ к категориям, привязанным к текущей записи.
     * @return \yii\db\ActiveQuery
     */
    public function getCategories()
    {
        return $this->hasMany( Categories::className(), [ 'category_id' => 'category_id' ] )->via( 'record2Category' );
    }

    /**
     * Связь record2Category.
     */
    public function getRecord2Category()
    {
        return $this->hasMany( Record2Category::className(), [ 'record_id' => 'record_id' ] );
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

    /**
     * Формирует и возвращает массив категорий(ассоциативный массив).
     * В случае, если текущая запись связана с какой-то категорией - она выделяется статусом 'a' - active.
     * @see [[app\modules\blog\widgets\CategoriesWidget::categories]]
     * @return array[] массив элементами которого являются ассоциативные массивы
     */
    public function getCategoriesForWidget()
    {
        /* @var array $ret */
        $ret = $this->traitCategoriesForWidget( $this->retriveCategoryIDs( $this->record_id ) );

        $event = new CategoriesFilterEvent;
        $event->categories = &$ret;

        $this->trigger( self::EVENT_AFTER_CATEGORIES_FOR_WIDGET_FIND, $event );

        return $ret;
    }

    /**
     * @param mixed $catid идентификатор категории, если не задан - будет использован первый из списка
     * @return string url для записи
     */
    public function makeLink( $catid = null )
    {
        if ( is_null( $catid ) ) {
            /* @var int[] */
            $ids = [];
            $this->parseCategoryIDs( $this->_categoryIDs, $ids );
            $catid = $ids[ 0 ];
        } else {
            $catid = intval( $catid );
        }

        return Url::to( [ '/blog/blog/view', 'catid' => $catid, 'id' => $this->record_id ] );
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
        /* @var int[] $ids */
        $ids = [];
        $this->parseCategoryIDs( $this->_categoryIDs, $ids );

        foreach ( $ids as $catid ) {
            /* @var \app\modules\sef\helpers\Route $hRoute */
            $hRoute = Sef::routeInstance( '/blog/blog/index', [ 'catid' => $catid ] );

            if ( !$hRoute->load() ) {
                throw new \app\modules\sef\exceptions\RouteNotFound();
            }

            /* @var bool $slugExists */
            $slugExists = Sef::slugExists( $this->attributes[ $attribute ], $hRoute->sef_id() );

            if ( $slugExists ) {
                /* @var Categories $model */
                $model = Categories::findOne( $catid );

                if ( !is_null( $model ) ) {
                    $validator->message = 'По крайней мере категория {category} имеет такой же slug';
                    $validator->msgParams[ 'category' ] = $model->title;
                }

                return false;
            }
        }

        return true;
    }

    /**
     * Возвращает массив идентификаторов категорий к которым привязана запись блога.
     * @param int[] $categories массив с идентификаторами категорий
     * @param int $record_id идентификатор записи блога
     * @return int[] массив идентификаторов категорий
     */
    protected function retriveCategoryIDs( $record_id )
    {
        /* @var int[] $ids */
        $ids = [];

        foreach ( $this->categories as $category ) {
            $ids[]  = intval( $category->category_id );
        }

        return $ids;
    }

    /**
     * Метод управляет связями между записью и категориями. Должен вызываться только после сохранения записи.
     * Алгоритм:
     * 1. получить категории, которые были привязаны до редактирования(опускается при вставке)
     * 2. получить категории, с которыми устанавливается связь
     * 3. из этих двух массивов вычислить общие категории функцией array_intersect
     * 4. функцией array_diff вычислить категории для разлинковки и категории для линковки
     * 5. произвести вставку и/или удаление связей
     * @param bool $insert если true - производится вставка, false - изменение
     * @throws \Exception если при вставке или удалении возникла ошибка; данный Exception должен перехватываться кодом сохранения записи и откатывать транзакцию
     */
    protected function processLinkingCategories( $insert )
    {
        /* @var int[] $oldIDs идентификаторы категорий, которые были назначены до редактирования*/
        $oldIDs = [];
        
        if ( !$insert ) {
            $this->parseCategoryIDs( $this->_oldCategoryIDs, $oldIDs );
        }

        /* @var int[] $ids идентификаторы категорий, назначенные после редактирования*/
        $ids = [];
        
        $this->parseCategoryIDs( $this->_categoryIDs, $ids );

        /* @var int[] $bothIDs общие идентификаторы ДО-ПОСЛЕ */
        $bothIDs = array_intersect( $oldIDs, $ids );

        /* @var int[] $deleteIDs те, что array_diff $bothIDs и $oldIDs - удалить */
        $deleteIDs = array_diff( $oldIDs, $bothIDs );
        /* @var int[] $insertIDs те, что array_diff $bothIDs и $ids - вставить */
        $insertIDs = array_diff( $ids, $bothIDs );
        
        foreach ( $deleteIDs as $id ) {
            /* @var Record2Category @model */
            $model = Record2Category::find()->where( [ 'record_id' => $this->record_id, 'category_id' => $id ] );
            /* @var int $count */
            $count = intval( $model->count() );
            
            if( $count === 1  ) {
                Sef::deleteRoute( '/blog/blog/view', [ 'catid' => $id, 'id' => $this->record_id ] );

                $count = $model->one()->delete();

                if ( $count === false ) {
                    Yii::error( 'При разлинковке записи и категории возникла ошибка' );
                    throw new \Exception( "[Record2Category] can't delete id: " . $model->id );
                }
            } elseif( $count > 1 ) {
                Yii::warning( 'При разлинковке записи и категории найдено более одной записи' );
                throw new \Exception( "[Record2Category] can't unlink id: " . $model->id . ", count: " . $count );
            } else {
                Yii::warning( 'При разлинковке записи и категории не удалось найти запись' );
                throw new \Exception( "[Record2Category] can't unlink id: " . $model->id . ", count = 0" );
            }
        }

        foreach ( $insertIDs as $id ) {
            /* @var Record2Category @model */
            $model = Record2Category::find()->where( [ 'record_id' => $this->record_id, 'category_id' => $id ] );
            /* @var int $count */
            $count = intval( $model->count() );
            
            if( $count === 0  ) {

                /* @var \app\modules\sef\helpers\Route */
                $hRoute = Sef::routeInstance( '/blog/blog/index', [ 'catid' => $id ] );

                if ( !$hRoute->load() ) {
                    throw new \Exception( 'Невозможно загрузить маршрут для категории' );
                }

                Sef::registerRoute( '/blog/blog/view', [ 'catid' => $id, 'id' => $this->record_id ], $this->slug, $hRoute->sef_id() );

                $model = new Record2Category();

                $model->record_id = $this->record_id;
                $model->category_id = $id;

                if ( !$model->save() ) {
                    Yii::error( 'При линковке записи и категории возникла ошибка' );
                    throw new \Exception( "[Record2Category] can't insert pair: record_id = " . $this->record_id . ", category_id = " . $id );
                }
            } elseif( $count > 0 ) {
                Yii::warning( 'При линковке записи и категории найдены существующие записи в количестве: ' . $count );
                throw new \Exception( "[Record2Category] can't link -> found exists records(" . $count . ")" );
            }
        }
    }
}
