<?php

/**
 * Файл содержит класс SluggableBehavior, расширяющий \yii\behaviors\SluggableBehavior.
 * @author M0r1arty <m0r1arty.nv@yandex.ru>
 */
namespace app\modules\sef\behaviors;

/**
 * Класс SluggableBehavior расширяет стандартный SluggableBehavior, чтобы позволить пользователю самому выбирать slug.
 */
class SluggableBehavior extends \yii\behaviors\SluggableBehavior
{
	/**
	 * @var string[] массив названий сценариев для которых нужно запретить базовому классу переопределять slug
	 */
	public $sefAllowedScenarios = [];

	/**
	 * {@inheritdoc}
	 */
	protected function isNewSlugNeeded()
	{
		$model = $this->owner;

		if ( in_array( $model->scenario, $this->sefAllowedScenarios ) ) {
			return true;
		} else {

			foreach ((array) $this->attribute as $attribute) {
				if ( !$model->isAttributeChanged( $attribute ) && $model->isAttributeChanged( $this->slugAttribute ) ) {
					return true;
				}
			}
		}

		return 	parent::isNewSlugNeeded();
	}

	/**
	 * {@inheritdoc}
	 */
	protected function generateSlug( $slugParts )
	{
		if ( !in_array( $this->owner->scenario, $this->sefAllowedScenarios ) || empty( $this->owner->attributes[ $this->slugAttribute ] ) ) {
			return parent::generateSlug( $slugParts );
		}

		return $this->owner->attributes[ $this->slugAttribute ];
	}
}
