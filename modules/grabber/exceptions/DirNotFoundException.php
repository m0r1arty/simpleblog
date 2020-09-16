<?php

/**
 * Файл содержит исключение DirNotFoundException
 * @author M0r1arty <m0r1arty.nv@yandex.ru>
 */
namespace app\modules\grabber\exceptions;

/**
 * Класс DirNotFoundException является исключением, выбрасываемым при попытке получить список файлов из директории, которой нет или которую нельзя прочитать.
 */
class DirNotFoundException extends \Exception
{
    /**
     * @return string the user-friendly имя исключения
     */
    public function getName()
    {
        return 'Directory Not Found Exception';
    }
}
