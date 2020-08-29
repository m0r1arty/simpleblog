<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;
/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Записи';
$this->params['breadcrumbs'][] = $this->title;

\app\assets\AppAsset::register($this);
?>
<div class="records-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Создать', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php Pjax::begin(); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
            'record_id',
            'title',
            'slug',
            [
                'attribute' => 'created_at',
                'label' => 'Создана',
                'value' => function( $model )
                {
                    /* @var $model \app\modules\blog\models\Records */
                    return $model->dateCreated;
                },
            ],
            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>

    <?php Pjax::end(); ?>

</div>
