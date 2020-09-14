<?php

namespace app\modules\grabber\controllers;

use Yii;
use app\modules\grabber\models\TaskInstances;
use yii\data\ActiveDataProvider;
use app\modules\blog\components\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

use app\modules\grabber\models\Tasks;

/**
 * TasksController implements the CRUD actions for TaskInstances model.
 */
class TasksController extends Controller
{
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => \yii\filters\AccessControl::className(),
                'except' => [ 'index' ],
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
     * Lists all TaskInstances models.
     * @return mixed
     */
    public function actionIndex()
    {
        $dataProvider = new ActiveDataProvider([
            'query' => TaskInstances::find(),
        ]);

        return $this->render('index', [
            'dataProvider' => $dataProvider,
            'tasks' => Tasks::find()->all(),
        ]);
    }

    /**
     * Creates a new TaskInstances model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate( $id )
    {
        $model = new TaskInstances();

        $model->task_id = intval( $id );

        /* @var \app\modules\grabber\models\Tasks $model2 */
        $model2 = Tasks::findOne( $id );
        
        $conf = [
            'class' => $model2->class,
        ];
        
        $model->attachBehavior( 'taskBehavior', $conf );

        if ( $model->load( Yii::$app->request->post() ) && $model->save() ) {
            return $this->redirect(['index']);
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing TaskInstances model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        $model->attachBehavior( 'taskBehavior', $model->task->class );

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['index']);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing TaskInstances model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the TaskInstances model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return TaskInstances the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = TaskInstances::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
