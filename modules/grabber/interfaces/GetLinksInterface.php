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
	 * Метод getLinks получает данные со страницы, которую вернул транспорт, парсит и возвращает ссылки, которые нужно загрузить для поиска данных записи блога.
	 * @param mixed $data данные, которые вернул транспорт.
	 * @return array массив ссылок, которые будут переданы транспорту одна за другой для обработки
	 * @throws \app\modules\grabber\exceptions\LinksNotFoundException если данные не содержат ссылок.
	 */
	public function getLinks( $data );
}
