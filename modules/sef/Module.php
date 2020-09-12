<?php

/**
 * Файл содержит модуль ЧПУ
 * @author M0r1arty <m0r1arty.nv@yandex.ru>
 */

namespace app\modules\sef;

use Yii;

use app\components\Module as AppModule;

/**
 * Класс модуля sef.
 */

class Module extends AppModule
{
	/**
	 * @var string|bool $alias регистрация алиаса @sef или другого; false отменяет регистрацию
	 */
	public $alias = '@sef';
	/**
	 * @var bool $registerInBuiltInValidators если true - валидатор будет доступен в [[\yii\base\Model::rules]] через short name 'slug'
	 */
	public $registerInBuiltInValidators = true;
	/**
	 * @inheritdoc
	*/
	public function init()
	{
		parent::init();
	}

	/**
	 * Регистрирует, если нужно, алиас @sef или другой. Регистрирует, если задано, валидатор SlugValidator с коротким именем slug.
	 * @inheritdoc
	*/
	public function bootstrap( $app )
	{
		parent::bootstrap( $app );

		if ( ! ( $this->alias === false ) ) {
			Yii::setAlias( $this->alias, '@app/modules/sef' );
		}

		if( $this->registerInBuiltInValidators ) {
			if ( isset( \yii\validators\Validator::$builtInValidators[ 'uniqueslug' ] ) ) {
				Yii::warning( 'Validator::$builtInValidators уже содержит валидатор uniqueslug' );
			}

			\yii\validators\Validator::$builtInValidators[ 'uniqueslug' ] = 'app\modules\sef\validators\UniqueSlugValidator';
		}
	}
}
