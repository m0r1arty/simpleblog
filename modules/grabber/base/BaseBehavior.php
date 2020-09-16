<?php

/**
 * Файл содержит класс BaseBehavior - базу для других базовых классов(задача,парсер,транспорт)
 * @author M0r1arty <m0r1arty.nv@yandex.ru>
 */
namespace app\modules\grabber\base;

use Yii;

use yii\base\Behavior;

/**
 * Класс BaseBehavior. Расширяется классами BaseTask, BaseParser, BaseTransport.
 */
class BaseBehavior extends Behavior
{
	public $owner;
}
