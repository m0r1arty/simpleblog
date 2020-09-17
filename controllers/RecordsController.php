<?php

/**
 */
namespace app\controllers;

use Yii;

use yii\rest\ActiveController;
use yii\filters\auth\HttpBasicAuth;

/**
 */
class RecordsController extends ActiveController
{
	public $modelClass = 'app\modules\blog\models\Records';

	public function behaviors()
	{
		return array_merge(
			parent::behaviors(),
			[
				'authenticator' => [
					'class' => HttpBasicAuth::className(),
				],
			]
		);
	}
}
