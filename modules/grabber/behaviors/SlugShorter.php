<?php

/**
 */
namespace app\modules\grabber\behaviors;

use Yii;
use yii\base\Behavior;
use yii\db\ActiveRecord;
/**
 */
class SlugShorter extends Behavior
{
	public $max = 0;
	public $shortTo = 0;
	public $slugAttribute = 'slug';

	public function events()
	{
		return [
			ActiveRecord::EVENT_BEFORE_VALIDATE => 'shortSlug',
		];
	}

	public function shortSlug( $event )
	{
		$model = $event->sender;
		
		$slug = $model->attributes[ $this->slugAttribute ];

		if ( strlen( $slug ) > $this->max ) {
			$model->setAttribute( $this->slugAttribute, substr( $slug, 0, $this->shortTo ) );
		}
	}
}
