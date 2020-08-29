<?php

/**
 * Файл содержит BlogController, отвечающий за создание и редактирование записей.
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
     * @return mixed
     */
    public function actionIndex()
    {
        $dataProvider = new ActiveDataProvider([
            'query' => Records::find()->orderBy( [ 'record_id' => 'desc' ] ),
            'pagination' =>
            [
                'pageSize' => $this->recordsPerPage,
            ]
        ]);

        return $this->renderRecordList( $dataProvider );
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
     */
    public function actionCreate()
    {
        $model = new Records();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['list', 'id' => $model->record_id]);
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

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->record_id]);
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
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

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
     * @return string результат рендеринга
     */
    protected function renderRecordList( $dataProvider )
    {
        return $this->render( 'index', [
            'dataProvider' => $dataProvider,
        ] );
    }
}
