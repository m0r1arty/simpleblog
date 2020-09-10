<?php

/**
 * Файл содержит исключение InvalidRouteRegistrationException
 * @author M0r1arty <m0r1arty.nv@yandex.ru>
 */
namespace app\modules\sef\exceptions;

/**
 * Класс InvalidRouteRegistrationException является исключением, выбрасываемым при неудачной регистрации маршрута в модуле sef.
 */
class InvalidRouteRegistrationException extends \Exception
{
    /**
     * @return string the user-friendly имя исключения
     */
    public function getName()
    {
        return 'Invalid Route Registration Exception';
    }
}
