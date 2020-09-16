<?php

/**
 * Файл содержит виджет CategoriesWidget, который можно использовать и в ActiveForm как элемент привязки категорий, и на фронтенде для выбора интересующей категории.
 * @author M0r1arty <m0r1arty.nv@yandex.ru>
 */
 
 namespace app\modules\blog\widgets;

 use Yii;
 use yii\base\Widget;
 use yii\helpers\Html;

/**
 * Виджет CategoriesWidget. Работает в двух режимах:
 * 1. статичный для выбора категории с постами
 * 2. как элемент управления в ActiveForm
 * Позволяет переопределить путь поиска views.
 */
 class CategoriesWidget extends Widget
 {
 	/**
 	 * Кое-что скопировано из \yii\widgets\ActiveField для поддержки ActiveForm
 	 * @see [[\yii\widgets\ActiveField]]
 	 */
    /**
     * @var ActiveForm the form that this field is associated with.
     */
    public $form;
    /**
     * @var Model the data model that this field is associated with.
     */
    public $model;
    /**
     * @var string the model attribute that this field is associated with.
     */
    public $attribute;
    /* @var array елементы - ассоциативные массивы. Подробнее \app\modules\blog\widgets\views */
    public $categories = [];
    /* @var string|false $viewPath позволяет переопределить путь поиска views; поддерживает алиасы */
    public $viewPath = false;
    /* @var string $viewList view для рендеринга категорий */
 	public $viewList = 'admin-categories';
 	/* @var string $viewItem view для рендеринга отдельной категории */
 	public $viewItem = 'admin-category';
 	/* @var string $containerCssClass css класс для контейнера категорий; на данный момент используется только в режиме "элемент управления" */
 	public $containerCssClass = 'admin-list-categories';
 	/* @var string $defaultCssItemClass css класс итема категории, которую можно выбрать; используется только в режиме "элемент управления" */
 	public $defaultCssItemClass = 'default';
 	/* @var string $activeCssItemClass css класс итема выбранной категории; используется только в режиме "элемент управления" */
 	public $activeCssItemClass = 'active';
 	/* @var string $errorCssItemClass css класс итема категории, которую нельзя выбрать; может использоваться вместе с $activeCssItemClass ; используется только в режиме "элемент управления" */
 	public $errorCssItemClass = 'error';

 	/**
 	 * Расширяет базовый метод, позволяя указать другой путь для поиска views
 	 * {@inheritdoc}
 	 * @throws \yii\base\InvalidConfigException
 	 */
 	public function getViewPath()
 	{
 		if ( $this->viewPath === false ) {
 			return parent::getViewPath();
 		} elseif( is_string( $this->viewPath ) ) {
 			return Yii::getAlias( $this->viewPath );
 		} else {
 			throw new \yii\base\InvalidConfigException();
 		}
 	}

 	/**
 	 * {@inheritdoc}
 	 * @throws \yii\base\InvalidConfigException
 	 */
 	public function run()
 	{
 		$params = [];

        /**
         * $this->categories должне быть массивом
         */
 		if ( !is_array( $this->categories ) ) {
 			throw new \yii\base\InvalidConfigException;
 		}

        /**
         * Нет категорий для выбора
         */
 		if ( empty( $this->categories ) ) {
 			return '';
 		}

        /**
         * Ключи title и link должны быть у каждой категории
         */
 		foreach ( $this->categories as $category ) {
 			if ( !is_array( $category ) || !isset( $category[ 'title' ], $category[ 'link' ] ) ) {
 				throw new \yii\base\InvalidConfigException();
 			}
 		}

        /**
         * CSS классы для контейнера, категории, которую можно выбрать, активной категории и категории, которую выбрать нельзя.
         */
 		$params[ 'css' ] = [
 			'container' => $this->containerCssClass,
 			'default' => $this->defaultCssItemClass,
 			'active' => $this->activeCssItemClass,
 			'error' => $this->errorCssItemClass,
 		];

 		$params[ 'categories' ] = $this->categories;
 		$params[ 'viewItem' ] = $this->viewItem;

 		if ( !empty( $this->model ) ) {
 			$params[ 'inputName' ] = Html::getInputName( $this->model, $this->attribute );
 			$params[ 'inputId' ] = Html::getInputId( $this->model, $this->attribute );
 			$params[ 'value' ]  = $this->model->categoryIDs;
 		}

 		return $this->render( $this->viewList, $params );
 	}
 }
