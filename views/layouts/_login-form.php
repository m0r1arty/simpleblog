<?php

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;

$form = ActiveForm::begin([
	'action' => [ \yii\helpers\Url::to( [ '/default/signin' ] ) ],
	'id' => 'login-form',
	'layout' => 'inline',
	'fieldConfig' => [
	],
]);

echo '<div class="input-group input-group-sm">';

/**
	'tag' => false чтобы yii\widgets\ActiveField не генерировал div враппер, который ломает отображение input-group-sm
*/
echo $form->field( $loginModel, 'username', [
	'inputTemplate' => '<div class="input-group-prepend"><span class="input-group-text">Login</span></div>{input}',
	'inputOptions' =>
	[
		'placeholder' => 'Login',
	],
	'options' =>
	[
		'tag' => false,
	],
	'template' => '{input}',
] )->textInput();

echo $form->field( $loginModel, 'password', [
	'inputTemplate' => '<div class="input-group-prepend"><span class="input-group-text">Password</span></div>{input}',
	'inputOptions' =>
	[
		'placeholder' => 'Password',
	],
	'options' =>
	[
		'tag' => false,
	],
	'template' => '{input}',
] )->passwordInput();

echo Html::beginTag( 'button', [ 'style' => 'margin: 0 2px; border:transparent; border-radius: 5px;' ] );
echo "Вход";
echo Html::endTag( 'button' );

echo '</div>';

ActiveForm::end();
