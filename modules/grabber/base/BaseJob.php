<?php

/**
 * Файл содержит базовый класс BaseJob для задач, запускаемых через расширение queue.
 * @author M0r1arty <m0r1arty.nv@yandex.ru>
 */
namespace app\modules\grabber\base;

use Yii;

use yii\base\Component;

class BaseJob extends Component
{
	/* @var app\modules\grabber\Module $grabber */
	public $grabber = null;
	/* @var app\modules\grabber\interfaces\CoreInterface $core */
	public $core = null;

	/**
	 * Метод настраивает среду выполнения задачи
	 */
	public function jobInit()
	{
		$this->grabber = Yii::$app->getModule( 'grabber' );

		if ( is_null( $this->grabber ) ) {
			return;
		}

		/**
		 * Если необходимо изменяем путь до шаблона писем
		 */
		if ( $this->grabber->mailPath !== false ) {
			Yii::$app->mailer->setViewPath( $this->grabber->mailPath );
		}

		/**
		 * Создаём ядро задачи
		 */
		$coreConfig = [
			'class' => $this->grabber->coreClass,
			'taskConfig' => $this->grabber->taskConfig,
		];

		$this->core = Yii::createObject( $coreConfig );
	}
}
