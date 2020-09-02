<?php

/**
 * Файл содержит BlogController, отвечающий за создание и редактирование записей.
 * @author M0r1arty <m0r1arty.nv@yandex.ru>
 */

namespace app\modules\blog\controllers;

use Yii;
use app\modules\blog\models\Records;
use yii\data\ActiveDataProvider;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * BlogController implements the CRUD actions for Records model.
 */
class BlogController extends \app\modules\blog\components\Controller
{
    /**
     * Значение по-умолчанию сколько записей показывать на странице.
     * Одно значение для листинга постов гостю и CRUD таблицы редактирования.
     */
    const DEFAULT_RECORDS_PER_PAGE = 25;

    /**
     * @var int $recordsPerPage количество постов при листинге гостю
     */
    public $recordsPerPage = self::DEFAULT_RECORDS_PER_PAGE;
    /**
     * @var int $recordsPerAdminPage количество постов в CRUD таблице.
     */
    public $recordsPerAdminPage = self::DEFAULT_RECORDS_PER_PAGE;

    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => \yii\filters\AccessControl::className(),
                'except' => [ 'index', 'view' ],
                'rules' => [
                    [
                        'actions' => [ 'list', 'create', 'update', 'delete' ],
                        'allow' => true,
                        'roles' => [ '@' ],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],
        ];
    }

    /**
     * Метод отображает список постов всего блога
     * @param int $catid Категория, из которой показывать записи
     * @return string
     */
    public function actionIndex( $catid = null )
    {
        if ( !is_null( $catid ) ) {
            $catid = intval( $catid );
        }

        $dataProviderConfig = [
            'query' => Records::find()->orderBy( [ 'record_id' => 'desc' ] ),
            'pagination' =>
            [
                'pageSize' => $this->recordsPerPage,
            ]
        ];

        if ( $catid > 0 ) {
            /* @var string $r2cTableName имя таблицы связей */
            $r2cTableName = \app\modules\blog\models\Record2Category::tableName();
            $dataProviderConfig[ 'query' ]->innerJoin( $r2cTableName, $r2cTableName . '.record_id = records.record_id' )->where( $r2cTableName . '.category_id = :catid', [ 'catid' => $catid ] );
        }

        $dataProvider = new ActiveDataProvider( $dataProviderConfig );

        $params = [ 'dataProvider' => $dataProvider ];

        if ( $catid > 0 ) {
            $params[ 'catid' ] = $catid;
        }

        return $this->renderRecordList( $params );
    }

    /**
     * Lists all Records models.
     * @return mixed
     */
    public function actionList()
    {
        $dataProvider = new ActiveDataProvider([
            'query' => Records::find(),
            'pagination' =>
            [
                'pageSize' => $this->recordsPerAdminPage,
            ]
        ]);

        return $this->render('list', [
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Records model.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new Records model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     * @throws \Exception
     * @throws \Throwable
     */
    public function actionCreate()
    {
        $model = new Records();

        /* @var \yii\db\Transaction $transaction */
        $transaction = Yii::$app->db->beginTransaction();

        try {
            if ($model->load(Yii::$app->request->post()) && $model->validate() && $model->save()) {
                $transaction->commit();
                return $this->redirect(['list']);
            }
        } catch ( \Exception $e ) {
            $transaction->rollBack();
            throw $e;
        } catch ( \Throwable $e ) {
            $transaction->rollBack();
            throw $e;
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing Records model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        /* @var \yii\db\Transaction $transaction */
        $transaction = Yii::$app->db->beginTransaction();

        try {
            if ($model->load(Yii::$app->request->post()) && $model->validate() && $model->save()) {
                $transaction->commit();
                return $this->redirect(['list']);
            }
        } catch ( \Exception $e ) {
            $transaction->rollBack();
            throw $e;
        } catch ( \Throwable $e ) {
            $transaction->rollBack();
            throw $e;
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing Records model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     * @throws \Exception если удалить не получилось, или при удалении случился \Exception
     */
    public function actionDelete($id)
    {
        /* @var \yii\db\Transaction $transaction */
        $transaction = Yii::$app->db->beginTransaction();

        try {
            /* @var int $count */
            $count = $this->findModel( $id )->delete();

            if( $count === false )
            {
                $transaction->rollBack();
                throw \Exception( '[Records] Не удалось удалить запись id = ' . $id );
            }

            $transaction->commit();
        } catch ( \Exception $e ) {
            $transaction->rollBack();
            throw $e;
        } catch ( \Throwable $e ) {
            $transaction->rollBack();
            throw $e;
        }

        return $this->redirect(['list']);
    }

    /**
     * Finds the Records model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Records the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Records::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }

    /**
     * Общий интерфейс рендеринга ленты сообщений всего блога или отдельной категории
     * @param \yii\data\ActiveDataProvider $dataProvider
     * @param array $params массив параметров view
     * @return string результат рендеринга
     */
    protected function renderRecordList( $params )
    {
        return $this->render( 'index', $params );
    }
}
