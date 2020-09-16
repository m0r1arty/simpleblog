<?php

/**
 * Файл содержит исключение NotAccessibleException
 * @author M0r1arty <m0r1arty.nv@yandex.ru>
 */
namespace app\modules\grabber\exceptions;

/**
 * Класс NotAccessibleException является исключением, выбрасываемым контент не доступен(например, файл является нечитаемым).
 */
class NotAccessibleException extends \Exception
{
    /**
     * @return string the user-friendly имя исключения
     */
    public function getName()
    {
        return 'Not Accessible Exception';
    }
}
