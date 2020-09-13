<?php

/**
 * Файл содержит UniqueSlugValidator валидатор
 * @author M0r1arty <m0r1arty.nv@yandex.ru>
 */

namespace app\modules\sef\validators;

/**
 * Класс UniqueSlugValidator представляет валидатор, проверяющий уникальность slug`а у выбранного узла sef дерева.
 * Сама валидация должна осуществляться в модели для которой включена валидация, поскольку только модель может знать какой route и с какими параметрами изпользуются.
 * Модель должна реализовывать интерфейс \app\modules\sef\components\UniqueSlugInterface
 */
class UniqueSlugValidator extends \yii\validators\Validator
{
	/**
	 * @var string[] $msgParams массив для параметров, используемых в formatMessage
	 */
	public $msgParams = [];

	/**
	 * {@inheritdoc}
	 */
	public function init()
	{
		parent::init();

		if ( empty( $this->message ) ) {
			$this->message = '{attribute} содержит значение, которое уже используется на данном уровне дерева';
		}
	}

	/**
	 * {@inheritdoc}
	 */
	public function validateAttribute( $model, $attribute )
	{
		if ( !isset( $model->attributes[ $attribute ] ) ) {
			return;
		}

		if ( isset( $model->attributes[ $attribute ], $model->oldAttributes[ $attribute ] ) &&  !$model->isAttributeChanged( $attribute ) ) {
			return;
		}

		if ( !$model instanceof \app\modules\sef\components\UniqueSlugInterface ) {
			$model->addError( $attribute, 'Модель должна реализовывать UniqueSlugInterface интерфейс' );
		}

		if ( !$model->checkUniqueSlug( $attribute, $this ) ) {
			$model->addError( $attribute, $this->formatMessage( $this->message, array_merge( $this->msgParams, [ 'attribute' => $attribute ] ) ) );
		}
	}
}
