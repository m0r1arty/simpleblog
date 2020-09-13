<?php

/**
 * Файл содержит asset для модуля blog.
 * @author M0r1arty <m0r1arty.nv@yandex.ru>
 */

namespace app\modules\blog\assets;

use Yii;

use yii\web\AssetBundle;

/**
 * Класс BlogAsset
 */
class BlogAsset extends AssetBundle
{
	public $sourcePath = '@blog/assets/sources';
	public $css = [
		'css/blog.css',
	];
	public $js = [
		'js/categories.widget.asset.js',
	];
	public $depends = [
		'rmrevin\yii\fontawesome\CdnFreeAssetBundle',
		'app\assets\AppAsset',
	];
}
