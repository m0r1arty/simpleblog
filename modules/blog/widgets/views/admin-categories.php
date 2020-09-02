<?php

/**
 * Данный view предназначен для использования в форме добавления и редактирования записей для привязки записи к категориям.
 */

/* @var $this yii\web\View */
/**
 * @var $css string[string] ключами массива ожидаются строки(имена css классов): container, default, active, error
 * container - для контейнера, в котором перечисляются категории
 * default - для элемента, который можно выбрать
 * active - для элемента, который выбран
 * error - для элемента, который выбрать нельзя по какой-то причине
*/
/* @var $categories array */
?>
<div class="categories-widget<?= ' '.$css[ 'container' ] ?>" data-css-default="<?= $css[ 'default' ] ?>" data-css-active="<?= $css[ 'active' ] ?>" data-css-error="<?= $css[ 'error' ] ?>">
	<input type="hidden" class="categories-widget-input" name="<?= $inputName ?>" value="<?= $value ?>" id="<?= $inputId ?>"/>
	<ul>
		<?php
		foreach ( $categories as $category ) {
			/* @var $category string[string]*/
			if ( !isset( $category[ 'status' ] ) ) {
				$class = $css[ 'default' ];
			} else {
				switch ( $category[ 'status' ] ) {
					case 'd'://default
						$class = $css[ 'default' ];
						break;
					case 'a'://active
						$class = $css[ 'active' ];
						break;
					case 'ar':
						$class = $css[ 'active' ] . ' ' . $css[ 'error' ];
						break;
					case 'e'://error
						$class = $css[ 'error' ];
						break;
					default:
						$class = $css[ 'default' ];
				}
			}
			echo $this->render( $viewItem, [ 'class' => $class, 'category' => $category ] );
		}
		?>
	</ul>
</div>
