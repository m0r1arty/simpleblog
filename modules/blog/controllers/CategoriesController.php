<?php

/**
 * Файл содержит урезанный контроллер категорий
 * @author M0r1arty <m0r1arty.nv@yandex.ru>
 */

namespace app\modules\blog\controllers;

use Yii;
use app\modules\blog\models\Categories;
use yii\data\ActiveDataProvider;
use app\modules\blog\components\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * CategoriesController implements the CRUD actions for Categories model.
 * Из CRUD генерации удалён action view + настроен AccessControl(только авторизованные) + настроена пагинация
 */
class CategoriesController extends Controller
{
    /**
     * Значение по-умолчанию сколько категорий показывать на странице.
     */
    const DEFAULT_RECORDS_PER_PAGE = 25;
    /**
     * @var int $recordsPerAdminPage количество постов в CRUD таблице.
     */
    public $categoriesPerAdminPage = self::DEFAULT_RECORDS_PER_PAGE;

    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'access' =>[
                'class' => \yii\filters\AccessControl::className(),
                'rules' => [
                    [
                        'actions' => [ 'index', 'create', 'update', 'delete' ],
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
     * Lists all Categories models.
     * @return mixed
     */
    public function actionIndex()
    {
        $dataProvider = new ActiveDataProvider( [
            'query' => Categories::find(),
            'pagination' => [
                'pageSize' => $this->categoriesPerAdminPage,
            ],
        ] );

        return $this->render( 'index', [
            'dataProvider' => $dataProvider,
        ] );
    }

    /**
     * Creates a new Categories model.
     * If creation is successful, the browser will be redirected to the 'index' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Categories( [ 'scenario' => Categories::SCENARIO_WEB ] );

        /* @var \yii\db\Transaction $transaction */
        $transaction = Yii::$app->db->beginTransaction();

        try {
            if ( $model->load( Yii::$app->request->post() ) && $model->save() ) {

                $transaction->commit();
                return $this->redirect( [ 'index' ] );
            }
        } catch( \Exception $e ) {
            $transaction->rollBack();
            throw $e;
        } catch( \Throwable $e ) {
            $transaction->rollBack();
            throw $e;
        }

        return $this->render( 'create', [
            'model' => $model,
        ] );
    }

    /**
     * Updates an existing Categories model.
     * If update is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        $model->scenario = Categories::SCENARIO_WEB;

        /* @var \yii\db\Transaction $transaction */
        $transaction = Yii::$app->db->beginTransaction();

        try {
            if ( $model->load( Yii::$app->request->post() ) && $model->save() ) {
                $transaction->commit();
                return $this->redirect( [ 'index' ] );
            }
        } catch( \Exception $e ) {
            $transaction->rollBack();
            throw $e;
        } catch( \Throwable $e ) {
            $transaction->rollBack();
            throw $e;
        }

        return $this->render( 'update', [
            'model' => $model,
        ] );
    }

    /**
     * Deletes an existing Categories model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        /* @var \yii\db\Transaction $transaction */
        $transaction = Yii::$app->db->beginTransaction();

        try {
            $this->findModel($id)->delete();
            $transaction->commit();
        } catch ( \Exception $e ) {
            $transaction->rollBack();
            throw $e;
        } catch( \Throwable $e ) {
            $transaction->rollBack();
            throw $e;
        }

        return $this->redirect(['index']);
    }

    /**
     * Finds the Categories model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Categories the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Categories::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
