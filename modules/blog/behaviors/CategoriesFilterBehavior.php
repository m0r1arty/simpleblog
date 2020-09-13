<?php

/**
 */
namespace app\modules\blog\behaviors;

use Yii;

use yii\base\Behavior;

use app\modules\blog\models\Records;

use app\modules\sef\helpers\Sef;
use app\modules\sef\helpers\Route;
use app\modules\sef\exceptions\NotFoundRouteException;
/**
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
			//if ( $category[ 'status' ] === 'd' ) {
				
				$hRoute = Sef::routeInstance( '/blog/blog/index', [ 'catid' => intval( $category[ 'id' ] ) ] );
				
				if ( !$hRoute->load() ) {
					throw new NotFoundRouteException();
				}
				
				$hRoute2 = Sef::routeInstanceBySlugParentId( $model->slug, $hRoute->sef_id() );

				if( !is_null( $hRoute2 ) ) {
					if( !$hRoute2->load() ) {
						throw new NotFoundRouteException();
					}
					/* @var array params */
					$params = $hRoute2->params();

					if ( intval( $params[ 'id' ] ) !== intval( $model->record_id ) ) {
						if ( $category[ 'status' ] === 'd' ) {
							$event->categories[ $key ][ 'status' ] = 'e';
						} else {
							$event->categories[ $key ][ 'status' ] = 'ae';
						}
					}
				}
			//}
		}
	}
}
