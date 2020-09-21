<?php

/**
 * Файл содержит задачу HttpTask
 * @author M0r1arty <m0r1arty.nv@yandex.ru>
 */
namespace app\modules\grabber\tasks;

use Yii;

use app\modules\grabber\base\BaseTask;

use app\modules\grabber\exceptions\LinksNotFoundException;
use app\modules\grabber\exceptions\ContentNotFoundException;
use app\modules\grabber\exceptions\RecordModelException;

/**
 * Класс HttpTask реализует задачу запроса данных с web страницы и обработку их выбранным парсером.
 */
 class HttpTask extends BaseTask
 {
 	/**
 	 * {@inheritdoc}
 	 */
 	public static function taskTitle()
 	{
 		return 'HTTP источник';
 	}

 	/**
 	 * {@inheritdoc}
 	 */
 	public function run()
 	{
 		$categoryIDs = $this->owner->categoryIDs;

 		/**
 		 * Точка сохранения
 		 */
 		$stopId = $this->owner->getParam( 'id' );
 		/**
 		 * Новая точка сохранения
 		 */
 		$saveId = null;

 		$stop = false;

 		$linkSource = $this->owner->source;

 		$transport = $this->owner->getBehavior( 'transportBehavior' );
 		$parser = $this->owner->getBehavior( 'parserBehavior' );

 		/* @var int $countGrabbed */
 		$countGrabbed = 0;
 		/* @var array $records */
 		$records = [];

 		while( true )
 		{
 			$page = $transport->getContent( $linkSource );

 			/**
 			 * Ссылка на след. страницу
 			 */
 			$linkSource = $parser->getNextSource( $page );

 			try {
 				/**
 				 * @var string[] $contentLinks массив ссылок на страницы с контентом вида id => link
 				 */
 				$contentLinks = $parser->getLinks( $page );
 			} catch ( LinksNotFoundException $e ) {
 				/**
 				 * Тут можно сообщить администратору, что возможно вёрстка на сайте изменилась
 				 */
 				if ( $this->mailOnErrors ) {
 					$params = [
 						'taskTitle' => $this->owner->task->title,
 						'source' => $this->owner->source,
 					];
 						
 					Yii::$app->mailer->compose( 'errorLinksTask', $params )
 					->setTo( Yii::$app->params[ 'adminEmail' ] )
 					->setFrom( Yii::$app->params[ 'senderEmail' ] )
 					->setSubject( $this->errorSubject )
 					->send();
 				}

 				return;
 			}

 			foreach ( $contentLinks as $id => $link ) {

 				/**
 				 * Если дошли до точки сохранения или сохранили максимум возможных при первом запуске записей
 				 */
 				if ( 
 					( !is_null( $stopId ) && $id <= $stopId ) ||
 					( is_null( $stopId ) && $countGrabbed === $this->maxRecords )
 				) {
 					/**
 					 */
 					$stop = true;
 					break;
 				}

 				if ( is_null( $saveId ) ) {
 					$saveId = $id;
 				}

 				/**
 				 * Получить страницу с новостью
 				 */
 				$content = $transport->getContent( $link );

 				try {
 					/**
 					 * Получаем контент от парсера
 					 */
 					$record = $parser->parse( $content );
 					$recordModel = $this->saveRecord( $record, $categoryIDs );
 					$records[] = array_merge( $record, [ 'id' => $recordModel->record_id, 'link' => $recordModel->makeLink() ] );
 				} catch( ContentNotFoundException $e ) {
 					/**
 					 * Сообщить админу о кривой ссылке или смене вёрстки
 					 */
 					$stop = true;

 					if ( $this->mailOnErrors ) {
 						$params = [
 							'taskTitle' => $this->owner->task->title,
 							'source' => $this->owner->source,
 							'link' => $link,
 						];
 						
 						Yii::$app->mailer->compose( 'errorContentTask', $params )
 						->setTo( Yii::$app->params[ 'adminEmail' ] )
 						->setFrom( Yii::$app->params[ 'senderEmail' ] )
 						->setSubject( $this->errorSubject )
 						->send();
 					}
 					break;
 				} catch( RecordModelException $e ) {
 					$stop = true;

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

 					break;
 				}

 				$countGrabbed++;
 			}

 			if ( $stop ) {
 				break;
 			}

 			if ( $linkSource === false ) {
 				break;
 			}
 		}

 		/**
 		 * Точка сохранения
 		 */
 		if ( !is_null( $saveId ) && $saveId != $stopId ) {
 			$this->owner->setParam( 'id', $saveId );
 			$this->owner->save();
 		}

 		$this->afterGrabbing( $records, $countGrabbed );
 	}

 	/**
 	 * {@inheritdoc}
 	 */
 	public function getParsers()
 	{
 		$parsers = \app\modules\grabber\models\Parsers::find()->all();
 		$ret = [];

 		foreach ( $parsers as $parser ) {
 			$conf = [
 				'class' => $parser->class,
 			];

 			$obj = Yii::createObject( $conf );

 			/**
 			 * Для работы этого типа задач необходимо двигаться по страницам
 			 */
 			if ( $obj instanceof \app\modules\grabber\interfaces\GetLinksInterface && 
 			$obj instanceof \app\modules\grabber\interfaces\NextSourceInterface ) {
 				$ret[] = $parser;
 			}
 		}

 		return $ret;
 	}
 }
