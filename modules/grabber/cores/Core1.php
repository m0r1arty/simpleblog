<?php

/**
 */
namespace app\modules\grabber\cores;

use Yii;

use yii\base\Component;

use \app\modules\grabber\models\TaskInstances;

/**
 */
 class Core1 extends Component implements \app\modules\grabber\interfaces\CoreInterface
 {
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
 			'owner' => $task,
 		];
 		$task->attachBehavior( 'taskBehavior', $config );

 		$config = [
 			'class' => $task->transport->class,
 			'owner' => $task,
 		];
 		$task->attachBehavior( 'transportBehavior', $config );

 		$config = [
 			'class' => $task->parser->class,
 			'owner' => $task,
 		];
 		$task->attachBehavior( 'parserBehavior', $config );

 		Yii::configure( $task, $this->taskConfig );

 		$task->run();
 	}
 }
