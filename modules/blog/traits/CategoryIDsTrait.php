<?php

/**
 * Файл содержит трейт CategoryIDsTrait.
 * @author M0r1arty <m0r1arty.nv@yandex.ru>
 */

namespace app\modules\blog\traits;

/**
 * Трейт CategoryIDsTrait подключает к классу метод parseCategoryIDs
 */
trait CategoryIDsTrait
{
	/**
	 * Метод парсит строку и складывает найденные идентификаторы в массив, переданный по ссылке
	 * @param string $str строка, которая должна иметь вид '1,3,8' etc
	 * @param int[] &ids массив, куда будут помещены найденные идентификаторы
	 * @throws \yii\base\InvalidArgumentException
	 */
	public function parseCategoryIDs( $str, &$ids )
	{
		if ( !is_string( $str ) ) {
			throw new \yii\base\InvalidArgumentException();
		}

		foreach ( explode( ',', $str ) as $id ) {
			$id = intval( trim( $id ) );

			if ( $id > 0 ) {
				$ids[] = $id;
			}
		}
	}
}
