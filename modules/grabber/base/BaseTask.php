<?php

/**
 * Файл содержит базовый класс для задач BaseTask
 * @author M0r1arty <m0r1arty.nv@yandex.ru>
 */

namespace app\modules\grabber\base;

use Yii;

use app\modules\grabber\exceptions\RecordModelException;
use app\modules\blog\models\Records;

/**
 * Базовый класс BaseTask для задач содержит настройки задачи, а так же логику сохранения записи блога и отчёта о завершённой задаче.
 */
abstract class BaseTask extends BaseBehavior
{
	/**
	 * @var int $maxRecords если нет точки сохранения(ид поста, даты поста) - столько максимально брать записей
	 */
	public $maxRecords = 10;
	/**
	 * @var int $user_id идентификатор пользователя от имени которого будут заноситься записи
	 */
	public $user_id = 1;
	/**
	 * @var array[string] $slugShorter конфигурация поведения SlugShorter для слишком длинных slug`ов
	 */
	public $slugShorter = [
		'class' => 'app\modules\grabber\behaviors\SlugShorter',
		'max' => 60, // если strlen( slug ) > max
		'shortTo' => 57, // обрезать до 57 символов
	];
	/**
	 *  @var bool $mailOnEnd отсылать ли письма администратору о награбленных записях
	 */
	public $mailOnEnd = true;
	/**
	 * @var bool $mailOnEndAndNothing отсылать ли письма администратору, если новых записей не обнаружено
	 */
	public $mailOnEndAndNothing = false;
	/**
	 * @var bool $mailOnErrors отсылать ли письма администратору об ошибках при выполнении задачи
	 */
	public $mailOnErrors = true;
	/**
	 * @var string $reportSubject тема письма с отчётом
	 */
	public $reportSubject = 'Task Report';
	/**
	 * @var string $errorSubject тема письма с информацией об ошибке
	 */
	public $errorSubject = 'Task Error';
	
	/**
	 * Возвращает название задачи, зарегистрированное в таблице {{%tasks}}
	 * @return string тайтл задачи
	 */
	abstract public static function taskTitle();
	/**
	 * Метод вызывается, чтобы выполнить задачу
	 */
	abstract public function run();

	/**
	 * Метод для получения списка парсеров. Может быть переопредён, чтобы отфильтровать только поддерживаемые парсеры.
	 */
 	public function getParsers()
 	{
 		return \app\modules\grabber\models\Parsers::find()->all();
 	}
	/**
	 * Метод для получения списка транспортов. Может быть переопредён, чтобы отфильтровать только поддерживаемые транспорты.
	 */
 	public function getTransports()
 	{
 		return \app\modules\grabber\models\Transports::find()->all();
 	}

 	/**
 	 * Метод afterGrabbing выполняется после завершения задачи
 	 * @param array[string] $records ассоциативный массив со списком полученных записей с ключами id,link,title,preview,content
 	 * @param int $countGrabbed сколько было сохранено записей
 	 */
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

 	/**
 	 * Метод saveRecord сохраняет запись в таблице записей
 	 * @param array[string] ассоциативный массив представляющий запись, который вернул парсер. Ключи: title,preview,content.
 	 * @param string $categoryIDs список категорий, разделённый запятыми
 	 */
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
 			$transaction->rollBack();
 			throw $e;
 		} catch( \Throwable $e ) {
 			$transaction->rollBack();
 			throw $e;
 		}
 	}
}
