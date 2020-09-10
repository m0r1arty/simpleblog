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
	 * Метод getPathByParentId конструирует путь вида [ 'a', 'b', 'c' ] для url '/a/b/c', принимая идентификатор элемента 'c' в таблице {{%sef}}
	 * @param int $sef_id идентификатор в таблице {{%sef}} для которого необходимо восстановить путь; для примера идентификатор 'c' для url '/a/b/c'
	 * @param string[] $path массив для рекурсии, накапливающий массив для возврата
	 * @return string[] массив вида [ 'a', 'b', 'c' ] для url вида '/a/b/c'
	 */
	public static function getPathByParentId( $sef_id, $path = [] )
	{
		/* @var SefModel $model */
		$model = SefModel::find()->where( [ 'id' => $sef_id ] )->one();

		if ( is_null( $model ) || $model->parent_id == 0 ) {
			return $path;
		} else {
			array_unshift( $path, $model->slug );
			return self::getPathByParentId( $model->parent_id, $path );
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
		/**
		 * $route не должен быть пустым
		 */
		$route = self::normalizeRoute( $route );

		if ( empty( $route ) ) {
			if ( $throwExcept ) {
				throw new InvalidRouteRegistrationException();
			} else {
				return false;
			}
		}

		/**
		 * $slug не должен существовать на этом уровне
		 * @var SefModel
		 */

		$model = SefModel::find()->where( [ 'parent_id' => $parent_id, 'slug' => $slug ] );

		if ( !is_null( $model->one() ) ) {
			if ( $throwExcept ) {
				throw new InvalidRouteRegistrationException();
			} else {
				return false;
			}
		}

		/**
		 * Регистрация листа в дереве url
		 */
		$model = new SefModel();
		$model->parent_id = $parent_id;
		$model->slug = $slug;
		$model->params = json_encode( array_merge( [ $route ], $params ) );

		if ( !$model->validate() || !$model->save() ) {
			if ( $throwExcept ) {
				throw new InvalidRouteRegistrationException();
			} else {
				return false;
			}
		}
		/* @var int $sef_id понадобится в таблице {{%bsef}} */
		$sef_id = $model->id;

		$path = self::getPathByParentId( $parent_id );

		/* @var string $cacheKey используется для сброса кеша */
		if ( empty( $path ) ) {
			$cacheKey = 'sefroot';
		} else {
			$cacheKey = 'sef:' . implode( ':', $path );
		}

		/**
		 * По данному $cacheKey должен находится ассоциативный массив вида array[slug] = [$route,$params].
		 * Чтобы регистрируемый маршрут заработал данный кеш надо удалять.
		 */
		Yii::$app->cache->delete( $cacheKey );

		/**
		 * Нет необходимости дублировать сам маршрут. Но если его нет - регистрируем в таблице {{%bsef_route}}
		 */

		/* BSefRoute $model */
		$model = BSefRoute::find()->where( [ 'crc' => crc32( $route ), 'route' => $route ] )->one();

		if ( is_null( $model ) ) {
			$model = new BSefRoute();
			$model->route = $route;
			$model->crc = crc32( $route );

			if ( !$model->validate() || !$model->save() ) {
				if ( $throwExcept ) {
					throw new InvalidRouteRegistrationException();
				} else {
					return false;
				}
			}
		}
		/* @var int $route_id понадобится в таблице {{%bsef}} */
		$route_id = $model->route_id;

		/**
		 * Параметры должны быть отсортированы по ключу, т.к. участвуют в формировании ключа кеша.
		 */
		ksort( $params );

		/* @var string[] $strParams */
		$strParams = [];

		foreach ( $params as $key => $val ) {
			$key = trim( $key );
			$val = trim( strval( $val ) );

			if ( !empty( $key ) && !empty( $val ) ) {
				$strParams[] = $key . '=' . $val;
			}
		}

		/**
		 * Таблица {{%bsef_params}} хранит параметры вида 'category_id=4'.
		 * Их тоже нет необходимости дублировать, поэтому сначала ищем и добавляем только если не находим.
		 */

		/* @var int[] $paramsIDs */
		
		$paramsIDs = [];

		foreach ( $strParams as $val ) {
			/* @var BSefParams $model */
			$model = BSefParams::find()->where( [ 'crc' => crc32( $val ), 'param' => $val ] )->one();

			if ( !is_null( $model ) ) {
				$paramsIDs[] = intval( $model->param_id );
			} else {
				$model = new BSefParams();
				$model->param = $val;
				$model->crc = crc32( $val );

				if ( !$model->validate() || !$model->save() ) {
					if( $throwExcept ) {
						throw new InvalidRouteRegistrationException();
					} else {
						return false;
					}
				}

				$paramsIDs[] = intval( $model->param_id );
			}
		}

		/**
		 * $paramIDs содержит идентификаторы параметров. Чтобы избежать дубляжа при регистрации необходимо проверить уникальность [route_id,crc,params].
		 * Для определённости $paramIDs должен быть отсортирован, а затем склеен разделителем.
		 */
		sort( $paramsIDs );
		/* @var string $strModelParams */
		$strModelParams = implode( ':', $paramsIDs );

		/* @var BSef $model */
		$model = BSef::find()->where( [ 'route_id' => $route_id, 'crc' => crc32( $strModelParams ), 'params' => $strModelParams ] )->one();

		if ( !is_null( $model ) ) {
			if ( $throwExcept ) {
				throw new InvalidRouteRegistrationException();
			} else {
				return false;
			}
		}

		/**
		 * Связка маршрут, параметры уникальн - можно совершать последние движения.
		 * Таблица {{%bsef}} нужна для: 1) ссылки на {{%sef}} при поиске url по [$route,$params] 2) уверенности в уникальности пары [$route,$params]
		 */

		$model = new BSef();
		$model->params = $strModelParams;
		$model->crc = crc32( $strModelParams );
		$model->route_id = $route_id;
		$model->sef_id = $sef_id;

		if ( !$model->validate() || !$model->save() ) {
			if ( $throwExcept ) {
				throw new InvalidRouteRegistrationException();
			} else {
				return false;
			}
		}

		/**
		 * Кеш для поиска url по маршруту
		 */

		/* @var string $cacheKey ключ для хранения parent_id и url */
		$cacheKey = 'bsef:' . $route . ':' . implode( ':', $strParams );
		Yii::$app->cache->set( $cacheKey, [ 'pid' => $parent_id, 'url' => '/' . implode( '/', $path ) . ( empty( $path ) ? '' : '/' ) . $slug ] );

		return true;
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
		$route = trim( $route );

		ksort( $params );

		/* @var string[] $strParams */
		$strParams = [];
		/* @var string[] $usedParams */
		$usedParams = [];
		/* @var string[] $unusedParams */
		$unusedParams = [];

		foreach ( $params as $key => $val ) {
			$key = trim( $key );
			$val = trim( strval( $val ) );
			
			$usedParams[] = $key;

			if ( !empty( $key ) && !empty( $val ) ) {
				$strParams[] = $key . '=' . $val;
			}
		}

		$cacheKey = 'bsef:' . $route . ':' . implode( ':', $strParams );

		$data = Yii::$app->cache->get( $cacheKey );

		if ( $data !== false ) {

			if ( is_array( $data ) && isset( $data[ 'url' ] ) ) {
				return $data[ 'url' ];
			}
		}

		/**
		 * Если в кеше ничего не найдено:
		 * 1. поиск route_id в {{bsef_route}} по crc и route
		 * 2. сбор идентификаторов параметров в таблице {{%bsef_params}} по crc и param; при этом дополнительные параметры(такие как page_num, например) отфильтровываются
		 * 3. поиск sef_id в {{%bsef}} по route_id, crc и params
		 * 4. поиск slug и parent_id в {{%sef}} по sef_id
		 * 5. конструирование url по найденному slug и self::getPathByParentId(parent_id)
		 * 6. добавление к url дополнительных параметров через http_build_query
		 * 7. сохранение url в кеше
		 */

		/* @var BSefRoute $model */
		$model = BSefRoute::find()->where( [ 'crc' => crc32( $route ), 'route' => $route ] )->one();

		if ( is_null( $model ) ) {
			return false;
		}

		/* @var int $route_id */
		$route_id = intval( $model->route_id );

		/* @var int[] $paramsIDs */
		$paramsIDs = [];

		/* @var int $i */
		$i = 0;
		/* @var $model */
		foreach ( $strParams as $val ) {
			$model = BSefParams::find()->where( [ 'crc' => crc32( $val ), 'param' => $val ] )->one();
			
			if ( !is_null( $model ) ) {
				$paramsIDs[] = intval( $model->param_id );
			} else {
				$unusedParams[] = $i;
			}

			$i++;
		}

		sort( $paramsIDs );

		$strModelParams = implode( ':', $paramsIDs );

		/* @var BSef $model */
		$model = BSef::find()->where( [ 'route_id' => $route_id, 'crc' => crc32( $strModelParams ), 'params' => $strModelParams ] )->one();

		if ( is_null( $model ) ) {
			return false;
		}

		$sef_id = intval( $model->sef_id );

		/* @var SefModel $model */
		$model = SefModel::find()->where( [ 'id' => $sef_id ] )->one();

		if ( is_null( $model ) ) {
			return false;
		}

		/* @var string[] $path */
		$path = self::getPathByParentId( $model->parent_id );

		$url = '/' . implode( '/', $path ) . ( ( empty( $path ) ) ? '' : '/' ) . $model->slug;
		
		$usedParams = array_flip( $usedParams );
		$remainParams = array_intersect( $unusedParams, $usedParams );
		$remainParams = array_filter( $usedParams, function ( $val ) use ( $remainParams ) {
			return in_array( $val, $remainParams );
		} );

		/* @var array $queryParams массив для QueryString */
		$queryParams = array_intersect_key( $params, $remainParams );
		/* @var string[] массив ключей параметров для хранения в кеше */
		$persistParams = array_keys( array_diff_key( $params, $queryParams ) );

		if ( !empty( $queryParams ) ) {
			$url .= '?' . http_build_query( $queryParams );
		}

		Yii::$app->cache->set( $cacheKey, [ 'pid' => $model->parent_id, 'url' => $url ] );

		return $url;
	}
}
