<?php

/**
  *
  */

namespace app\commands;

use yii\console\Controller;
use yii\console\ExitCode;
use app\models\Users;

/**
  *
  */
class UsersController extends Controller
{
   /**
     * Эта команда создаст пользователя со случайным или заданным паролем
     * @param string $username Имя пользователя
     * @param string $password Пароль(не обязательно)
     * @return int Exit code
     */
    public function actionCreate( $username, $password = "" ) {
        $model = Users::findByUsername( $username );

        if (!is_null($model)) {
        	$this->stderr("User " . $username . " already exists\n");
        	return ExitCode::DATAERR;
        }

        $model = new Users();

        $model->login = $username;

        if (empty($password)) {
        	$password = \Yii::$app->security->generateRandomString(14);
        }

        $model->password = md5($password);

        $this->stdout("User \"" . $username . "\", Password: \"" . $password . "\"\n");

        if ($model->save()) {
        	return ExitCode::OK;
        }else{
        	return ExitCode::OSERR;
        }
    }
}