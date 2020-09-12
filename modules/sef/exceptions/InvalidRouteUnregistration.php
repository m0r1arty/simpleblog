<?php

/**
 * Файл содержит исключение InvalidRouteUnregistrationException
 * @author M0r1arty <m0r1arty.nv@yandex.ru>
 */
namespace app\modules\sef\exceptions;

/**
 * Класс InvalidRouteUnregistrationException является исключением, выбрасываемым при неудачной попытке удаления маршрута из модуля sef.
 */
class InvalidRouteUnregistrationException extends \Exception
{
    /**
     * @return string the user-friendly имя исключения
     */
    public function getName()
    {
        return 'Invalid Route Unregistration Exception';
    }
}
