<?php

/**
 * Файл содержит транспорт HttpTransport
 * @author M0r1arty <m0r1arty.nv@yandex.ru>
 */
namespace app\modules\grabber\transports;

use Yii;

use app\modules\grabber\base\BaseTransport;

/**
 * Класс HttpTransport реализует задачу получения контента с web страницы методом GET
 */
 class HttpTransport extends BaseTransport
 {
 	public static function transportTitle()
 	{
 		return 'HTTP транспорт';
 	}

 	public function getContent( $link = null )
 	{
 		$client = new \GuzzleHttp\Client();

 		$response = $client->request( 'GET', $link );

 		$code = $response->getStatusCode();
 		
 		if ( $code !== 200 ) {
 			//throw
 		}

 		return $response->getBody();
 	}
 }
