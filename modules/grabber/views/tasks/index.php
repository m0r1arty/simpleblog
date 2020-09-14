<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Задачи';
?>
<div class="task-instances-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?php foreach ( $tasks as $task ): ?>
        <?= Html::a( $task->title, [ '/grabber/tasks/create/' . $task->task_id ], ['class' => 'btn btn-success']) ?>
        <?php endforeach; ?>
    </p>


    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'id',
            [
                'attribute' => 'task_id',
                'value' => function ( $model ) {
                    return $model->task->title;
                },
            ],
            [
                'attribute' => 'transport_id',
                'value' => function ( $model ) {
                    return $model->transport->title;
                },
            ],
            [
                'attribute' => 'parser_id',
                'value' => function ( $model ) {
                    return $model->parser->title;
                },
            ],
            'source',

            ['class' => 'yii\grid\ActionColumn', 'template' => '{update} {delete}' ],
        ],
    ]); ?>


</div>
