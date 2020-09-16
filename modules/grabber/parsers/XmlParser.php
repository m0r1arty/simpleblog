<?php

/**
 */

namespace app\modules\grabber\parsers;

use Yii;

use app\modules\grabber\base\BaseParser;
use app\modules\grabber\exceptions\ContentNotFoundException;

/**
 */
 class XmlParser extends BaseParser
 {
 	public static function parserTitle()
 	{
 		return 'XML парсер';
 	}

 	public function parse( $data )
 	{
 		$xml = @simplexml_load_string( $data );
 		
 		if ( $xml === false ) {
 			throw new ContentNotFoundException();
 		}

 		if ( !isset( $xml->title, $xml->preview, $xml->content ) ) {
 			throw new ContentNotFoundException();
 		}

 		$ret = [
 			'title' => strval( $xml->title ),
 			'preview' => strval( $xml->preview ),
 			'content' => strval( $xml->content ),
 		];
 		
 		return $ret;
 	}
 }
