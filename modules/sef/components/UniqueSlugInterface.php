<?php

/**
 * Файл содержит интерфейс UniqueSlugInterface
 */
namespace app\modules\sef\components;

/**
 * Интерфейс UniqueSlugInterface должны реализовывать модели, которые интегрируются с модулем sef.
 */
interface UniqueSlugInterface
{
	/**
	 * @param string $attribute имя атрибута
	 * @param \app\modules\sef\validators\UniqueSlugValidator $validator текущий экземпляр валидатора. Им можно, воспользоваться, например, чтобы изменить текст $message ошибки, который будет отображён.
	 */
	public function checkUniqueSlug( $attribute, $validator );
}
