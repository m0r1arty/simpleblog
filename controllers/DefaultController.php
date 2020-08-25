<?php

namespace app\controllers;

use Yii;

use yii\filters\AccessControl;
use app\models\LoginForm;

class DefaultController extends \app\components\Controller
{
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