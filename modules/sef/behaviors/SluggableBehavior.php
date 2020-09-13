<?php

/**
 * Файл содержит класс SluggableBehavior, расширяющий \yii\behaviors\SluggableBehavior.
 * @author M0r1arty <m0r1arty.nv@yandex.ru>
 */
namespace app\modules\sef\behaviors;

use Yii;

use yii\validators\UniqueValidator;

/**
 * Класс SluggableBehavior расширяет стандартный SluggableBehavior, чтобы позволить пользователю самому выбирать slug.
 * Для того, чтобы позволить пользователю самостоятельно выбирать slug, необходимо добавить название сценария в $sefAllowedScenarios.
 * Для того, чтобы отобразить ошибку валидации(неуникальный slug) на странице формы без генерации уникального slug`а, необходимо добавить название сценария в $sefShowErrorsInScenarios. Без этого будет автоматически сгенерировано имя средствами базового класса вида slug-N. Соответственно при автоматическом добавлении нужно использовать сценарии, которые НЕ СОДЕРЖАТЬСЯ в данном массиве.
 */
class SluggableBehavior extends \yii\behaviors\SluggableBehavior
{
	/**
	 * @var string[] массив названий сценариев для которых нужно запретить базовому классу переопределять slug
	 */
	public $sefAllowedScenarios = [];
	/**
	 * @var string[] массив названий сценариев для которых НЕ НУЖНО генерировать уникальный slug
	 */
	public $sefShowErrorsInScenarios = [];

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

	/**
	 * {@inheritdoc}
	 */
	protected function makeUnique( $slug )
	{
		$model = $this->owner;

		if ( !in_array( $model->scenario, $this->sefShowErrorsInScenarios ) ) {
			return parent::makeUnique( $slug );
		}

        /* @var $validator UniqueValidator */
        /* @var $model BaseActiveRecord */
        $validator = Yii::createObject(array_merge(
            [
                'class' => UniqueValidator::className(),
            ],
            $this->uniqueValidator
        ));

        $model = $this->owner;

        $validator->validateAttribute($model, $this->slugAttribute);

		return $slug;
	}
}
