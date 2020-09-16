<?php

/**
 * Файл содержит базовый класс для трансортов BaseTransport
 * @author M0r1arty <m0r1arty.nv@yandex.ru>
 */
namespace app\modules\grabber\base;

use Yii;

/**
 * BaseTransport - базовый класс для транспортов.
 */
abstract class BaseTransport extends BaseBehavior
{
	/**
	 * Возвращает название транспорта, зарегистрированное в таблице {{%transports}}
	 * @return string тайтл транспорта
	 */
	abstract public static function transportTitle();
	/**
	 * Метод getContent возвращает контент страницы, содержимое файла или всего того, что придумает разработчик базового класса.
	 * @return string контент из источника
	 */
	abstract public function getContent( $file );
}
