<?php

/**
 * Файл содержит интерфейс GetLinksInterface.
 * @author M0r1arty <m0r1arty.nv@yandex.ru>
 */
namespace app\modules\grabber\interfaces;

/**
 * Интерфейс GetLinksInterface предназначен для парсеров.
 */
interface GetLinksInterface
{
	/**
	 * @param mixed $data данные, которые вернул транспорт.
	 * Это может быть html страницей, массивом имён файлов в каталоге, ассоциативным массивом в котором есть элемент-указатель на следующий источник и т.д.
	 * @return array массив ссылок, которые будут переданы транспорту одна за другой для обработки
	 * @throws \app\modules\grabber\exceptions\LinksNotFoundException если данные не содержат ссылок.
	 */
	public function getLinks( $data );
}
