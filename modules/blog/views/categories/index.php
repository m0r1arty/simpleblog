<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Категории';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="categories-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Создать категорию', ['create'], ['class' => 'btn btn-success']) ?>
    </p>


    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'pager' => [
            'prevPageLabel' => '<i class="fa fa-arrow-left"></i>',
            'nextPageLabel' => '<i class="fa fa-arrow-right"></i>',
            'disableCurrentPageButton' => true,
        ],
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'category_id',
            'title',
            'slug',

            ['class' => 'yii\grid\ActionColumn', 'template' => '{update} {delete}'],
        ],
    ]); ?>


</div>
