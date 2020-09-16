<?php

/**
 * Файл содержит поведение CategoriesFilterBehavior
 * @author M0r1arty <m0r1arty.nv@yandex.ru>
 */
namespace app\modules\blog\behaviors;

use Yii;

use yii\base\Behavior;

use app\modules\blog\models\Records;

use app\modules\sef\helpers\Sef;
use app\modules\sef\helpers\Route;
use app\modules\sef\exceptions\NotFoundRouteException;

/**
 * Поведение CategoriesFilterBehavior используется при вызове \app\modules\blog\models\Records::getCategoriesForWidget, когда необходимо получить список категорий для
 * виджета \app\modules\blog\widgets\CategoriesWidget.
 */
class CategoriesFilterBehavior extends Behavior
{
	/**
	 * {@inheritdoc}
	 */
	public function events()
	{
		return [
			Records::EVENT_AFTER_CATEGORIES_FOR_WIDGET_FIND => 'afterCategoriesForWidgetFind',
		];
	}

	/**
	 * Метод afterCategoriesForWidgetFind реагирует на событие EVENT_AFTER_CATEGORIES_FOR_WIDGET_FIND, посылаемое \app\modules\blog\models\Records.
	 * У Records должен быть прописан slug. Если slug совпадает с одним из slug`ов категорий, то категория становится недоступной для выбора(красная рамка при выборе).
	 * Если категория уже выбрана(когда пользователь меняет slug при редактировании записи) - она будет выделена красным в виджете.
	 * @param \app\modules\blog\components\CategoriesFilterEvent $event
	 */
	public function afterCategoriesForWidgetFind( $event )
	{
		if ( $event->sender->isNewRecord ) {
			return;
		}

		/* @var Route $hRoute */
		$hRoute = null;
		/* @var Route $hRoute2 */
		$hRoute2 = null;
		/* @var \app\modules\blog\models\Records $model */
		$model = $event->sender;

		foreach ( $event->categories as $key => $category ) {

			/**
			 * Маршрут для категории для получения идентификатора узла в sef дереве.
			 */
			$hRoute = Sef::routeInstance( '/blog/blog/index', [ 'catid' => intval( $category[ 'id' ] ) ] );

			if ( !$hRoute->load() ) {
				throw new NotFoundRouteException();
			}

			/**
			 * Получаем маршрут через slug записи и ид узла.
			 */
			$hRoute2 = Sef::routeInstanceBySlugParentId( $model->slug, $hRoute->sef_id() );

			/**
			 * Если маршрут существует - это может быть проблемой.
			 */
			if( !is_null( $hRoute2 ) ) {
				if( !$hRoute2->load() ) {
					throw new NotFoundRouteException();
				}

				/* @var array params */
				$params = $hRoute2->params();

				/**
				 * Проблема это только в том случае, если record_id не совпадает.
				 */
				if ( intval( $params[ 'id' ] ) !== intval( $model->record_id ) ) {
					if ( $category[ 'status' ] === 'd' ) {
						//красная рамка у категории
						$event->categories[ $key ][ 'status' ] = 'e';
					} else {
						//красная категория
						$event->categories[ $key ][ 'status' ] = 'ae';
					}
				}
			}//if is_null
		}//foreach
	}
}
