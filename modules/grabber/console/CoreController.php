<?php

/**
 * Файл содержит консольный контроллер для запуска задач и простановки задач в очередь расширения queue.
 * @author M0r1arty <m0r1arty.nv@yandex.ru>
 */
  namespace app\modules\grabber\console;

use Yii;

use yii\console\Controller;
use yii\console\ExitCode;
use yii\console\widgets\Table;

use app\modules\grabber\models\TaskInstances;
use app\modules\grabber\jobs\RunAllJob;
use app\modules\grabber\jobs\RunOneJob;

/**
 * Класс предназначен для ручного запуска задач и простановки задач в очередь расширения queue
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
	 * @param int $id идентификатор задачи, которую необходимо выполнить
	 */
	public function actionRun( $id )
	{
		$id = intval( $id );

		$ti = TaskInstances::findOne( $id );

		if ( is_null( $ti ) ) {
			$this->stderr( 'Task not found' );
			return ExitCode::UNSPECIFIED_ERROR;
		}
		
		/**
		 * Такой же код в \app\modules\grabber\base\BaseJob, но он выполняется в другом контексте
		 */

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

	/**
	 * Помещает задачу RunOneJob на выполнение
	 * @param int $id идентификатор задачи, которую нужно поместить в очередь
	 */
	public function actionPlace( $id )
	{
		$id = intval( $id );

		/* @var TaskInstances $ti */
		$ti = TaskInstances::findOne( $id );

		if ( is_null( $ti ) ) {
			$this->stderr( 'Task not found' );
			return ExitCode::UNSPECIFIED_ERROR;
		}

		if ( !isset( Yii::$app->queue ) || is_null( Yii::$app->queue ) || !( Yii::$app->queue instanceof \yii\queue\Queue ) ) {
			$this->stderr( 'Queue not found' );
			return ExitCode::UNSPECIFIED_ERROR;
		}


		$jobId = Yii::$app->queue->push( new RunOneJob( [ 'taskInstanceId' => $id ] ) );

		$msg = "ID поставленной задачи {$jobId}";

		echo $msg;
		Yii::info( $msg );

		return ExitCode::OK;
	}

	/**
	 * Помещает задачу RunAllJob на выполнение
	 */
	public function actionPlaceall()
	{
		if ( !isset( Yii::$app->queue ) || is_null( Yii::$app->queue ) || !( Yii::$app->queue instanceof \yii\queue\Queue ) ) {
			$this->stderr( 'Queue not found' );
			return ExitCode::UNSPECIFIED_ERROR;
		}

		$jobId = Yii::$app->queue->push( new RunAllJob() );

		$msg = "ID поставленной задачи {$jobId}";

		echo $msg;
		Yii::info( $msg );

		return ExitCode::OK;
	}
}
