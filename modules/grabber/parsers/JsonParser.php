<?php

/**
 */

namespace app\modules\grabber\parsers;

use Yii;

use app\modules\grabber\base\BaseParser;

use app\modules\grabber\exceptions\ContentNotFoundException;
/**
 */
 class JsonParser extends BaseParser
 {
 	public static function parserTitle()
 	{
 		return 'JSON парсер';
 	}

 	public function parse( $data )
 	{
 		$ret = json_decode( $data, true );

 		if ( !is_array( $ret ) && !isset( $ret[ 'title' ], $ret[ 'preview' ], $ret[ 'content' ] ) ) {
 			throw new ContentNotFoundException();
 		}

 		return $ret;
 	}
 }
