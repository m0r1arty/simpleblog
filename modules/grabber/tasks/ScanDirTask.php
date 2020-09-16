<?php

/**
 * Файл содержит задачу ScanDirTask
 * @author M0r1arty <m0r1arty.nv@yandex.ru>
 */

namespace app\modules\grabber\tasks;

use Yii;

use app\modules\grabber\base\BaseTask;
use app\modules\grabber\exceptions\DirNotFoundException;
use app\modules\grabber\exceptions\NotAccessibleException;
use app\modules\grabber\exceptions\ContentNotFoundException;

/**
 * Класс ScanDirTask реализует задачу сканирования директории и обработки найденных файлов выбранным парсером.
 */
 class ScanDirTask extends BaseTask
 {
 	/**
 	 * {@inheritdoc}
 	 */
 	public static function taskTitle()
 	{
 		return 'Локальная директория';
 	}

 	/**
 	 * {@inheritdoc}
 	 */
 	public function run()
 	{
 		$categoryIDs = $this->owner->categoryIDs;

 		$directory = $this->owner->source;

 		$transport = $this->owner->getBehavior( 'transportBehavior' );
 		$parser = $this->owner->getBehavior( 'parserBehavior' );

 		/* @var int $countGrabbed */
 		$countGrabbed = 0;
 		/* @var array $records */
 		$records = [];

 		try {
 			
 			$files = $transport->getFiles( $directory );
 			
 			foreach ( $files as $file ) {

 				try {
 					$content = $transport->getContent( $file );
 					
 					$record = $parser->parse( $content );
 					$recordModel = $this->saveRecord( $record, $categoryIDs );
 					$records[] = array_merge( $record, [ 'id' => $recordModel->record_id, 'link' => $recordModel->makeLink() ] );
 					$countGrabbed++;

 					@unlink( $file );

 				} catch( NotAccessibleException $e ) {
 					/**
 					 * Нет файла или его нельзя прочитать
 					 */
 					if ( $this->mailOnErrors ) {
 						$params = [
 							'taskTitle' => $this->owner->task->title,
 							'source' => $this->owner->source,
 							'file' => $file,
 						];

 						Yii::$app->mailer->compose( 'errorFileAccessibleTask', $params )
 						->setTo( Yii::$app->params[ 'adminEmail' ] )
 						->setFrom( Yii::$app->params[ 'senderEmail' ] )
 						->setSubject( $this->errorSubject )
 						->send();
 					}
 				} catch( ContentNotFoundException $e ) {
 					/**
 					 * Возможно неподдерживаемый формат
 					 */
 					if ( $this->mailOnErrors ) {
 						$params = [
 							'taskTitle' => $this->owner->task->title,
 							'source' => $this->owner->source,
 							'file' => $file,
 							'parserTitle' => $this->owner->parser->title,
 						];

 						Yii::$app->mailer->compose( 'errorFileContentTask', $params )
 						->setTo( Yii::$app->params[ 'adminEmail' ] )
 						->setFrom( Yii::$app->params[ 'senderEmail' ] )
 						->setSubject( $this->errorSubject )
 						->send();
 					}
 				} catch( RecordModelException $e ) {
 					/**
 					 * Ошибка сохранения
 					 */
 					if ( $this->mailOnErrors ) {
 						$params = [
 							'taskTitle' => $this->owner->task->title,
 							'source' => $this->owner->source,
 							'errors' => $e->model->errors,
 						];

 						Yii::$app->mailer->compose( 'errorModelTask', $params )
 						->setTo( Yii::$app->params[ 'adminEmail' ] )
 						->setFrom( Yii::$app->params[ 'senderEmail' ] )
 						->setSubject( $this->errorSubject )
 						->send();
 					}
 				}
 			}//end foreach

 		} catch( DirNotFoundException $e ) {
 			/**
 			 * Директория не найдена или нельзя прочитать файлы
 			 */
 			if ( $this->mailOnErrors ) {
 				$params = [
 					'taskTitle' => $this->owner->task->title,
 					'source' => $this->owner->source,
 					'directory' => $directory,
 				];

 				Yii::$app->mailer->compose( 'errorFilesTask', $params )
 				->setTo( Yii::$app->params[ 'adminEmail' ] )
 				->setFrom( Yii::$app->params[ 'senderEmail' ] )
 				->setSubject( $this->errorSubject )
 				->send();
 			}
 		}

 		$this->afterGrabbing( $records, $countGrabbed );
 	}

 	/**
 	 * {inheritdoc}
 	 */
 	public function getTransports()
 	{
 		$transports = \app\modules\grabber\models\Transports::find()->all();
 		$ret = [];

 		foreach ( $transports as $transport ) {
 			$conf = [
 				'class' => $transport->class,
 			];

 			$obj = Yii::createObject( $conf );

 			/**
 			 * Для работы этого типа задач необходимо двигаться по страницам
 			 */
 			if ( $obj instanceof \app\modules\grabber\interfaces\GetFilesInterface ) {
 				$ret[] = $transport;
 			}
 		}

 		return $ret;
 	}
 }
