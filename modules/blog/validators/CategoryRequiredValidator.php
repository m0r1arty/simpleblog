<?php

/**
 * Файл содержит валидатор CategoryRequiredValidator.
 * @author M0r1arty <m0r1arty.nv@yandex.ru>
 */

namespace app\modules\blog\validators;

use Yii;
use yii\validators\Validator;
use yii\helpers\Html;

use app\modules\blog\traits\CategoryIDsTrait;

/**
 * Валидатор CategoryRequiredValidator добавляет проверку того, что к записи была добавлена хотя бы одна категория.
 * Поддерживает статичную валидацию, а так же валидацию на стороне клиента.
 */
class CategoryRequiredValidator extends Validator
{
	use CategoryIDsTrait;

	/**
	 * {@inheritdoc}
	 */
	public function init()
	{
		parent::init();

		if ( empty( $this->message ) ) {
			$this->message = 'Нужно привязать хотя бы одну категорию';
		}
	}
	/**
	 * {@inheritdoc}
	 */
	public function validateAttribute( $model, $attribute )
	{
		$ids = [];
		
		$this->parseCategoryIDs( $model->$attribute, $ids );

		if ( empty( $ids ) ) {
			$model->addError( $attribute, $this->formatMessage( $this->message, [ 'attribute' => $attribute ] ) );
		}
	}

	/**
	 * Подключает client-side валидацию. Тесно связан с categories.widget.asset.js - этот скрипт работает с категориями сгенерированными виджетом [[\app\modules\blog\widgets\CategoriesWidget]].
	 * window.categoriesIDs - это Map, ключами которого являются inputId элемента виджета, а значениями экземпляры класса, определённого в JS файле.
	 * {@inheritdoc}
	 */
	public function clientValidateAttribute( $model, $attribute, $view )
	{
		$inputId = Html::getInputId( $model, $attribute );
		$errMsg = $this->formatMessage( $this->message, [ 'attribute' => $attribute ] );

		return <<<JS
		{
			let categoryWidget = window.categoriesIDs[ '{$inputId}' ];

			if ( categoryWidget === undefined ) {
				console.error( 'Не могу найти CategoriesWidget' )
			} else {
				if ( categoryWidget.countActive() === 0 ) {
					messages.push( '{$errMsg}' );
				}
			}
		}
JS;
	}
}
