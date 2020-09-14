<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\modules\grabber\models\TaskInstances */

$this->title = 'Редактирование задачи';
$this->params['breadcrumbs'][] = ['label' => 'Задачи', 'url' => ['index']];
$this->params['breadcrumbs'][] = 'Редактирование';
?>
<div class="task-instances-update">

    <h2><?= Html::encode($this->title) ?></h2>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
