<?php

/**
 */

namespace app\modules\grabber\tasks;

use Yii;

use app\modules\grabber\base\BaseTask;

/**
 */
 class HttpTask extends BaseTask
 {
 	public static function taskTitle()
 	{
 		return 'HTTP источник';
 	}

 	public function getParsers()
 	{
 		$parsers = \app\modules\grabber\models\Parsers::find()->all();
 		$ret = [];

 		foreach ( $parsers as $parser ) {
 			$conf = [
 				'class' => $parser->class,
 			];

 			$obj = Yii::createObject( $conf );

 			if ( $obj instanceof \app\modules\grabber\interfaces\GetLinksInterface && 
 			$obj instanceof \app\modules\grabber\interfaces\NextSourceInterface ) {
 				$ret[] = $parser;
 			}
 		}

 		return $ret;
 	}
 }
