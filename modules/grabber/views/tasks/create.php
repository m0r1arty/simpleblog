<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\modules\grabber\models\TaskInstances */

$this->title = 'Создание задачи ' . $model->task->title;
$this->params['breadcrumbs'][] = ['label' => 'Задачи', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="task-instances-create">

    <h2><?= Html::encode($this->title) ?></h2>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
