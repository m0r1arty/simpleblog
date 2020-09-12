<?php

/**
 * Файл содержит исключение NotFoundRouteException
 * @author M0r1arty <m0r1arty.nv@yandex.ru>
 */

namespace app\modules\sef\exceptions;

/**
 * Класс InvalidRouteRegistrationException является исключением, выбрасываемым при неудачном поиске маршрута в модуле sef.
 */
class NotFoundRouteException extends \Exception
{
    /**
     * @return string the user-friendly имя исключения
     */
    public function getName()
    {
        return 'Not Found Route Exception';
    }
}
