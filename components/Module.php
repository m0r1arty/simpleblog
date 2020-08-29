<?php

/**
 * Файл содержит базовый модуль приложения.
 * @author M0r1arty <m0r1arty.nv@yandex.ru>
 */

namespace app\components;

use Yii;
use yii\base\BootstrapInterface;
use yii\base\Module as BaseModule;

/**
 * \app\components\Module предназначен создать контекст для работы других модулей.
 */
class Module extends BaseModule implements BootstrapInterface
{
	/**
	 * @inheritdoc
	*/
	public function behaviors()
	{
		return array_merge( parent::behaviors(), [
			//application module behaviors
		] );
	}

	/**
	 * @inheritdoc
	*/
	public function bootstrap( $app )
	{
		//
	}
}
