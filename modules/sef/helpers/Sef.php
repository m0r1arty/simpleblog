<?php

/**
 * Файл содержит хелпер Sef.
 * @author M0r1arty <m0r1arty.nv@yandex.ru>
 */

namespace app\modules\sef\helpers;

use Yii;

use app\modules\sef\models\Sef as SefModel;
use app\modules\sef\models\BSefRoute;
use app\modules\sef\models\BSefParams;
use app\modules\sef\models\BSef;
use app\modules\sef\exceptions\InvalidRouteRegistrationException;
use app\modules\sef\exceptions\NotFoundRouteException;
use app\modules\sef\exceptions\RouteUnknownException;

/**
 * Хелпер Sef. Расширяется от BaseUrl чтобы иметь доступ к protected BaseUrl::normalizeRoute
 */
class Sef extends \yii\helpers\BaseUrl
{
	/**
	 * Метод retriveDataByPath принимает разделённый на массив url и возвращает ассоциативный массив, ключом в котором является строка из поля slug, а данными json_decoded поле params. Элементы массива объединяет общий предок - последний элемент переданного $path.
	 * @param string[] $path url вида [ 'a', 'b', 'c' ] для url '/a/b/c'
	 * @param int $level уровень вложенности в $path; для url '/a/b/c' можно начать с 'b'($level = 1), но при этом обязательно указывать корректный $parent_id;
	 * @param int $parent_id идентификатор предыдущего уровня
	 * @return bool|array[string] массив вида array[slug] => route; в случае если указанный path не существует - возвращается false
	 */
	public static function retriveDataByPath( $path, $level = 0, $parent_id = 1 )
	{
		if ( $level === count( $path ) ) {
			//собрать и вернуть данные с этого уровня
			$ret = [];

			$models = SefModel::find()->where( [ 'parent_id' => $parent_id ] );

			foreach ( $models->all() as $model ) {
				
				$params = json_decode( $model->params, true );

				if ( is_array( $params ) ) {
					$ret[ $model->slug ] = $params;
				}
			}
			return $ret;
		} else {
			//проверить существование ключа на этом уровне, если есть - рекурсивно вернуть retriveDataByPath, иначе - false

			$model = SefModel::find()->where( [ 'parent_id' => $parent_id, 'slug' => $path[ $level ] ] )->one();

			if ( is_null( $model ) ) {
				return false;
			} else {
				return self::retriveDataByPath( $path, $level + 1, $model->id );
			}
		}
	}

	/**
	 * Метод registerRoute регистрирует маршрут в модуле sef. Возвращает true в случае успешной регистрации. В случае неудачной регистрации в зависимости от параметра $throwExcept
	 * или выбрасывает ошибку, или возвращает false. Вызывать метод необходимо внутри транзакции.
	 * Пример регистрации маршрута:
	 * Sef::registerRoute( '/blog/blog/view', [ 'record_id' => 123, 'category_id' => 4 ], 'VeryCoolRecord', 5 )
	 * В случае успешной регистрации станет возможным прямой и обратный поиск(маршрута по url и url по маршруту).
	 * В примере выше $parent_id === 5 это идентификатор узла к которому идёт стыковка slug. Все узлы первого уровня должны стыковаться к $parent_id === 1(корень, регистрируемый в миграции).
	 * Узел с идентификатором = 5 в примере - это категория 'news', чей parent_id = 1.
	 * Тогда при запросе url /news/VeryCoolRecord будет найден маршрут [ 'blog/blog/view', 'record_id' => 123, 'category_id' => 4 ].
	 * И обратно при маршрута будет найден url /news/VeryCoolRecord.
	 * При этом нормализуется $route используя protected метод normalizeRoute унаследованного BaseUrl.
	 * @param string $route маршрут, например '/blog/blog/view'
	 * @param array[string] $params массив параметров для $route, например [ 'record_id' => 123, 'category_id' => 4 ]
	 * @param string $slug имя стыкуемого листа
	 * @param int $parent_id идентификатор узла, к которому стыкуется элемент
	 * @param bool $throwExcept true если нужно генерировать exception InvalidRouteRegistrationException вместо возврата false
	 * @return bool если удалось зарегистрировать маршрут - true, если нет и $throwExcept === false  - false
	 * @throws InvalidRouteRegistrationException если не удалось зарегистрировать маршрут и $throwExcept === true
	 */
	public static function registerRoute( $route, $params, $slug, $parent_id, $throwExcept = true )
	{
		$route = self::normalizeRoute( $route );

		/* @var \app\modules\sef\helpers\Route */
		$hRoute = new Route( $route, $params );

		/* @var bool $ret */
		$ret = $hRoute->register( $slug, $parent_id, $throwExcept );

		if ( $ret ) {
			/**
			 * По данному $cacheKey должен находится ассоциативный массив вида array[slug] = [$route,$params].
			 * Чтобы регистрируемый маршрут заработал данный кеш надо удалять.
			 */
			Yii::$app->cache->delete( $hRoute->sefKey() );
			/**
			 * Кеш для поиска url по маршруту
			 */
			Yii::$app->cache->set( $hRoute->bsefKey(), [ 'pid' => $parent_id, 'url' => $hRoute->url() ] );
		}

		return $ret;
	}

