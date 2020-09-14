<?php

/**
 */

namespace app\modules\blog\traits;

use Yii;
use yii\helpers\Url;

use app\modules\blog\models\Categories;

/**
 */
 trait RetriveCategoriesTrait
 {
    /**
     * Формирует и возвращает массив категорий(ассоциативный массив).
     * В случае, если текущая запись связана с какой-то категорией - она выделяется статусом 'a' - active.
     * @param int[] $ids - массив идентификаторов категорий к которым привязана запись
     * @see [[app\modules\blog\widgets\CategoriesWidget::categories]]
     * @return array[] массив элементами которого являются ассоциативные массивы
     */
    public function traitCategoriesForWidget( $ids )
    {
        $ret = [];

        /* @var \app\modules\blog\models\Categories $models */
        $models = Categories::find()->all();

        if ( $this->isNewRecord ) {
            foreach ( $models as $category ) {
                $ret[] = [
                    'id' => $category->category_id,
                    'title' => $category->title,
                    'link' => Url::to( [ '/blog/blog/index', 'catid' => $category->category_id ] ),
                    'status' => 'd',
                ];
            }
        } else {
            foreach ( $models as $category ) {
                $ret[] = [
                    'id' => $category->category_id,
                    'title' => $category->title,
                    'link' => Url::to( [ '/blog/blog/index', 'catid' => $category->category_id ] ),
                    'status' => ( ( in_array( $category->category_id, $ids ) ) ?  'a' : 'd' ),
                ];
            }
        }

        return $ret;
    }
 }
