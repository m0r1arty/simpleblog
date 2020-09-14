<?php

/**
 */

namespace app\modules\grabber\transports;

use Yii;

use app\modules\grabber\base\BaseTransport;

/**
 */
 class HttpTransport extends BaseTransport
 {
 	public static function transportTitle()
 	{
 		return 'HTTP транспорт';
 	}

 	public function getContent( $link = null )
 	{
 		//
 	}
 }
