<?php

/**
 */

namespace app\modules\blog\components;

use Yii;

use yii\base\Event;

/**
 */

class CategoriesFilterEvent extends Event
{
	public $categories = [];
}
