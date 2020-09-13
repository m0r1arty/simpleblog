<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;

use yii\helpers\Url;
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
        'pager' => [
            'prevPageLabel' => '<i class="fa fa-arrow-left"></i>',
            'nextPageLabel' => '<i class="fa fa-arrow-right"></i>',
            'disableCurrentPageButton' => true,
        ],
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
            [
                'class' => 'yii\grid\ActionColumn',
                'urlCreator' => function ( $action, $model, $key, $index, $urlCreator ) {
                    switch ( $action ) {
                        case 'view':
                            return $model->makeLink();
                            break;
                        default:
                        /**
                         * default action from [[\yii\grid\ActionColumn::createUrl]]
                         */
                            $params = is_array($key) ? $key : ['id' => (string) $key];
                            $params[0] = $urlCreator->controller ? $urlCreator->controller . '/' . $action : $action;
                            return Url::toRoute($params);
                    }
                },
            ],
        ],
    ]); ?>

    <?php Pjax::end(); ?>

</div>
