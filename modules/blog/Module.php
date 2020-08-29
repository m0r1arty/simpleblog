<?php

/**
 * Файл содержит модуль блога. Сам модуль тесно взаимодействует с модулем sef(ЧПУ).
 * Модуль содержит контроллеры BlogController и CategoriesController.
 * BlogController отвечает за вывод постов в общей ленте и по категориям, а так же CRUD функционал(сгенерированный gii).
 * CategoriesController содержит исключительно CRUD функционал(сгенерированный gii).
 * Для доступа к CRUD функционалу необходима авторизация. Создать учётные данные можно консольной командой:
 * 		yii users/create username
 * 
 * @author M0r1arty <m0r1arty.nv@yandex.ru>
 */

namespace app\modules\blog;

use Yii;

use app\components\Module as AppModule;

/**
 * Класс модуля blog
 */
class Module extends AppModule
{
	/**
	 * @inheritdoc
	*/
	public function init()
	{
		parent::init();
	}

	/**
	 * @inheritdoc
	*/
	public function bootstrap( $app )
	{
		parent::bootstrap( $app );
	}
}
