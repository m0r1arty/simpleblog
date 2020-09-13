<?php

use yii\helpers\Html;
use yii\widgets\ListView;
use yii\widgets\Pjax;
use app\modules\blog\helpers\Categories;
/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */
/* @var $catid(optional) int|null */

$this->title = 'Блог Бе3печного блоггеp@';
$this->params['breadcrumbs'][] = $this->title;

\app\assets\AppAsset::register($this);

$categoriesWidgetConfig = [
];

if ( isset( $catid ) ) {
	$catid = intval( $catid );
} else {
	$catid = 0;
}

$categoriesWidgetConfig[ 'categories' ] = Categories::categoriesForWidget( null, $catid );
$categoriesWidgetConfig[ 'viewList' ] = 'categories';
$categoriesWidgetConfig[ 'viewItem' ] = 'category';

?>
<?= \app\modules\blog\widgets\CategoriesWidget::widget( $categoriesWidgetConfig ) ?>
<div class="records-public">
	<?php echo ListView::widget([
	'dataProvider' => $dataProvider,
	'pager' => [
		'prevPageLabel' => '<i class="fa fa-arrow-left"></i>',
		'nextPageLabel' => '<i class="fa fa-arrow-right"></i>',
		'disableCurrentPageButton' => true,
	],
	'itemView' => '_listViewItem',
	'viewParams' => [ 'catid' => empty( $catid )? null : $catid ],
	'layout' => "{pager}\n{items}\n{pager}",
	]); ?>
</div>
