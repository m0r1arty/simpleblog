<?php

/**
 * Файл содержит интерфейс CoreInterface
 * @author M0r1arty <m0r1arty.nv@yandex.ru>
 */
namespace app\modules\grabber\interfaces;

/**
 * Интерфейс CoreInterface должо реализовывать каждое ядро задачи.
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
