<?php

/**
 */
namespace app\modules\grabber\interfaces;

/**
 */
interface CoreInterface
{
	/**
	 * Метод выполняет одну задачу.
	 * @param \app\modules\grabber\models\TaskInstances $task задача, которую надо выполнить
	 */
	public function run( $task );
	/**
	 * Метод выполняет все задачи
	 */
	public function runAll();
}
