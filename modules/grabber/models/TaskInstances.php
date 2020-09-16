<?php

namespace app\modules\grabber\models;

use Yii;

use app\modules\grabber\models\Tasks;
use app\modules\grabber\models\Transports;
use app\modules\grabber\models\Parsers;

use app\modules\blog\traits\CategoryIDsTrait;
use app\modules\blog\traits\RetriveCategoriesTrait;
/**
 * This is the model class for table "taskinstances".
 *
 * @property int $id
 * @property int $task_id
 * @property int $transport_id
 * @property int $parser_id
 * @property string $params
 */
class TaskInstances extends \yii\db\ActiveRecord implements \app\modules\blog\components\RetriveCategoriesInterface
{
    use CategoryIDsTrait;
    use RetriveCategoriesTrait;

    /* @var string $source источник данных для транспорта */
    public $source = '';
    /* @var string разделённый запятыми список идентификаторов категорий куда задача будет складывать посты с собранными данными */
    public $categoryIDs = '';

    /* @var array[string] массив с параметрами, которые должны использоваться во время запуска задачи: идентификатор и/или дата поста на сайте(как точка останова сбора данных) */
    protected $otherParams = [];
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'taskinstances';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['task_id', 'transport_id', 'parser_id'], 'required'],
            [['task_id', 'transport_id', 'parser_id'], 'integer'],
            [['params'], 'string'],
            [['source'], 'required'],
            [['categoryIDs'], '\app\modules\blog\validators\CategoryRequiredValidator' ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'task_id' => 'Задача',
            'transport_id' => 'Транспорт',
            'parser_id' => 'Парсер',
            'params' => 'Params',
            'source' => 'Источник(url,директория)',
            'categoryIDs' => 'Категории',
        ];
    }

    /**
     * Связь task
     */
    public function getTask()
    {
        return $this->hasOne( Tasks::className(), [ 'task_id' => 'task_id' ] );
    }

    /**
     * Связь parser
     */
    public function getTransport()
    {
        return $this->hasOne( Transports::className(), [ 'transport_id' => 'transport_id' ] );
    }

    /**
     * Связь parser
     */
    public function getParser()
    {
        return $this->hasOne( Parsers::className(), [ 'parser_id' => 'parser_id' ] );
    }

    /**
     * Реализация интерфейса RetriveCategoriesInterface
     * @return array[] массив элементами которого являются ассоциативные массивы
     */
    public function getCategoriesForWidget()
    {
        /* @var int[] $ids */
        $ids = [];

        $this->parseCategoryIDs( $this->categoryIDs, $ids );
        $ret = $this->traitCategoriesForWidget( $ids );
        return $ret;
    }

    /**
     * {@inheritdoc}
     */
    public function beforeSave( $insert )
    {
        if ( !parent::beforeSave( $insert ) ) {
            return false;
        }

        $this->params = json_encode( array_merge( [ 'source' => $this->source, 'categoryIDs' => $this->categoryIDs ], $this->otherParams ) );

        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function afterFind()
    {
        $params = json_decode( $this->params, true );

        $this->source = $params[ 'source' ];
        $this->categoryIDs = $params[ 'categoryIDs' ];

        $this->otherParams = array_filter( $params, function ( $key ) {
            if ( !in_array( $key, [ 'source', 'categoryIDs' ] ) ) {
                return true;
            }
            return false;
        }, ARRAY_FILTER_USE_KEY );
    }

    public function getParam( $name )
    {
        return $this->otherParams[ $name ] ?? NULL;
    }

    public function setParam( $name, $val )
    {
        $this->otherParams[ $name ] = $val;
    }
}
