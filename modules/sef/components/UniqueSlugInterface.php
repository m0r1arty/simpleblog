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
	public function checkUniqueSlug( $attribute );
}
