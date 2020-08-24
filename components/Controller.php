<?php

namespace app\components;

use Yii;
use yii\web\Controller as WebController;

use app\models\LoginForm;

class Controller extends WebController
{
	protected $viewParams = [];

	public function init()
	{
		parent::init();

		if( Yii::$app->user->isGuest )
		{
			$this->view->params[ 'loginModel' ] = new LoginForm();
		}
	}
}