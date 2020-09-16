<?php

/**
 * Файл содержит транспорт DirTransport
 * @author M0r1arty <m0r1arty.nv@yandex.ru>
 */
namespace app\modules\grabber\transports;

use Yii;

use app\modules\grabber\base\BaseTransport;
use app\modules\grabber\interfaces\GetFilesInterface;

use app\modules\grabber\exceptions\DirNotFoundException;
use app\modules\grabber\exceptions\NotAccessibleException;

/**
 * Класс DirTransport реализует задачи получения списка файлов директории и получение их контента.
 */
 class DirTransport extends BaseTransport implements GetFilesInterface
 {
 	/**
 	 * {@inheritdoc}
 	 */
 	public static function transportTitle()
 	{
 		return 'Сканер директорий';
 	}

 	/**
 	 * {@inheritdoc}
 	 */
 	public function getFiles( $dir )
 	{
 		/**
 		 * Если это не директория, не читаема или не открывается
 		 */
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
