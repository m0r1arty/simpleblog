<?php

/**
 */

namespace app\modules\grabber\parsers;

use Yii;

use app\modules\grabber\base\BaseParser;

use app\modules\grabber\interfaces\GetLinksInterface;
use app\modules\grabber\interfaces\NextSourceInterface;

/**
 */
 class ChelseaBluesParser extends BaseParser implements GetLinksInterface, NextSourceInterface
 {
 	public static function parserTitle()
 	{
 		return 'Парсер ChelseaBlues';
 	}

 	public function parse( $data )
 	{
 		//
 	}

 	/**
 	 * {@inheritdoc}
 	 */
 	public function getNextSource( $data )
 	{
 		//
 	}

 	/**
 	 * {@inheritdoc}
 	 */
 	public function getLinks( $data )
 	{
 		//
 	}
 }
