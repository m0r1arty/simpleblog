<?php

/**
 * Файл содержит модель записей.
 * @author M0r1arty <m0r1arty.nv@yandex.ru>
 */
namespace app\modules\blog\models;

use Yii;

use yii\helpers\Url;

use yii\behaviors\TimestampBehavior;
use yii\behaviors\SluggableBehavior;
use yii\behaviors\AttributeBehavior;

use app\modules\blog\traits\CategoryIDsTrait;

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
class Records extends \yii\db\ActiveRecord
{
    /**
     * Трейт, который подключает метод парсинга строки в массив категорий
     */
    use CategoryIDsTrait;
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
     * Возвращает массив идентификаторов категорий к которым привязана запись блога.
     * @param int $record_id идентификатор записи блога
     * @return int[] массив идентификаторов категорий
     */
    protected function retriveCategoryIDs( $record_id )
    {
        /* @var Record2Category $model */
        $model = Record2Category::find()->where( [ 'record_id' => $record_id ] );

        /* @var int[] */
        $ids = array_map(
            function ( $r2c )
            {
                return intval( $r2c->category_id );
            }, $model->all()
        );

        return $ids;
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
            [['title', 'slug'], 'required', 'message' => '{attribute} не может быть пустым' ],
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
     * Метод управляет связями между записью и категориями. Должен вызываться только после сохранения записи.
     * Алгоритм:
     * 1. получить категории, которые были привязаны до редактирования(опускается при вставке)
     * 2. получить категории, с которыми устанавливается связь
     * 3. из этих двух массивов вычислить общие категории функцией array_intersect
     * 4. функцией array_diff вычислить категории для разлинковки и категории для линковки
     * 5. произвести вставку и/или удаление связей
     * @throws \Exception если при вставке или удалении возникла ошибка; данный Exception должен перехватываться кодом сохранения записи и откатывать транзакцию
     */
    protected function processLinkingCategories()
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

    /**
     * {@inheritdoc}
     */
    public function afterSave( $insert, $changedAttributes )
    {
        parent::afterSave( $insert, $changedAttributes );

        $this->processLinkingCategories();
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
     * Виртуальное свойство user.
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
     * Виртуальное свойство categories. Возвращает категории, привязанные к текущей записи.
     * @return \yii\db\ActiveQuery
     */
    public function getCategories()
    {
        /* @var string $categoriesTable */
        $categoriesTable = \app\modules\blog\models\Categories::tableName();
        /* @var string $r2cTable */
        $r2cTable = \app\modules\blog\models\Record2Category::tableName();

        $query = new \yii\db\ActiveQuery( \app\modules\blog\models\Categories::className() );
        $query->select( $categoriesTable . '.*' )->from( $categoriesTable )
            ->innerJoin( $r2cTable, $categoriesTable . '.category_id = ' . $r2cTable . '.category_id' )
            ->where( $r2cTable . '.record_id = :id', [ 'id' => $this->record_id ] );
        
        return $query;
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
        $ret = [];

        /* @var \app\modules\blog\models\Categories $models */
        $models = Categories::find()->all();

        if ( $this->isNewRecord ) {
            foreach ( $models as $category ) {
                $ret[] = [
                    'id' => $category->category_id,
                    'title' => $category->title,
                    'link' => Url::to( [ '/blog/blog/index', 'catid' => $category->category_id ] ),
                    'status' => 'd',
                ];
            }
        } else {
            /* @var int[] $ids */
            $ids = $this->retriveCategoryIDs( $this->record_id );
            foreach ( $models as $category ) {
                $ret[] = [
                    'id' => $category->category_id,
                    'title' => $category->title,
                    'link' => Url::to( [ '/blog/blog/index', 'catid' => $category->category_id ] ),
                    'status' => ( ( in_array( $category->category_id, $ids ) ) ?  'a' : 'd' ),
                ];
            }
        }

        return $ret;
    }
}
