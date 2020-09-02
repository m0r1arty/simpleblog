<?php
/**
 * Данный view предназначен для использования при листинге категорий фронтенда.
 */

/* @var $this yii\web\View */
/* @var $categories array массив категорий для рендеринга */
/* @var $viewItem string имя view, которым нужно рендерить итемы категорий */
?>
<div class="categories-widget">
	<ul>
		<?php
		foreach ( $categories as $category ) {
			/* @var $category string[string]*/
			echo $this->render( $viewItem, [ 'category' => $category ] );
		}
		?>
	</ul>
</div>
