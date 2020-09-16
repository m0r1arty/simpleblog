<?php

/**
 * Файл содержит парсер XmlParser
 * @author M0r1arty <m0r1arty.nv@yandex.ru>
 */
namespace app\modules\grabber\parsers;

use Yii;

use app\modules\grabber\base\BaseParser;
use app\modules\grabber\exceptions\ContentNotFoundException;

/**
 * Класс реализует парсер xml данных. Документ должен выглядеть так:
 * <?xml version="1.0" encoding="UTF-8"?>
 * <record>
 * 		<title><![CDATA[Это самый обычный тайтл]]></title>
 * 		<preview><![CDATA[Превью превью превью]]></preview>
 * 		<content><![CDATA[Контент контент контент]]></content>
 * </record>
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
