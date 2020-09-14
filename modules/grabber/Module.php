<?php

/**
 * Файл содержит модуль граббера
 * @author M0r1arty <m0r1arty.nv@yandex.ru>
 */

namespace app\modules\grabber;

use Yii;

use app\components\Module as AppModule;

/**
 * Класс модуля grabber.
 */

class Module extends AppModule
{
	/**
	 * @var string|bool $alias регистрация алиаса @grabber или другого; false отменяет регистрацию
	 */
	public $alias = '@grabber';
	/**
	 * @inheritdoc
	*/
	public function init()
	{
		parent::init();
	}

	/**
	 * Регистрирует, если нужно, алиас @grabber или другой.
	 * @inheritdoc
	*/
	public function bootstrap( $app )
	{
		parent::bootstrap( $app );

		if ( ! ( $this->alias === false ) ) {
			Yii::setAlias( $this->alias, '@app/modules/grabber' );
		}

		$app->getUrlManager()->addRules([
			[ 'pattern' => $this->id . '/tasks/create/<id:\d+>', 'route' => $this->id . '/tasks/create' ],
			[ 'pattern' => $this->id . '/<controller:[\w\-]+>/<action:[\w\-]+>', 'route' => $this->id . '/<controller>/<action>' ],
		] );
	}
}
