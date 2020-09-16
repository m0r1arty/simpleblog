<?php

/**
 * Файл содержит CategoriesFilterEvent
 * @author M0r1arty <m0r1arty.nv@yandex.ru>
 */

namespace app\modules\blog\components;

use Yii;

use yii\base\Event;

/**
 * Класс CategoriesFilterEvent - событие, генерируемое \app\modules\blog\models\Records::getCategoriesForWidget
 */
class CategoriesFilterEvent extends Event
{
	/**
	 * @var array $categories массив категорий; каждая категория представлена ассоциативным массивом с ключами(id,title,link,status)
	 */
	public $categories = [];
}