	/**
	 * Метод deleteRoute удаляет регистрацию маршрута в модуле sef.
	 * @return bool true - в случае успешного удаления регистрации, false в противном случае
	 */
	public static function deleteRoute( $route, $params, $throwExcept = true )
	{
		$route = self::normalizeRoute( $route );
		/* @var \app\modules\sef\helpers\Route */
		$hRoute = new Route( $route, $params );

		if ( !$hRoute->load() ) {
			if ( $throwExcept ) {
				throw new NotFoundRouteException();
			} else {
				return false;
			}
		}

		return $hRoute->unregister( $throwExcept );
	}

	/**
	 * Метод getUrlByRoute пытается найти url по переданной паре [route,params].
	 * @param string $route
	 * @param array[string] $params
	 * @return bool|string в случае успешного поиска возвращается url; false - в случае если url не найден
	 */
	public static function getUrlByRoute( $route, $params )
	{
		/**
		 * Сначала поиск в кеше.
		 */

		/* @var \app\modules\sef\helpers\Route */
		$hRoute = new Route( $route, $params );

		/* @var mixed $data */
		$data = $hRoute->urlFromCache();
		
		if ( $data !== false ) {

			if ( is_array( $data ) && isset( $data[ 'url' ] ) ) {
				return $data[ 'url' ];
			}
		}

		/**
		 * Если в кеше ничего нет.
		 */

		if ( !$hRoute->load() ) {
			return false;
		}

		/* @var string $url */
		$url = $hRoute->url();
		
		Yii::$app->cache->set( $hRoute->bsefKey(), [ 'pid' => $hRoute->parent_id(), 'url' => $url ] );

		return $url;
	}

	/**
	 * Метод moveSlug позволяет изменить slug и/или передать его другому родительскому узлу.
	 * @param string $route маршрут
	 * @param array[string] $params параметры маршрута
	 * @param string $slug новый slug маршрута
	 * @param int $parent_id идентификатор нового потомка для маршрута
	 * @param bool $throwExcept true - если нужно генерировать исключения, вместо возврата false
	 * @return bool true - если переименование или смена потомка увенчалась успехом
	 * @throws NotFoundRouteException если не удалось загрузить переданный маршрут
	 * @throws \yii\base\InvalidArgumentException если узел с таким slug существует среди потомков родительского sef узла
	 * @throws RouteUnknownException Route::moveSlug выбрасывает если не удалось получить экземпляр SefModel или не получилось сохранить изменения в экземпляре
	 */
	public static function moveSlug( $route, $params, $slug, $parent_id, $throwExcept = true )
	{
		$route = self::normalizeRoute( $route );

		/* @var \app\modules\sef\helpers\Route */
		$hRoute = new Route( $route, $params );

		if ( !$hRoute->load() ) {
			if ( $throwExcept ) {
				throw new NotFoundRouteException();
			} else {
				return false;
			}
		}

		if ( self::slugExists( $slug, $parent_id ) ) {
			if ( $throwExcept ) {
				throw new \yii\base\InvalidArgumentException();
			} else {
				return false;
			}
		}

		return $hRoute->moveSlug( $slug, $parent_id, $throwExcept );
	}

	/**
	 * Метод slugExists проверяет существует ли у заданного узла потомок с указанным slug
	 * @param string $slug проверяемый slug
	 * @param int $parent_id идентификатор узла в sef дереве
	 * @return bool true - если такой slug существует в sef дереве с идентификатором $parent_id
	 */
	public static function slugExists( $slug, $parent_id )
	{
		/* @var SefModel $model */
		$model = SefModel::find()->where( [ 'parent_id' => $parent_id, 'slug' => $slug ] )->one();

		return !is_null( $model );
	}

	/**
	 * Метод routeInstance инстанцирует экземпляр хелпера Route.
	 * @param string $route маршрут
	 * @param array[string] $params параметры маршрута
	 * @return Route экземпляр хелпера
	 */
	public static function routeInstance( $route, $params )
	{
		$route = self::normalizeRoute( $route );
		/* @var \app\modules\sef\helpers\Route */
		$hRoute = new Route( $route, $params );

		return $hRoute;
	}

	/**
	 * @param string $slug slug по которому производят поиск
	 * @param int $parent_id идентификатор узла в sef дереве
	 * @return Route|null прогруженный данными экземпляр Route если пара slug/parent_id существует; null - если такой пары нет
	 */
	public static function routeInstanceBySlugParentId( $slug, $parent_id )
	{
		/* @var SefModel $model */
		$model = SefModel::find()->where( [ 'parent_id' => $parent_id, 'slug' => $slug ] )->one();

		if ( is_null( $model ) ) {
			return null;
		}

		/* @var array $params */
		$params = json_decode( $model->params, true );
		$route = $params[ 0 ];

		/* @var Route $hRoute */
		$hRoute = new Route( $route, $params );

		return $hRoute;
	}
}
