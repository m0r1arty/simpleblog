<?php

/**
 */

namespace app\modules\grabber\base;

use Yii;

/**
 */

abstract class BaseParser
{
	abstract public static function parserTitle();
	abstract public function parse( $data );
}
