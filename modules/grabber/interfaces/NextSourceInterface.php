<?php

/**
 * Файл содержит интерфейс NextSourceInterface.
 * @author M0r1arty <m0r1arty.nv@yandex.ru>
 */
namespace app\modules\grabber\interfaces;

/**
 * Интерфейс NextSourceInterface предназначен для парсеров.
 */
interface NextSourceInterface
{
	/**
	 * Метод getNextSource получает данные со страницы, которую вернул транспорт и возвращает ссылку на следующую страницу(на которой должны быть ссылки на контент).
	 * @param mixed $data данные, которые вернул транспорт.
	 * @return mixed false - в случае, если следующего источника нет; иначе то что будет передано транспорту после обработки всех ссылок
	 */
	public function getNextSource( $data );
}
