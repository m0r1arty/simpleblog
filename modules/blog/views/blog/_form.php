<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

use mihaildev\ckeditor\CKEditor;

/* @var $this yii\web\View */
/* @var $model app\modules\blog\models\Records */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="records-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'title')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'preview')->widget(
        CKEditor::className(), [
            'editorOptions' => [
                'preset' => 'full',
                'inline' => false,
            ],
        ]
    ) ?>

    <?= $form->field($model, 'content')->widget(
        CKEditor::className(), [
            'editorOptions' => [
                'preset' => 'full',
                'inline' => false,
            ],
        ]
    ) ?>

    <?= $form->field($model, 'slug')->textInput(['maxlength' => true]) ?>

    <div class="form-group">
        <?= Html::submitButton('Сохранить', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
