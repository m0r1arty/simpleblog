<?php

/**
 * Файл содержит задачу RunAllJob для расширения queue
 * @author M0r1arty <m0r1arty.nv@yandex.ru>
 */
namespace app\modules\grabber\jobs;

use Yii;

use app\modules\grabber\base\BaseJob;
use app\modules\grabber\models\TaskInstances;

/**
 * Задача RunAllJob запускает полный процесс граббинга для всех зарегистрированных задач.
 */
 class RunAllJob extends BaseJob implements \yii\queue\JobInterface
 {
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

 		$this->core->runAll();
 	}
 }
