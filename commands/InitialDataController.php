<?php

/**
 * Файл содержит консольную команду 
 * @author M0r1arty <m0r1arty.nv@yandex.ru>
 */
namespace app\commands;

use Yii;
use yii\helpers\Console;
use yii\console\Controller;
use yii\console\ExitCode;

use app\modules\blog\models\Categories;
use app\modules\blog\models\Records;

use app\modules\grabber\models\Tasks;
use app\modules\grabber\models\Parsers;
use app\modules\grabber\models\Transports;
use app\modules\grabber\models\TaskInstances;

/**
 * Установка начальных данных
 */
class InitialDataController extends Controller
{
 	/**
 	 * Метод установит начальные данны
 	 */
 	public function actionInstall()
 	{
 		$path = Yii::getAlias( '@app/runtime/grabber' );
 		$pathXml = $path . '/xml';
 		$pathJson = $path . '/json';

 		$this->stdout( "Создание директорий\n", Console::FG_BLUE );

 		if ( !is_dir( $path ) ) {
 			@mkdir( $path, 0777, true );

 			if ( is_dir( $path ) ) {
 				$this->stdout( "{$path} ", Console::FG_GREEN );
 				$this->stdout( "директория создана\n" );
 			} else {
 				$this->stderr( "{$path} ", Console::FG_RED );
 				$this->stderr( "директория не создана\n" );
 				return ExitCode::OSERR;
 			}
 		}

 		if ( !is_dir( $pathXml ) ) {
 			mkdir( $pathXml, 0777, true );

 			if ( is_dir( $pathXml ) ) {
 				$this->stdout( "{$pathXml} ", Console::FG_GREEN );
 				$this->stdout( "директория создана\n" );
 			} else {
 				$this->stderr( "{$pathXml} ", Console::FG_RED );
 				$this->stderr( "директория не создана\n" );
 				return ExitCode::OSERR;
 			}

 			copy( Yii::getAlias( '@app/commands/src/file.xml' ), $pathXml . '/file.xml' );
 		}

 		if ( !is_dir( $pathJson ) ) {
 			mkdir( $pathJson, 0777, true );

 			if ( is_dir( $pathJson ) ) {
 				$this->stdout( "{$pathJson} ", Console::FG_GREEN );
 				$this->stdout( "директория создана\n" );
 			} else {
 				$this->stderr( "{$pathJson} ", Console::FG_RED );
 				$this->stderr( "директория не создана\n" );
 				return ExitCode::OSERR;
 			}
 			copy( Yii::getAlias( '@app/commands/src/file.json' ), $pathJson . '/file.json' );
 		}

 		/**
 		 * Устанавливаем категории
 		 */

 		$this->stdout( "Установка категорий\n", Console::FG_BLUE );

 		$categoryIDs = [];

 		$model = new Categories();

 		$model->title = 'Первая категория';
 		
 		if ( $model->save() ) {
 			$this->stdout( "{$model->title}", Console::FG_GREEN );
 			$this->stdout( " установлена\n" );
 		}

 		$categoryIDs[] = $model->category_id;

 		$model = new Categories();

 		$model->title = 'Вторая категория';
 		
 		if ( $model->save() ) {
 			$this->stdout( "{$model->title}", Console::FG_GREEN );
 			$this->stdout( " установлена\n" );
 		}

 		$model = new Categories();

 		$model->title = 'Третья категория';
 		
 		if ( $model->save() ) {
 			$this->stdout( "{$model->title}", Console::FG_GREEN );
 			$this->stdout( " установлена\n" );
 		}

 		$categoryIDs[] = $model->category_id;

 		/**
 		 * $categoryIDs собирал идентификаторы категорий(первой,третьей) на которые будет мапить.
 		 */

 		$this->stdout( "Добавление записи\n", Console::FG_BLUE );

 		$model = new Records();

 		$model->title = 'Это самый тестовый тайтл';
 		$model->preview = '<p>Это самое что ни на есть тестовое превью</p>';
 		$model->content = '<p>Это самый тестовый контент</p><p>Причём из целых 2х параграфов.</p>';
 		$model->user_id = 1;//исходим из того, что пользователь уже зарегистрирован

 		$model->categoryIDs = implode( ',', $categoryIDs );

 		$model->detachBehavior( 'userBehavior' );

 		if ( $model->save() ) {
 			$this->stdout( "Запись {$model->title}", Console::FG_GREEN );
 			$this->stdout( " добавлена\n" );
 		}

 		/**
 		 * Добавим задачи
 		 */
 		$this->stdout( "Добавление задач\n", Console::FG_BLUE );

 		$model = Tasks::find()->where( [ 'class' => '\app\modules\grabber\tasks\HttpTask' ] )->one();

 		$httpTaskId = $model->task_id;

 		$model = Parsers::find()->where( [ 'class' => '\app\modules\grabber\parsers\ChelseaBluesParser' ] )->one();
 		$httpParserId = $model->parser_id;

 		$model = Transports::find()->where( [ 'class' => '\app\modules\grabber\transports\HttpTransport' ] )->one();
 		$httpTransportId = $model->transport_id;

 		$model = new TaskInstances();

 		$model->categoryIDs = implode( ',', $categoryIDs );
 		$model->source = 'https://chelseablues.ru';
 		$model->task_id = $httpTaskId;
 		$model->parser_id = $httpParserId;
 		$model->transport_id = $httpTransportId;

 		if ( $model->save() ) {
 			$this->stdout( 'Задача ' );
 			$this->stdout( "{$model->task->title}({$model->source})", Console::FG_GREEN );
 			$this->stdout( " добавлена\n" );
 		}

 		$model = Tasks::find()->where( [ 'class' => '\app\modules\grabber\tasks\ScanDirTask' ] )->one();
 		$sdTaskId = $model->task_id;

 		$model = Parsers::find()->where( [ 'class' => '\app\modules\grabber\parsers\JsonParser' ] )->one();
 		$parserId = $model->parser_id;
 		
 		$model = Transports::find()->where( [ 'class' => '\app\modules\grabber\transports\DirTransport' ] )->one();
 		$transportId = $model->transport_id;

 		$model = new TaskInstances();

 		$model->categoryIDs = implode( ',', $categoryIDs );
 		$model->source = $pathJson;
 		$model->task_id = $sdTaskId;
 		$model->parser_id = $parserId;
 		$model->transport_id = $transportId;

 		if ( $model->save() ) {
 			$this->stdout( 'Задача ' );
 			$this->stdout( "{$model->task->title}({$model->source})", Console::FG_GREEN );
 			$this->stdout( " добавлена\n" );
 		}

 		$model = Parsers::find()->where( [ 'class' => '\app\modules\grabber\parsers\XmlParser' ] )->one();
 		$parserId = $model->parser_id;

 		$model = new TaskInstances();

 		$model->categoryIDs = implode( ',', $categoryIDs );
 		$model->source = $pathXml;
 		$model->task_id = $sdTaskId;
 		$model->parser_id = $parserId;
 		$model->transport_id = $transportId;

 		if ( $model->save() ) {
 			$this->stdout( 'Задача ' );
 			$this->stdout( "{$model->task->title}({$model->source})", Console::FG_GREEN );
 			$this->stdout( " добавлена\n" );
 		}
 	}
}
