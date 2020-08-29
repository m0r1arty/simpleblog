<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\modules\blog\models\Records */

$this->title = 'Добавление записи';
$this->params['breadcrumbs'][] = ['label' => 'Записи', 'url' => ['list']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="records-create">

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
