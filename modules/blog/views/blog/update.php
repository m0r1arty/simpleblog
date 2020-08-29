<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\modules\blog\models\Records */

$this->title = 'Редактирование записи: ' . $model->title;
$this->params['breadcrumbs'][] = ['label' => 'Записи', 'url' => ['list']];
$this->params['breadcrumbs'][] = ['label' => $model->title];
?>
<div class="records-update">

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
