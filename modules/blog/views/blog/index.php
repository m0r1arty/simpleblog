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
	'itemView' => '_listViewItem',
	'layout' => "{pager}\n{items}\n{pager}",
	]); ?>
</div>
