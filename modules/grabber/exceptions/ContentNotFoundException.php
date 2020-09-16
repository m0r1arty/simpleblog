<?php

/**
 * Файл содержит исключение ContentNotFoundException
 * @author M0r1arty <m0r1arty.nv@yandex.ru>
 */
namespace app\modules\grabber\exceptions;

/**
 * Класс ContentNotFoundException является исключением, выбрасываемым при неудачной попытке разбора содержимого страницы.
 */
class ContentNotFoundException extends \Exception
{
    /**
     * @return string the user-friendly имя исключения
     */
    public function getName()
    {
        return 'Content Not Found Exception';
    }
}
