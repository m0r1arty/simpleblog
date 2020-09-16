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
	 * @var string $coreClass класс для передачи Yii::createObject для создания ядра граббера
	 */
	public $coreClass = 'app\modules\grabber\cores\Core1';
	/**
	 * Конфиг задачи
	 * @see [[Yii::configure]]
	 */
	public $taskConfig = [
		'maxRecords' => 25,//если у задачи нет сохранения(дата до какого поста надо грабить или ид поста) - это максимальное количество постов(после отработки задача должна сохранить дату/ид поста)
	];
	/**
	 */
	public $mailPath = '@grabber/mail';
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

		if ( $app instanceof \yii\web\Application ) {
			$app->getUrlManager()->addRules([
				[ 'pattern' => $this->id . '/tasks/create/<id:\d+>', 'route' => $this->id . '/tasks/create' ],
				[ 'pattern' => $this->id . '/<controller:[\w\-]+>/<action:[\w\-]+>', 'route' => $this->id . '/<controller>/<action>' ],
			] );
		} elseif ( $app instanceof \yii\console\Application ) {
			$app->controllerMap[ $this->id ] = [
				'class' => 'app\modules\grabber\console\CoreController',
			];
		}
	}
}
