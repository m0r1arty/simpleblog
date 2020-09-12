<?php

/**
 * Файл содержит хелпер Route для хелпера Sef
 * @author M0r1arty <m0r1arty.nv@yandex.ru>
 */

namespace app\modules\sef\helpers;

use Yii;
use yii\base\InvalidConfigException;

use app\modules\sef\models\Sef;
use app\modules\sef\models\BSefRoute;
use app\modules\sef\models\BSefParams;
use app\modules\sef\models\BSef;
use app\modules\sef\exceptions\InvalidRouteRegistrationException;
use app\modules\sef\exceptions\InvalidRouteUnregistrationException;
use app\modules\sef\exceptions\NotFoundRouteException;
use app\modules\sef\exceptions\RouteUnknownException;

/**
 * Класс Route нужен, чтобы выделить рутинные операции с таблицами {{%sef}}, {{%bsef}}, {{%bsef_route}}, {{%bsef_params}} из хелпера Sef.
 */
class Route
{
	/* @var string $_route Yii маршрут */
	private $_route = '';
	/* @var array[string] $_params параметры Yii маршрута */
	private $_params = [];
	/**
	 * @var array[string] $_paramIDs идентификаторы соответствующих параметров маршрута из таблицы {{%bsef_params}}
	 * Должны заполнятся при загрузке или регистрации
	 * Ключи соответствуют ключам в $_params
	 * В случае загрузки могут содержать меньше значений, чем в $_params, из-за дополнительных параметров(номер страницы, сортировка и т.п.)
	 */
	private $_paramIDs = [];
	/* @var array[string] $_remainParams дополнительные параметры(часть $_params), для которых нет соответствующих идентификаторов в $_paramIDs; при регистрации пустой */
	private $_remainParams = [];
	/* @var string $_sefCacheKey ключ для кеша, в котором хранится slug текущего маршрута */
	private $_sefCacheKey = '';
	/* @var string $_bsefCacheKey ключ для кеша, в котором хранятся parent_id и url текущего маршрута */
	private $_bsefCacheKey = '';
	/* @var string $_url соответствующий маршруту url */
	private $_url = '';
	/* @var string $_slug slug маршрута */
	private $_slug = '';
	/* @var int идентификатор slug`а в дереве */
	private $_sefId = 0;
	/* @var int идентификатор в таблице связки {{%bsef}} */
	private $_bsefId = 0;
	/* @var int $_parentId идентификатор родителя slug`а в дереве */
	private $_parentId = 0;
	/* @var int $_routeId идентификатор маршрута в таблице {{%bsef_route}} */
	private $_routeId = 0;
	/* @var string[] массив url [ 'a', 'b', 'c' ] для '/a/b/c' */
	private $_path = [];
	/* @var bool $_loaded true - если маршрут успешно загружен или зарегистрирован */
	private $_loaded = false;
	
