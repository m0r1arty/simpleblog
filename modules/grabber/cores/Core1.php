<?php

/**
 * Файл содержит ядро(класс Core1) выполняемой задачи.
 * @author M0r1arty <m0r1arty.nv@yandex.ru>
 */
namespace app\modules\grabber\cores;

use Yii;

use yii\base\Component;

use \app\modules\grabber\models\TaskInstances;

/**
 * Класс Core1 является ядром запускающим(инстанцируя и конфигурируя) задачи.
 */
 class Core1 extends Component implements \app\modules\grabber\interfaces\CoreInterface
 {
 	/**
 	 * array[string] $taskConfig конфигурация каждой задачи
 	 */
 	public $taskConfig = [];

 	/**
 	 * {@inheritdoc}
 	 */
 	public function run( $task )
 	{
 		$this->runTask( $task );
 	}

 	/**
 	 * {@inheritdoc}
 	 */
 	public function runAll()
 	{
 		foreach ( TaskInstances::find()->all() as $ti ) {
 			$this->runTask( $ti );
 		}
 	}

 	/**
 	 * Метод выполняет переданную задачу.
 	 * @param \app\modules\grabber\models\TaskInstances $task задача, которую нужно выполнить
 	 */
 	protected function runTask( $task )
 	{
 		$config = [
 			'class' => $task->task->class,
 		];
 		$task->attachBehavior( 'taskBehavior', $config );

 		$config = [
 			'class' => $task->transport->class,
 		];
 		$task->attachBehavior( 'transportBehavior', $config );

 		$config = [
 			'class' => $task->parser->class,
 		];
 		$task->attachBehavior( 'parserBehavior', $config );

 		Yii::configure( $task, $this->taskConfig );

 		$task->run();
 	}
 }
