<?php

/**
 * Файл содержит интерфейс GetFilesInterface.
 * @author M0r1arty <m0r1arty.nv@yandex.ru>
 */
namespace app\modules\grabber\interfaces;

/**
 * Интерфейс GetFilesInterface предназначен для транспортов, которые должны возвращать файлы.
 */
interface GetFilesInterface
{
	/**
	 * Метод getFiles используется для получения списка файлов в указанной директории.
	 * @param string $dir директория, из которой нужно собрать файлы
	 * @return array массив имён файлов в директории; пустой массив, если директория не содержит файлов.
	 * @throws \app\modules\grabber\exceptions\DirNotFoundException если $dir не является директорий
	 */
	public function getFiles( $dir );
}
