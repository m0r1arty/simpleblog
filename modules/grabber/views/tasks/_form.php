<?php

use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

use app\modules\blog\widgets\CategoriesWidget;

/* @var $this yii\web\View */
/* @var $model app\modules\grabber\models\TaskInstances */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="task-instances-form">

    <?php $form = ActiveForm::begin(); ?>
    <?= $form->field($model, 'categoryIDs')->widget( CategoriesWidget::className(), [
        'categories' => \app\modules\blog\helpers\Categories::categoriesForWidget( $model ),
    ] ) ?>
    <?= $form->field( $model, 'source' )->textInput() ?>
    <?= $form->field($model, 'transport_id')->dropDownList( ArrayHelper::map( $model->transports, 'transport_id', 'title' ) ) ?>

    <?= $form->field( $model, 'parser_id' )->dropDownList( ArrayHelper::map( $model->parsers, 'parser_id', 'title' ) ) ?>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
