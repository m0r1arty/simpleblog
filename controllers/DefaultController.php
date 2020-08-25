<?php

/**
 * Файл содержит DefaultController, который является необходимой прокладкой
 * к функционалу модуля blog.
 * @author M0r1arty <m0r1arty.nv@yandex.ru>
 */

namespace app\controllers;

use Yii;

use yii\filters\AccessControl;
use app\models\LoginForm;

/**
 * Класс DefaultController отвечает за авторизацию/деавторизацию пользователей, а так же отвечает за отображение ошибки.
 * {@inheritdoc}
 */
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

    /**
     * Данный action отвечает за авторизацию пользователя в системе
     * @return \yii\web\Response
     */
	public function actionSignin()
	{
        if (!Yii::$app->user->isGuest) {
            return $this->goHome();
        }

        /* @var $model \app\models\LoginForm */
        $model = new LoginForm();

        $model->load( Yii::$app->request->post() ) && $model->login();

        return $this->goBack();
	}

	/**
	 * Данный action удаляет авторизацию пользователя
	 * @return \yii\web\Response
	 */
    public function actionSignout()
    {
        Yii::$app->user->logout();

        return $this->goHome();
    }
}
