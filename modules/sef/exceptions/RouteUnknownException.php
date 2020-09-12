<?php

/**
 * Файл содержит исключение RouteUnknownException
 * @author M0r1arty <m0r1arty.nv@yandex.ru>
 */
namespace app\modules\sef\exceptions;
/**
 * Класс RouteUnknownException является исключением, выбрасываемым при "неожиданных" обстоятельствах. Например, при изменении имени slug`а.
 */

class RouteUnknownException extends \Exception
{
    /**
     * @return string the user-friendly имя исключения
     */
    public function getName()
    {
        return 'Route Unknown Exception';
    }
}