<?php

use yii\helpers\Html;
use yii\widgets\ListView;
use yii\widgets\Pjax;
/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Блог Бе3печного блоггеp@';
$this->params['breadcrumbs'][] = $this->title;

\app\assets\AppAsset::register($this);
?>
<div class="records-public">
	<?php echo ListView::widget([
	'dataProvider' => $dataProvider,
	'pager' => [
		'prevPageLabel' => '<i class="fa fa-arrow-left"></i>',
		'nextPageLabel' => '<i class="fa fa-arrow-right"></i>',
		'disableCurrentPageButton' => true,
	],
	'itemView' => '_listViewItem',
	'layout' => "{pager}\n{items}\n{pager}",
	]); ?>
</div>
