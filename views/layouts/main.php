<?php

/* @var $this \yii\web\View */
/* @var $content string */

use app\widgets\Alert;
use yii\helpers\Html;
use yii\bootstrap4\Nav;
use yii\bootstrap4\NavBar;
use yii\widgets\Breadcrumbs;
use app\assets\AppAsset;

AppAsset::register($this);

if ( Yii::$app->user->isGuest ) {
    $loginForm = $this->render( '//layouts/_login-form', [ 'loginModel' => $this->params[ 'loginModel' ] ] );
}

?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>">
<head>
    <meta charset="<?= Yii::$app->charset ?>">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?php $this->registerCsrfMetaTags() ?>
    <title><?= Html::encode($this->title) ?></title>
    <?php $this->head() ?>
</head>
<body>
<?php $this->beginBody() ?>

<div class="wrap">
    <?php

    $strLoginItem = '';
    if ( Yii::$app->user->isGuest ) {
        $strLoginItem .= Html::beginTag( 'li' );
        $strLoginItem .= Html::beginTag( 'i', [ 'class' => 'fa fa-sign-in-alt' ] );
        $strLoginItem .= Html::a( 'Вход', '#', [ 'id' => 'blog-sign-in-id' ] );
        $strLoginItem .= Html::endTag( 'i' );

        $strLoginItem .= $loginForm;

        $strLoginItem .= Html::endTag( 'li' );
    }else{
        $strLoginItem .= Html::beginTag( 'li' );
        $strLoginItem .= Html::beginTag( 'div', [ 'class' => 'input-group' ] );

        $strLoginItem .= Html::beginForm( '/blog/signout', 'post' );
        
        $strLoginItem .= Html::beginTag( 'i', [ 'class' => 'fa fa-sign-out-alt' ] );
        $strLoginItem .= Html::endTag( 'i' );

        $strLoginItem .= Html::submitButton( 'Выход', ['class' => 'btn btn-link logout'] );
        $strLoginItem .= Html::endForm();

        $strLoginItem .= Html::endTag( 'div' );
        
        $strLoginItem .= Html::endTag( 'li' );
    }

    NavBar::begin([
        'brandLabel' => Yii::$app->name,
        'brandUrl' => Yii::$app->homeUrl,
        'options' => [
            'class' => 'navbar navbar-expand-lg navbar-dark bg-dark',
        ],
    ]);
    echo Nav::widget([
        'options' => ['class' => 'navbar-nav mr-auto'],
        'items' => [
            $strLoginItem
        ],
    ]);
    NavBar::end();
    ?>

    <div class="container">
        <?= Breadcrumbs::widget([
            'links' => isset($this->params['breadcrumbs']) ? $this->params['breadcrumbs'] : [],
        ]) ?>
        <?= Alert::widget() ?>
        <?= $content ?>
    </div>
</div>

<footer class="footer">
    <div class="container">
        <p class="pull-left">&copy; M0r1 <?= date('Y') ?></p>
        <p class="pull-right"><?= Yii::powered() ?></p>
    </div>
</footer>

<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>
