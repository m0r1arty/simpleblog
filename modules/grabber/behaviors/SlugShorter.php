<?php

/**
 * Файл содержит поведение SlugShorter
 * @author M0r1arty <m0r1arty.nv@yandex.ru>
 */
namespace app\modules\grabber\behaviors;

use Yii;
use yii\base\Behavior;
use yii\db\ActiveRecord;

/**
 * Поведение SlugShorter обрезает slug до приемлемого размера
 */
class SlugShorter extends Behavior
{
	/**
	 * @var int $max минимальная длина строки, чтобы запустить обрезание
	 */
	public $max = 0;
	/**
	 * @var int $shortTo до какой длины обрезать
	 */
	public $shortTo = 0;
	/**
	 * string $slugAttribute имя атрибута, который подвергается обрезанию
	 */
	public $slugAttribute = 'slug';

	/**
	 * {@inheritdoc}
	 */
	public function events()
	{
		return [
			ActiveRecord::EVENT_BEFORE_VALIDATE => 'shortSlug',
		];
	}

	/**
	 * Метод shortSlug укорачивает при необходимости slug
	 */
	public function shortSlug( $event )
	{
		$model = $event->sender;
		
		$slug = $model->attributes[ $this->slugAttribute ];

		if ( strlen( $slug ) > $this->max ) {
			$model->setAttribute( $this->slugAttribute, substr( $slug, 0, $this->shortTo ) );
		}
	}
}
