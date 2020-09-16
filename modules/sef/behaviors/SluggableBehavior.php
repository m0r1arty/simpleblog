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
		/**
		 * В базовом если метод вернёт true будет сгенерирован новый slug, иначе slug останется прежним(из post).
		 * Алгоритм базового:
		 * если slug пустой - вернуть true
		 * если immutable === true вернуть false
		 * если attribute is null - вернуть true
		 * если изменился attribute - вернуть true
		 * 
		 * Для чего надо возвращать true, если по сценарию разрешается редактировать slug как угодно?
		 * Чтобы не прохлопать пустой slug и протащить валидацию на уникальность.
		 * 
		 * Смысл в следующем:
		 * Если разрешено изменять slug или запрещено, но slug изменился - будет вызван generateSlug, а он перегружен.
		 * generateSlug вернёт slug, который указал пользователь, если изменять slug разрешено или он пустой.
		 * Если ensureUnique == true - будет вызван makeUnique, а он перегружен.
		 * makeUnique - вызовет 1 раз валидацию slug на уникальность и оставит ошибку для отображения, если сценарий запрещает автоматическую генерацию уникальных уникальных slug.
		 */
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
	 * @see [[isNewSlugNeeded]]
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
	 * @see [[isNewSlugNeeded]]
	 * {@inheritdoc}
	 */
	protected function makeUnique( $slug )
	{
		$model = $this->owner;

		if ( !in_array( $model->scenario, $this->sefShowErrorsInScenarios ) ) {
			return parent::makeUnique( $slug );
		}

		/**
		 * @see [[\yii\validators\SluggableBehavior::validateSlug]]
		 */
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
