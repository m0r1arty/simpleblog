<?php
/**
 * Данный view рендерит отдельный item списка категорий фронтенда
 */

/* @var $this yii\web\View */
/* @var $category array категория для рендеринга */
?>
<?php if ( $category[ 'status' ] === 'd' ): ?>
	<li class="categories-widget-item default"><a href="<?= $category[ 'link' ] ?>"><?= $category[ 'title' ] ?></a></li>
<?php else: ?>
	<li class="categories-widget-item active"><?= $category[ 'title' ] ?></li>
<?php endif; ?>