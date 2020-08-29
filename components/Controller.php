<?php

/**
 * Файл содержит базовый контроллер для остальных контроллеров приложения.
 * @author M0r1arty <m0r1arty.nv@yandex.ru>
 */

namespace app\components;

use Yii;
use yii\web\Controller as WebController;

use app\models\LoginForm;

/**
 * \app\components\Controller предназначен создать контекст для работы конечных контроллеров.
 */
class Controller extends WebController
{
	/**
	 * {@inheritdoc}
	 */
	public function init()
	{
		parent::init();
	}
}
