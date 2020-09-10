<?php

/**
 * Файл содержит компонент UrlRule.
 * @author M0r1arty <m0r1arty.nv@yandex.ru>
 */

namespace app\modules\sef\components;

use Yii;
use yii\base\Component;
use yii\web\UrlRuleInterface;

use app\modules\sef\helpers\Sef;

/**
 * Класс UrlRule реализует интерфейс UrlRuleInterface для поддержки ЧПУ модулем sef.
 */
class UrlRule implements UrlRuleInterface
{
	/**
	 * {@inheritdoc}
	 */
	public function createUrl( $manager, $route, $params )
	{
		return Sef::getUrlByRoute( $route, $params );
	}

	/**
	 * {@inheritdoc}
	 * @throws \yii\base\InvalidArgumentException если переданные параметры имеют тип, который не ожидаем
	 */
	public function parseRequest( $manager, $request )
	{
		if ( !( $manager instanceof \yii\web\UrlManager && $request instanceof \yii\web\Request ) ) {
			throw new \yii\base\InvalidArgumentException();
		}

		/**
		 * Сначала парсим url
		 */
		$parsedUrl = parse_url( $request->url );
		$path = explode( '/', $parsedUrl[ 'path' ] );
		/**
		 * Избавляемся от лишних слешей
		 */
		$path = array_reduce( $path, function( $resultArray, $partUrl )
		{
			$partUrl = trim( $partUrl );

			if ( !empty( $partUrl ) ) {
				$resultArray[] = $partUrl;
			}

			return $resultArray;
		}, [] );

		if ( empty( $path ) ) {
			return false;
		}

		/**
		 * Данные как роутить ЧПУ будут хранится в n-1 уровне вложенности, т.е.
		 * /a/b/c url означает, что "c" будет ключём в ассоциативном массиве, который лежит в кеше по ключу "sef:a:b"
		 */
		$slug = array_pop( $path );

		if ( empty( $path ) ) {
			$cacheKey = 'sefroot';
		} else {
			$cacheKey = 'sef:' . implode( ':', $path );
		}

		/**
		 * @var array[string] ключами являются slug, значениями контроллер и параметры
		 */
		$data = Yii::$app->cache->get( $cacheKey );

		if ( $data === false ) {
			$data = Sef::retriveDataByPath( $path );

			if ( $data === false ) {
				return false;
			} elseif( is_array( $data ) ) {
				Yii::$app->cache->set( $cacheKey, json_encode( $data ) );
			} else {
				throw new \yii\base\InvalidValueException();
			}
		} else {
			$data = json_decode( $data, true );
		}

		if ( !isset( $data[ $slug ] ) ) {
			return false;
		}

		$route = array_shift( $data[ $slug ] );

		$data[ $slug ] = array_merge( $data[ $slug ], $request->queryParams );

		return [ $route, $data[ $slug ] ];
	}
}
