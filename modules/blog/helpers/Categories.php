<?php

/**
 * Файл содержит хелпер Categories.
 * @author M0r1arty <m0r1arty.nv@yandex.ru>
 */

namespace app\modules\blog\helpers;

use Yii;
use yii\helpers\Url;

use app\modules\blog\models\Categories as CategoriesModel;

/**
 * Класс Categories - хелпер, предназначенный для рутинных операций взаимодействия с моделью Categories.
 * На данный момент содержит только метод categoriesForWidget.
 */
class Categories
{
	/**
	 * Метод запрашивает у модели Categories список категорий для виджета \app\modules\blog\widgets\CategoriesWidget.
	 * Через параметры поддерживается запрос категорий для 2х режимов работы виджета:
	 * для режима "элемент управления" должна быть передана модель \app\modules\blog\models\Records $model
	 * для режима "статика" параметр $model должен быть null и указан параметр $category_id
	 * @param \app\modules\blog\models\Records|null $model если параметр null - нужен список категорий для отображения на frontend
	 * @param int $category_id если > 0 - данную категорию необходимо как-то выделить в шаблоне как текущую
	 * @return array возвращает массив категорий для виджета CategoriesWidget
	 */
	public static function categoriesForWidget( $model = null, $category_id = 0 )
	{
		if ( !is_null( $model ) && $model instanceof \app\modules\blog\components\RetriveCategoriesInterface ) {
			return $model->getCategoriesForWidget();
		} elseif( !is_null( $model ) ) {
			throw new \yii\base\InvalidArgumentException();
		} else {
			/* @var */
			$models = CategoriesModel::find()->orderBy( [ 'title' => 'asc' ] )->all();

			$categories = [];

			foreach ( $models as $category ) {
				$categories[] = [
					'title' => $category->title,
					'link' => Url::to( [ '/blog/blog/index', 'catid' => $category->category_id ] ),
					'status' => ( $category->category_id === $category_id ) ? 'a' : 'd',
				];
			}

			return $categories;
		}
	}
}
