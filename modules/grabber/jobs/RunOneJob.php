<?php

/**
 * Файл содержит задачу RunOneJob для расширения queue
 * @author M0r1arty <m0r1arty.nv@yandex.ru>
 */
namespace app\modules\grabber\jobs;

use Yii;

use app\modules\grabber\base\BaseJob;
use app\modules\grabber\models\TaskInstances;

/**
 * Задача RunOneJob получает через параметры идентификатор задачи, которую необходимо выполнить и выполняет через ядро.
 */
 class RunOneJob extends BaseJob implements \yii\queue\JobInterface
 {
 	/**
 	 * @var int $taskInstanceId идентификатор задачи
 	 */
 	public $taskInstanceId = 0;

 	public function execute( $queue )
 	{
 		$this->jobInit();

 		if ( is_null( $this->grabber ) ) {
 			Yii::error( 'Grabber module not found' );
 			return;
 		}

 		if ( is_null( $this->core ) ) {
 			Yii::error( 'Core not instanced' );
 			return;
 		}

 		/* @var app\modules\grabber\models\TaskInstances $model */
 		$model = TaskInstances::findOne( $this->taskInstanceId );


 		if ( is_null( $model ) ) {
 			Yii::error( "TaskInstance not found, id: {$this->taskInstanceId}" );
 			return;
 		}

 		$this->core->run( $model );
 	}
 }
