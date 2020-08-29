<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\modules\blog\models\Records */

$this->title = $model->title;
\yii\web\YiiAsset::register($this);
?>
<div class="records-view">

    <h1><?= Html::encode($this->title) ?></h1>
<?php if ( !Yii::$app->user->isGuest ): ?>
<?php

    $strEditContentLink = Html::beginTag( 'span', [ 'class' => 'glyphicon glyphicon-pencil', 'title' => 'Редактировать' ] );
    $strEditContentLink .= Html::endTag( 'span' );

    $strDeleteContentLink = Html::beginTag( 'span', [ 'class' => 'glyphicon glyphicon-trash', 'title' => 'Удалить' ] );
    $strDeleteContentLink .= Html::endTag( 'span' );
?>
    <p>
        <?= Html::a($strEditContentLink, ['update', 'id' => $model->record_id]) ?>
        <?= Html::a($strDeleteContentLink, ['delete', 'id' => $model->record_id], [
            'data' => [
                'confirm' => 'Удалить запись?',
                'method' => 'post',
            ],
        ]) ?>
    </p>
<?php endif; ?>

<div class="preview"><?= $model->preview ?></div>
<div class="content"><?= $model->content ?></div>
<div class="content footer">
    <i class="fa fa-calendar-alt" title="<?= ( $model->created_at === $model->updated_at )? 'Дата публикации' : 'Дата публикации(редактировано)' ?>"></i><span><?= $model->date ?></span>
    <i class="fa fa-user" title="Разместил"></i><span><?= $model->user->login ?></span>
</div>

</div>
