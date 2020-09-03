<?php

/**
 * Файл содержит класс Controller, расширяющий контроллер приложения.
 * @author M0r1arty <m0r1arty.nv@yandex.ru>
 */

namespace app\modules\sef\components;

use Yii;
use app\components\Controller as AppController;

/**
 * Данный контроллер предназначен для создания контекста контроллеров модуля sef.
 */
class Controller extends AppController
{
	/**
	 * {@inheritdoc}
	 */
	public function init()
	{
		parent::init();
	}
}
