<?php

/**
 */
 
 namespace app\modules\grabber\console;

use Yii;

use yii\console\Controller;
use yii\console\ExitCode;
use yii\console\widgets\Table;

use app\modules\grabber\models\TaskInstances;

/**
 * Класс предназначен для ручного запуска задач.
 */
class CoreController extends Controller
{
	/**
	 * Отображает список текущих задач
	 */
	public function actionIndex()
	{
		$rows = [];

		foreach ( TaskInstances::find()->all() as $ti ) {
			$rows[] = [ $ti->id, $ti->task->title, $ti->transport->title, $ti->parser->title, $ti->source ];
		}
		echo Table::widget( [
			'headers' => ['ID', 'Задача', 'Транспорт', 'Парсер', 'Источник'],
			'rows' => $rows,
		]);
	}

	/**
	 * Запускает на выполнение задачу по заданному идентификатору
	 * @param int $id
	 */
	public function actionRun( $id )
	{
		$id = intval( $id );

		$ti = TaskInstances::findOne( $id );

		if ( is_null( $ti ) ) {
			$this->stderr( 'Task not found' );
			return ExitCode::UNSPECIFIED_ERROR;
		}
		
		/* @var app\modules\grabber\Module $grabber */
		$grabber = Yii::$app->getModule( 'grabber' );
		
		if ( is_null( $grabber ) ) {
			$this->stderr( 'Grabber module not found' );
			return ExitCode::UNSPECIFIED_ERROR;
		}

		if ( $grabber->mailPath !== false ) {
			Yii::$app->mailer->setViewPath( $grabber->mailPath );
		}

		$coreConfig = [
			'class' => $grabber->coreClass,
			'taskConfig' => $grabber->taskConfig,
		];

		$core = Yii::createObject( $coreConfig );
		$core->run( $ti );
		
		return ExitCode::OK;
	}

	/**
	 * Запускает на выполнение все задачи
	 */
	public function actionRunall()
	{

		/* @var app\modules\grabber\Module $grabber */
		$grabber = Yii::$app->getModule( 'grabber' );
		
		if ( is_null( $grabber ) ) {
			$this->stderr( 'Grabber module not found' );
			return ExitCode::UNSPECIFIED_ERROR;
		}

		if ( $grabber->mailPath !== false ) {
			Yii::$app->mailer->setViewPath( $grabber->mailPath );
		}

		$coreConfig = [
			'class' => $grabber->coreClass,
			'taskConfig' => $grabber->taskConfig,
		];

		$core = Yii::createObject( $coreConfig );

		foreach ( TaskInstances::find()->all() as $ti ) {
			$core->run( $ti );
		}

		return ExitCode::OK;
	}
}
