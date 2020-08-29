<?php

/**
 * Файл содержит консольную команду users, которая на данный момент содержит всего одну sub-команду create
 * @author M0r1arty <m0r1arty.nv@yandex.ru>
 */

namespace app\commands;

use yii\console\Controller;
use yii\console\ExitCode;
use app\models\Users;

/**
 * Класс предназначен для управления пользователями. В данный момент поддерживает только команду создания пользователя.
 */
class UsersController extends Controller
{
   /**
     * Эта команда создаст пользователя со случайным или заданным паролем
     * @param string $username Имя пользователя
     * @param string $password Пароль(не обязательно)
     * @return int Exit code
     */
    public function actionCreate( $username, $password = "" )
    {
        /* @var \app\models\Users $model */
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
        } else {
        	return ExitCode::OSERR;
        }
    }
}
