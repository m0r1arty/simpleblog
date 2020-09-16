<?php

/**
 * Файл содержит базовый класс для парсеров BaseParser
 * @author M0r1arty <m0r1arty.nv@yandex.ru>
 */
namespace app\modules\grabber\base;

use Yii;

/**
 * BaseParser - базовый класс для парсеров.
 */
abstract class BaseParser extends BaseBehavior
{
	/**
	 * Возвращает название парсера, зарегистрированное в таблице {{%parsers}}
	 * @return string тайтл парсера
	 */
	abstract public static function parserTitle();
	/**
	 * Парсит контент известным ему способом
	 * @return array[string] ассоциативный массив с элементами title,preview,content
	 */
	abstract public function parse( $data );
}
