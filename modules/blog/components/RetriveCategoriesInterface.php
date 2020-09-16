<?php

/**
 * Файл содержит интерфейс RetriveCategoriesInterface
 * @author M0r1arty <m0r1arty.nv@yandex.ru>
 */
namespace app\modules\blog\components;

use Yii;

/**
 * Интерфейс RetriveCategoriesInterface должен быть реализован моделяеми, для которых запускается виджет \app\modules\blog\widget\CategoriesWidget.
 */
interface RetriveCategoriesInterface
{
    /**
     * Формирует и возвращает массив категорий(ассоциативный массив).
     * В случае, если текущая запись связана с какой-то категорией - она выделяется статусом 'a' - active.
     * @see [[app\modules\blog\widgets\CategoriesWidget::categories]]
     * @return array[] массив элементами которого являются ассоциативные массивы
     */
	public function getCategoriesForWidget();
}
