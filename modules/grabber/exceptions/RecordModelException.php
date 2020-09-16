<?php

/**
 * Файл содержит исключение RecordModelException
 * @author M0r1arty <m0r1arty.nv@yandex.ru>
 */
namespace app\modules\grabber\exceptions;

/**
 * Класс RecordModelException является исключением, выбрасываемым при неудачном сохранении записи граббером.
 */
class RecordModelException extends \Exception
{
	public $model;
    /**
     * @return string the user-friendly имя исключения
     */
    public function getName()
    {
        return 'Record Model Exception';
    }

    public function __construct( $model )
    {
    	parent::__construct( $this->getName() );
    	
    	$this->model = $model;
    }
}
