<?php

/**
 * Файл содержит исключение LinksNotFoundException
 * @author M0r1arty <m0r1arty.nv@yandex.ru>
 */
namespace app\modules\grabber\exceptions;

/**
 * Класс LinksNotFoundException является исключением, выбрасываемым при неудачной попытке найти ссылки на контент.
 */
class LinksNotFoundException extends \Exception
{
    /**
     * @return string the user-friendly имя исключения
     */
    public function getName()
    {
        return 'Links Not Found Exception';
    }
}
