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
	 * @param mixed $data данные, которые вернул транспорт.
	 * Это может быть html страницей, массивом имён файлов в каталоге, ассоциативным массивом в котором есть элемент-указатель на следующий источник и т.д.
	 * @return mixed false - в случае, если следующего источника нет; иначе то что будет передано транспорту после обработки всех ссылок
	 */
	public function getNextSource( $data );
}
