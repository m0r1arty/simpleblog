<?php
/**
 * Этот view для рендеринга отдельной категории в форме добавления/редактирования записей
 */

/* @var $this yii\web\View */
/* @var $category array категория для рендеринга */
/* @var $class string css класс итема категории */
?>
<li class="categories-widget-item<?= ' ' . $class ?>" data-id="<?= $category[ 'id' ] ?>"><?= $category[ 'title' ] ?></li>