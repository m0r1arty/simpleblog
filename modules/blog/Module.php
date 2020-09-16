<?php

/**
 * Файл содержит модуль блога. Сам модуль тесно взаимодействует с модулем sef(ЧПУ).
 * Модуль содержит контроллеры BlogController и CategoriesController.
 * BlogController отвечает за вывод постов в общей ленте и по категориям, а так же CRUD функционал(сгенерированный gii).
 * CategoriesController содержит исключительно CRUD функционал(сгенерированный gii).
 * Для доступа к CRUD функционалу необходима авторизация. Создать учётные данные можно консольной командой:
 * 		yii users/create username
 * 
 * @author M0r1arty <m0r1arty.nv@yandex.ru>
 */

namespace app\modules\blog;

use Yii;

use app\components\Module as AppModule;

/**
 * Класс модуля blog
 */
class Module extends AppModule
{
	/**
	 * @var string|bool $alias регистрация алиаса @blog или другого; false отменяет регистрацию
	 */
	public $alias = '@blog';
	/**
	 * @var bool $registerInBuiltInValidators если true - валидатор будет доступен в [[\yii\base\Model::rules]] через short name 'categoryRequired'
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
	 * Регистрирует, если нужно, алиас @blog или другой. Регистрирует, если задано, валидатор CategoryRequiredValidator с коротким именем categoryRequired.
	 * @inheritdoc
	*/
	public function bootstrap( $app )
	{
		parent::bootstrap( $app );

		/**
		 * Регистрация псевдонима, если нужна.
		 */
		if ( ! ( $this->alias === false ) ) {
			Yii::setAlias( $this->alias, '@app/modules/blog' );
		}

		/**
		 * Регистрация shortname валидатора, если нужна
		 */
		if( $this->registerInBuiltInValidators ) {
			if ( isset( \yii\validators\Validator::$builtInValidators[ 'categoryRequired' ] ) ) {
				Yii::warning( 'Validator::$builtInValidators уже содержит валидатор categoryRequired' );
			}

			\yii\validators\Validator::$builtInValidators[ 'categoryRequired' ] = 'app\modules\blog\validators\CategoryRequiredValidator';
		}
	}
}
