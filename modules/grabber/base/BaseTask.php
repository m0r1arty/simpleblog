<?php

/**
 */

namespace app\modules\grabber\base;

use Yii;

use yii\base\Behavior;

/**
 */
abstract class BaseTask extends Behavior
{
	abstract public static function taskTitle();
	
 	public function getParsers()
 	{
 		return \app\modules\grabber\models\Parsers::find()->all();
 	}

 	public function getTransports()
 	{
 		return \app\modules\grabber\models\Transports::find()->all();
 	}
}
