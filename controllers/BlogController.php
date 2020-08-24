<?php

namespace app\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;

use app\models\LoginForm;
use app\models\blog\Categories;

class BlogController extends \app\components\Controller
{
	const DEFAULT_RECORDS_PER_PAGE = 25;

	public $recordsPerPage = self::DEFAULT_RECORDS_PER_PAGE;

    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'actions' => [ 'index', 'signout' ],
                        'allow' => true,
                        'roles' => [ '@' ],
                    ],
                    [
                    	'actions' => [ 'index', 'signin' ],
                    	'allow' => true,
                    	'roles' => [ '?' ],
                    ],
                ],
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
        ];
    }

	public function actionIndex()
	{
		return $this->render( 'index', array_merge( [
		], $this->viewParams ) );
		//die( var_dump( \yii\helpers\Inflector::slug( 'Приветик' ) ) );
		/*
		$model = new Categories();

		$model->title = 'Приветик';
		$model->save();
		*/
	}

	public function actionSignin()
	{
        if (!Yii::$app->user->isGuest) {
            return $this->goHome();
        }

        $model = new LoginForm();

        $model->load( Yii::$app->request->post() ) && $model->login();

        return $this->goBack();
	}

    public function actionSignout()
    {
        Yii::$app->user->logout();

        return $this->goHome();
    }
}