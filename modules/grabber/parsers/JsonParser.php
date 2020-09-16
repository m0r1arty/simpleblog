<?php

/**
 * Файл содержит парсер JsonParser
 * @author M0r1arty <m0r1arty.nv@yandex.ru>
 */

namespace app\modules\grabber\parsers;

use Yii;

use app\modules\grabber\base\BaseParser;
use app\modules\grabber\exceptions\ContentNotFoundException;

/**
 * Класс JsonParser реализует парсер json данных.
 */
 class JsonParser extends BaseParser
 {
 	/**
 	 * {@inheritdoc}
 	 */
 	public static function parserTitle()
 	{
 		return 'JSON парсер';
 	}

 	/**
 	 * {@inheritdoc}
 	 */
 	public function parse( $data )
 	{
 		$ret = json_decode( $data, true );

 		/**
 		 * Обязательно должны быть только title, preview, content - остальное игнорируется.
 		 */
 		if ( !is_array( $ret ) && !isset( $ret[ 'title' ], $ret[ 'preview' ], $ret[ 'content' ] ) ) {
 			throw new ContentNotFoundException();
 		}

 		return $ret;
 	}
 }