	/**
	 * @param string $route маршрут
	 * @param array[string] параметры маршрута
	 */
	public function __construct( $route, $params )
	{
		$this->_route = trim( $route );
		
		/**
		 * Параметры должны быть отсортированы по ключу, т.к. участвуют в формировании ключа кеша.
		 */
		ksort( $params );
		foreach ( $params as $key => $val ) {
			$key = trim( $key );
			$val = trim( strval( $val ));

			$this->_params[ $key ] = $val;
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
		/* @var Sef $model */
		$model = Sef::find()->where( [ 'id' => $sef_id ] )->one();

		if ( is_null( $model ) || $model->parent_id == 0 ) {
			return $path;
		} else {
			array_unshift( $path, $model->slug );
			return self::getPathByParentId( $model->parent_id, $path );
		}
	}

	/**
	 * Метод register регистрирует маршрут в таблицах модуля sef.
	 * @param string $slug имя стыкуемого листа
	 * @param int $parent_id идентификатор узла, к которому стыкуется элемент
	 * @param bool $throwExcept true если нужно генерировать exception InvalidRouteRegistrationException вместо возврата false
	 * @return bool если удалось зарегистрировать маршрут - true, если нет и $throwExcept === false  - false
	 * @throws InvalidRouteRegistrationException если не удалось зарегистрировать маршрут и $throwExcept === true
	 */
	public function register( $slug, $parent_id, $throwExcept = true )
	{
		if ( empty( $this->_route ) ) {
			if ( $throwExcept ) {
				throw new InvalidRouteRegistrationException();
			} else {
				return false;
			}
		}

		$this->_slug = $slug;
		$this->_parentId = $parent_id;

		/**
		 * $slug не должен существовать на этом уровне
		 * @var Sef $model
		 */

		$model = Sef::find()->where( [ 'parent_id' => $parent_id, 'slug' => $slug ] );

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
		$model = new Sef();
		$model->parent_id = $parent_id;
		$model->slug = $slug;
		$model->params = json_encode( array_merge( [ $this->_route ], $this->_params ) );

		if ( !$model->validate() || !$model->save() ) {
			if ( $throwExcept ) {
				throw new InvalidRouteRegistrationException();
			} else {
				return false;
			}
		}
		/* @var int $sef_id понадобится в таблице {{%bsef}} */
		$this->_sefId = $sef_id = $model->id;

		$this->_path = $path = self::getPathByParentId( $parent_id );

		if ( empty( $path ) ) {
			$this->_sefCacheKey = 'sefroot';
		} else {
			$this->_sefCacheKey = 'sef:' . implode( ':', $path );
		}

		$this->loadRouteAndParams( true );

		/**
		 * $this->_paramIDs содержит идентификаторы параметров. Чтобы избежать дубляжа при регистрации необходимо проверить уникальность [route_id,crc,params].
		 * Для определённости $this->_paramIDs должен быть отсортирован, а затем склеен разделителем.
		 */
		/* @var string $strModelParams */
		$strModelParams = implode( ':', array_values( $this->_paramIDs ) );

		/* @var BSef $model */
		$model = BSef::find()->where( [ 'route_id' => $this->_routeId, 'crc' => crc32( $strModelParams ), 'params' => $strModelParams ] )->one();

		if ( !is_null( $model ) ) {
			if ( $throwExcept ) {
				throw new InvalidRouteRegistrationException();
			} else {
				return false;
			}
		}

		/**
		 * Связка маршрут, параметры уникальн - можно совершать последние движения.
		 * Таблица {{%bsef}} нужна для: 1) ссылки на {{%sef}} при поиске url по [route,params] 2) уверенности в уникальности пары [route,params]
		 */

		$model = new BSef();
		$model->params = $strModelParams;
		$model->crc = crc32( $strModelParams );
		$model->route_id = $this->_routeId;
		$model->sef_id = $this->_sefId;

		if ( !$model->validate() || !$model->save() ) {
			if ( $throwExcept ) {
				throw new InvalidRouteRegistrationException();
			} else {
				return false;
			}
		}

		$this->_bsefId = intval( $model->id );

		$this->_url = '/' . implode( '/', $path ) . ( empty( $path ) ? '' : '/' ) . $slug;

		$this->_loaded = true;

		return true;
	}

	/**
	 * Метод unregister удаляет регистрацию маршрута из таблиц модуля sef.
	 * Записи в {{%bsef_route}} и {{%bsef_params}} не удаляются.
	 * @param bool $throwExcept если true - генерирует исключение InvalidRouteUnregistrationException в случае неудачного удаления.
	 * @return bool true - если маршрут удалён; false - в случае неудачного удаления
	 * @throws InvalidConfigException при попытке выполнить метод на "пустом" объекте
	 * @throws InvalidRouteUnregistrationException при ошибке удаления маршрута
	 */
	public function unregister( $throwExcept = true )
	{
		if ( !$this->_loaded ) {
			throw new InvalidConfigException();
		}

		/* @var BSef $model */
		$model = BSef::find()->where( [ 'id' => $this->_bsefId ] )->one();
		/* @var bool $ret */
		$ret = $this->processUnregisterModel( $model, $throwExcept );

		if ( !$ret ) {
			return false;
		}

		/* @var BSef $model */
		$model = Sef::find()->where( [ 'id' => $this->_sefId ] )->one();
		/* @var bool $ret */
		$ret = $this->processUnregisterModel( $model, $throwExcept );

		return $ret;
	}

	/**
	 * Метод moveSlug позволяет переместить маршрут на другой узел дерева, а так же переименовать сам slug sef.
	 * @param string $newSlug новый slug
	 * @param int $parent_id новый идентификатор родительского узла
	 * @param bool $throwExcept если true - генерирует исключение InvalidRouteUnregistrationException в случае неудачной смены родителя/переименования.
	 * @return bool true - в случае успешного перемещения и/или переименования узла.
	 * @throws InvalidConfigException при попытке выполнить метод на "пустом" объекте
	 * @throws RouteUnknownException при неудачной попытке найти текущий узел для обновления slug и parent_id или при неудачном сохранении
	 */
	public function moveSlug( $newSlug, $parent_id, $throwExcept = true )
	{
		if ( !$this->_loaded ) {
			throw new InvalidConfigException();
		}

		$oldSefCacheKey = $this->_sefCacheKey;
		$oldParentId = $this->_parentId;
		$oldPath = $this->_path;
		$oldSlug = $this->_slug;
		$oldUrl = $this->_url;

		/* @var Sef $model */
		$model = Sef::findOne( $this->_sefId );

		if ( is_null( $model ) ) {
			if( $throwExcept ) {
				throw new RouteUnknownException();
			} else {
				return false;
			}
		}

		$model->parent_id = $parent_id;
		$model->slug = $newSlug;

		if ( !$model->save() ) {
			if ( $throwExcept ) {
				throw new RouteUnknownException();
			} else {
				return false;
			}
		}

		$this->_path = self::getPathByParentId( $parent_id );

		if ( empty( $this->_path ) ) {
			$this->_sefCacheKey = 'sefroot';
		} else {
			$this->_sefCacheKey = 'sef:' . implode( ':', $this->_path );
		}

		$this->_url = '/' . implode( '/', $this->_path ) . ( empty( $this->_path ) ? '' : '/' ) . $newSlug;

		Yii::$app->cache->delete( $oldSefCacheKey );

		if ( $oldSefCacheKey !== $this->_sefCacheKey ) {
			Yii::$app->cache->delete( $this->_sefCacheKey );
		}

		Yii::$app->cache->set( $this->_bsefCacheKey, [ 'pid' => $parent_id, 'url' => $this->_url ] );

		return true;
	}

	/**
	 * Метод load заполняет пустой объект информацией.
	 * @return bool true - в случае успешной загрузки объекта маршрута; false - в случае неудачи
	 */
	public function load()
	{
		if ( $this->_loaded ) {
			return true;
		}

		if ( !$this->loadRouteAndParams( false ) ) {
			return false;
		}

		$strModelParams = implode( ':', array_values( $this->_paramIDs ) );

		/* @var BSef $model */
		$model = BSef::find()->where( [ 'route_id' => $this->_routeId, 'crc' => crc32( $strModelParams ), 'params' => $strModelParams ] )->one();

		if ( is_null( $model ) ) {
			return false;
		}

		$this->_sefId = intval( $model->sef_id );
		$this->_bsefId = intval( $model->id );

		/* @var Sef $model */
		$model = Sef::find()->where( [ 'id' => $this->_sefId ] )->one();

		if ( is_null( $model ) ) {
			return false;
		}

		$this->_parentId = intval( $model->parent_id );
		$this->_slug = $model->slug;

		/* @var string[] $path */
		$this->_path = $path = self::getPathByParentId( $this->_parentId );

		if ( empty( $path ) ) {
			$this->_sefCacheKey = 'sefroot';
		} else {
			$this->_sefCacheKey = 'sef:' . implode( ':', $path );
		}

		$this->_url = '/' . implode( '/', $path ) . ( ( empty( $path ) ) ? '' : '/' ) . $model->slug;

		if ( !empty( $this->_remainParams ) ) {
			$this->_url .= '?' . http_build_query( $this->_remainParams );
		}

		$this->_loaded = true;

		return true;
	}

	/**
	 * Метод sefKey возвращает ключ кеша, в котором хранится коллекция slug`ов на данном уровне дерева.
	 * @return string ключ кеша
	 * @throws InvalidConfigException при попытке выполнить метод на "пустом" объекте
	 */
	public function sefKey()
	{
		if ( !$this->_loaded ) {
			throw new InvalidConfigException();
		}

		return $this->_sefCacheKey;
	}

	/**
	 * Метод bsefKey возвращает ключ кеша, в котором хранится url и parent_id текущего маршрута
	 * @return string ключ кеша
	 * @throws InvalidConfigException при попытке выполнить метод на "пустом" объекте
	 */
	public function bsefKey()
	{
		if ( !$this->_loaded ) {
			throw new InvalidConfigException();
		}

		return $this->_bsefCacheKey;
	}

	/**
	 * Метод url возвращает url :O
	 * @return string url для данного маршрута
	 * @throws InvalidConfigException при попытке выполнить метод на "пустом" объекте
	 */
	public function url()
	{
		if ( !$this->_loaded ) {
			throw new InvalidConfigException();
		}

		return $this->_url;
	}

	/**
	 * Метод parent_id возвращает идентификатор родительского узла в sef дереве
	 * @return int ид родительского узла
	 * @throws InvalidConfigException при попытке выполнить метод на "пустом" объекте
	 */
	public function parent_id()
	{
		if ( !$this->_loaded ) {
			throw new InvalidConfigException();
		}

		return $this->_parentId;
	}

	/**
	 * Метод route_id возвращает идентификатор маршрута
	 * @return int идентификатор маршрута из таблицы {{%bsef_route}}
	 * @throws InvalidConfigException при попытке выполнить метод на "пустом" объекте
	 */
	public function route_id()
	{
		if ( !$this->_loaded ) {
			throw new InvalidConfigException();
		}

		return $this->_routeId;
	}

	/**
	 * Метод sef_id возвращает идентификатор узла маршрута
	 * @return int идентификатор текущего узла sef дерева
	 * @throws InvalidConfigException при попытке выполнить метод на "пустом" объекте
	 */
	public function sef_id()
	{
		if ( !$this->_loaded ) {
			throw new InvalidConfigException();
		}

		return $this->_sefId;
	}

	/**
	 * Метод bsef_id возвращает идентификатор в таблице, связывающей маршрут, параметры и sef дерево.
	 * @return int идентификатор маршрута в таблице-связке {{%bsef}}
	 * @throws InvalidConfigException при попытке выполнить метод на "пустом" объекте
	 */
	public function bsef_id()
	{
		if ( !$this->_loaded ) {
			throw new InvalidConfigException();
		}

		return $this->_bsefId;
	}

	/**
	 * Метод slug возвращает slug маршрута
	 * @return string текущий slug маршрута
	 * @throws InvalidConfigException при попытке выполнить метод на "пустом" объекте
	 */
	public function slug()
	{
		if ( !$this->_loaded ) {
			throw new InvalidConfigException();
		}

		return $this->_slug;
	}

	/**
	 * Метод params возвращает параметры с которыми регистрировался маршрут, например [ 'cat_id' => 4 ]
	 * @return array[string] массив параметров маршрута с которыми он зарегистрирован
	 * @throws InvalidConfigException при попытке выполнить метод на "пустом" объекте
	 */
	public function params()
	{
		if ( !$this->_loaded ) {
			throw new InvalidConfigException();
		}
	}

	/**
	 * Метод paramIDs возвращает массив идентификаторов параметров с которыми регистрировался маршрут. Ключами в массиве служат имена параметров.
	 * @return array[string] массив в котором ключами являются ключи массива параметров с которым маршрут зарегистрирован, а значения - идентификаторы этих параметров в {{%bsef_params}}
	 * @throws InvalidConfigException при попытке выполнить метод на "пустом" объекте
	 */
	public function paramIDs()
	{
		if ( !$this->_loaded ) {
			throw new InvalidConfigException();
		}

		return $this->_paramIDs;
	}

	/**
	 * Метод remainParams возвращает параметры, которые не относятся к маршруту. Например, page_num=2, sort_field=title и т.п.
	 * @return array[string] массив дополнительных параметров
	 * @throws InvalidConfigException при попытке выполнить метод на "пустом" объекте
	 */
	public function remainParams()
	{
		if ( !$this->_loaded ) {
			throw new InvalidConfigException();
		}

		return $this->_remainParams;
	}

	/**
	 * Метод path возвращает массив родительских slug`ов до корня sef дерева.
	 * @return string[] массив из частей url. Например, [ 'a', 'b' ] - для slug`а 'c', родителем для которого является элемент 'b'.
	 * @throws InvalidConfigException при попытке выполнить метод на "пустом" объекте
	 */
	public function path()
	{
		if ( !$this->_loaded ) {
			throw new InvalidConfigException();
		}

		return $this->_path;
	}

	/**
	 * Метод urlFromCache позволяет получить данные кеша маршрута, не загружая маршрут. Т.е. предварительно вызывать метод load не нужно.
	 * @return mixed если нет кеша маршрута - false, иначе - данные из кеша.
	 */
	public function urlFromCache()
	{
		/* @var string[] $strParams */
		$strParams = [];

		foreach ( $this->_params as $key => $val ) {
			/* @var string $strParam */
			$strParam = $key . '=' . $val;
			$strParams[] = $strParam;
		}

		$cacheKey = 'bsef:' . $this->_route . ':' . implode( ':', $strParams );

		return Yii::$app->cache->get( $cacheKey );
	}

	/**
	 * Метод loadRouteAndParams предназначен для получения таких данных:
	 * идентификатор маршрута из таблицы {{%bsef_route}}
	 * массива идентификаторов параметров маршрута из таблицы {{%bsef_params}}
	 * массива дополнительных параметров маршрута
	 * ключа кеша, в котором хранится url и parent_id текущего маршрута
	 * @return bool true - в случае успешной загрузки или регистрации; иначе false
	 * @throws InvalidRouteRegistrationException если в случае регистрации не удалось зарегистрировать маршрут или параметры маршрута
	 */
	protected function loadRouteAndParams( $insert = false, $throwExcept = true )
	{
		/**
		 * Нет необходимости дублировать сам маршрут. Но если его нет, а $insert === true - регистрируем в таблице {{%bsef_route}}
		 */

		/* BSefRoute $model */
		$model = BSefRoute::find()->where( [ 'crc' => crc32( $this->_route ), 'route' => $this->_route ] )->one();

		if ( is_null( $model ) ) {

			if ( $insert ) {
				$model = new BSefRoute();
				$model->route = $this->_route;
				$model->crc = crc32( $this->_route );

				if ( !$model->validate() || !$model->save() ) {
					if ( $throwExcept ) {
						throw new InvalidRouteRegistrationException();
					} else {
						return false;
					}
				}
			} else {
				return false;
			}
		}

		$this->_routeId = $model->route_id;

		/* @var string[] $strParams */
		$strParams = [];
		/**
		 * Таблица {{%bsef_params}} хранит параметры вида 'category_id=4'.
		 * Их тоже нет необходимости дублировать, поэтому сначала ищем и добавляем только если не находим.
		 */

		foreach ( $this->_params as $key => $val ) {
			if ( !empty( $key ) && !empty( $val ) ) {
				/* @var string $strParam */
				$strParam = $key . '=' . $val;
				$strParams[] = $strParam;

				/* @var BSefParams $model */
				$model = BSefParams::find()->where( [ 'crc' => crc32( $strParam ), 'param' => $strParam ] )->one();

				if ( !is_null( $model ) ) {
					/* @var int $param_id */
					$param_id = intval( $model->param_id );
					$this->_paramIDs[ $key ] = $param_id;
				} else {

					if ( $insert ) {
						$model = new BSefParams();
						$model->param = $strParam;
						$model->crc = crc32( $strParam );

						if ( !$model->validate() || !$model->save() ) {
							if( $throwExcept ) {
								throw new InvalidRouteRegistrationException();
							} else {
								return false;
							}
						}

						/* @var int $param_id */
						$param_id = intval( $model->param_id );
						$this->_paramIDs[ $key ] = $param_id;
					} else {
						$this->_remainParams[ $key ] = $val;
					}
				}
			}
		}

		asort( $this->_paramIDs );

		$this->_bsefCacheKey = 'bsef:' . $this->_route . ':' . implode( ':', $strParams );

		return true;
	}

	/**
	 * Метод processUnregisterModel вызывается при удалении маршрута для таблиц {{%sef}} и {{%bsef}}.
	 * @param \yii\db\ActiveRecord $model AR, которую необходимо удалить
	 * @param bool $throwExcept true - если нужно генерировать исключение при неудачном удалении
	 * @throws InvalidRouteUnregistrationException если не получилось удалить запись
	 */
	protected function processUnregisterModel( $model, $throwExcept = true )
	{
		if ( is_null( $model ) ) {
			if ( $throwExcept ) {
				throw new InvalidRouteUnregistrationException();
			} else {
				return false;
			}
		}

		/* @var int $countDeleted */
		$countDeleted = $model->delete();

		if ( $countDeleted !== 1 ) {
			if ( $throwExcept ) {
				throw new InvalidRouteUnregistrationException();
			} else {
				return false;
			}
		}

		return true;
	}
}
