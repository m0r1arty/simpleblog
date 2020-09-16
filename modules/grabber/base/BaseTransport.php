<?php

/**
 */

namespace app\modules\grabber\base;

use Yii;

/**
 */

abstract class BaseTransport extends BaseBehavior
{
	abstract public static function transportTitle();
	abstract public function getContent( $file );
}
