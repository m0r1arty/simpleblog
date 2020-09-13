<?php

use yii\helpers\Url;
use yii\helpers\Html;
/* @var $this yii\web\View */
/* @var $model app\modules\blog\models\Records */
?>
<div class="record-list-view item">
	<div><h3><?= $model->title ?></h3></div>
	<div class="record preview content"><?= $model->preview ?></div>
	<div class="record preview footer left"><span><a href="<?= $model->makeLink( $catid ) ?>">Читать...</a></span></div>
	<div class="record preview footer right"><i class="fa fa-calendar-alt" title="<?= ( $model->created_at === $model->updated_at )? 'Дата публикации' : 'Дата публикации(редактировано)' ?>"></i><span><?= $model->getDate( 'Y.m.d H:i' ) ?></span><i class="fa fa-user" title="Разместил"></i><span><?= $model->user->login ?></span></div>
	<div style="clear:both"></div>
</div>
