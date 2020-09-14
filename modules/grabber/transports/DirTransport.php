<?php

/**
 */

namespace app\modules\grabber\transports;

use Yii;

use app\modules\grabber\base\BaseTransport;

/**
 */
 class DirTransport extends BaseTransport
 {
 	public static function transportTitle()
 	{
 		return 'Сканер директорий';
 	}

 	public function getContent( $file = null )
 	{
 		//
 	}
 }
