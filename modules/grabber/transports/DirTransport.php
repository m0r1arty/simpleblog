<?php

/**
 */

namespace app\modules\grabber\transports;

use Yii;

use app\modules\grabber\base\BaseTransport;
use app\modules\grabber\interfaces\GetFilesInterface;

use app\modules\grabber\exceptions\DirNotFoundException;
use app\modules\grabber\exceptions\NotAccessibleException;

/**
 */
 class DirTransport extends BaseTransport implements GetFilesInterface
 {
 	public static function transportTitle()
 	{
 		return 'Сканер директорий';
 	}

 	/**
 	 * {@inheritdoc}
 	 */
 	public function getFiles( $dir )
 	{
 		if ( !is_dir( $dir ) || !is_readable( $dir ) || ( $dh = opendir( $dir ) ) === false ) {
 			throw new DirNotFoundException();
 		}

 		/* @var string[] $ret массив имён файлов */
 		$ret = [];

 		while ( ( $file = readdir( $dh ) ) !== false ) {
 			if ( !is_file( $dir . DIRECTORY_SEPARATOR . $file ) ) {
 				continue;
 			}

 			$ret[] = $dir . DIRECTORY_SEPARATOR . $file;
 		}

 		closedir( $dh );

 		return $ret;
 	}

 	/**
 	 * {@inheritdoc}
 	 */
 	public function getContent( $file )
 	{
 		if ( !is_file( $file ) || !is_readable( $file ) ) {
 			throw new NotAccessibleException();
 		}

 		return file_get_contents( $file );
 	}
 }
