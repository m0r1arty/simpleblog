<?php

/**
 */

namespace app\modules\grabber\base;

use Yii;

/**
 */

abstract class BaseParser extends BaseBehavior
{
	abstract public static function parserTitle();
	abstract public function parse( $data );
}
