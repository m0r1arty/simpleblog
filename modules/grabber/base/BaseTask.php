<?php

/**
 */

namespace app\modules\grabber\base;

use Yii;

use app\modules\grabber\exceptions\RecordModelException;

use app\modules\blog\models\Records;

/**
 */
abstract class BaseTask extends BaseBehavior
{
	/**
	 * @var int $maxRecords если нет точки сохранения(ид поста, даты поста) - столько максимально брать записей
	 */
	public $maxRecords = 10;
	/**
	 * Идентификатор пользователя от имени которого будут заноситься записи
	 */
	public $user_id = 1;
	public $slugShorter = [
		'class' => 'app\modules\grabber\behaviors\SlugShorter',
		'max' => 60,
		'shortTo' => 57,
	];
	/**
	 */
	public $mailOnEnd = true;
	public $mailOnEndAndNothing = false;
	public $mailOnErrors = true;
	public $reportSubject = 'Task Report';
	public $errorSubject = 'Task Error';
	
	/**
	 */
	abstract public static function taskTitle();
	/**
	 */
	abstract public function run();

 	public function getParsers()
 	{
 		return \app\modules\grabber\models\Parsers::find()->all();
 	}

 	public function getTransports()
 	{
 		return \app\modules\grabber\models\Transports::find()->all();
 	}

 	public function afterGrabbing( $records, $countGrabbed )
 	{
 		/**
 		 * Если нужен отчёт.
 		 */
 		if ( $this->mailOnEnd ) {

 			$params = [
 				'taskTitle' => $this->owner->task->title,
 				'source' => $this->owner->source,
 			];

 			if ( !empty( $records ) ) {
 				$params[ 'records' ] = $records;
 				$params[ 'count' ] = $countGrabbed;

 				Yii::$app->mailer->compose( 'endTask', $params )
 				->setTo( Yii::$app->params[ 'adminEmail' ] )
 				->setFrom( Yii::$app->params[ 'senderEmail' ] )
 				->setSubject( $this->reportSubject )
 				->send();
 			} elseif ( $this->mailOnEndAndNothing ) {
 				Yii::$app->mailer->compose( 'endTaskNothing', $params )
 				->setTo( Yii::$app->params[ 'adminEmail' ] )
 				->setFrom( Yii::$app->params[ 'senderEmail' ] )
 				->setSubject( $this->reportSubject )
 				->send();
 			}
 		}
 	}

 	protected function saveRecord( $record, $categoryIDs )
 	{
 		/* @var \yii\db\Transaction $transaction */
 		$transaction = Yii::$app->db->beginTransaction();

 		try {
 			$model = new Records();

 			/**
 		 	* Отключить поведение, чтобы не лезло прописывать пользователя
 		 	*/
 			$model->detachBehavior( 'userBehavior' );
 			$model->attachBehavior( 'slugshortBehavior', $this->slugShorter );

 			$model->attributes = $record;
 			$model->categoryIDs = $categoryIDs;
 			$model->user_id = $this->user_id;

 			if ( !$model->save() ) {
 				/**
 				 * Плохо, можно сообщить об ошибках, отправив $model->errors
 				 */
 				throw new RecordModelException( $model );
 			}

 			$transaction->commit();

 			return $model;
 		} catch( \Exception $e ) {
 			throw $e;
 			$transaction->rollBack();
 		} catch( \Throwable $e ) {
 			throw $e;
 			$transaction->rollBack();
 		}
 	}
}
