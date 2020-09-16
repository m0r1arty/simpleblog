<?php

/**
 */

namespace app\modules\grabber\parsers;

use Yii;

use app\modules\grabber\base\BaseParser;

use app\modules\grabber\interfaces\GetLinksInterface;
use app\modules\grabber\interfaces\NextSourceInterface;

use app\modules\grabber\exceptions\LinksNotFoundException;
use app\modules\grabber\exceptions\ContentNotFoundException;

/**
 */
 class ChelseaBluesParser extends BaseParser implements GetLinksInterface, NextSourceInterface
 {
 	public static function parserTitle()
 	{
 		return 'Парсер ChelseaBlues';
 	}

 	public function parse( $data )
 	{
 		$q = \phpQuery::newDocument( $data );

 		$ret = [];

 		/**
 		 * Содержимое новости содержится в блоке с id == entry_block
 		 */
 		$contentBlock = $q->find( 'div#entry_block' );

 		if ( $contentBlock->count() !== 1 ) {
 			throw new ContentNotFoundException();
 		}

 		/**
 		 * Первый потомок должен быть h1
 		 */
 		$firstChild = $contentBlock->children()->get( 0 );

 		if ( $firstChild->tagName !== 'h1' ) {
 			throw new ContentNotFoundException();
 		}

 		$ret[ 'title' ] = $firstChild->textContent;

 		/**
 		 * Текстовое содержимое новости должно быть внутри блока div.text_block.
 		 * В основном это <p> но может содержать и изображение
 		 * Первый p содержит preview, но нужно избавится от <strong>
 		 */
 		$content = $contentBlock->find( 'div.text_block' );

 		if ( $content->count() !== 1 ) {
 			throw new ContentNotFoundException();
 		}

 		$preview = $content->children()->get( 0 );
 		$strong = pq( $preview )->find( 'strong' );

 		if( $strong->count() !== 1 )
 		{
 			throw new ContentNotFoundException();
 		}

 		$ret[ 'preview' ] = $strong->html();

 		$ret[ 'content' ] = '';

 		$count = $content->children()->count();

 		for ( $i = 1; $i < $count; $i++ ) {
 			$ret[ 'content' ] .= pq( $content->children()->get( $i ) )->html();
 		}

 		return $ret;
 	}

 	/**
 	 * {@inheritdoc}
 	 */
 	public function getNextSource( $data )
 	{
 		$q = \phpQuery::newDocument( $data );
 		/**
 		 * Блок ссылок на страницы находится в контейнере div.catPages1
 		 */
 		$pages = $q->find( 'div.catPages1' );
 		$links = pq( 'a.swchItem', $pages );

 		/**
 		 * Ссылок не найдено
 		 */
 		if ( count( $links ) === 0 ) {
 			return false;
 		}

 		/**
 		 * Возьмём href последней ссылки
 		 */
 		$href = pq( $links->get( $links->count() - 1 ) )->attr( 'href' );

 		$nextLinks = pq( "a.swchItem[href={$href}" );

 		/**
 		 * Количество таких ссылок должно быть 2 - одна с цифрой страницы + одна со стрелкой "next".
 		 */
 		if ( $nextLinks->count() !== 2 ) {
 			return false;
 		}

 		if ( $href[ 0 ] !== '/' ) {
 			$href = '/' . $href;
 		}
 		
 		return 'https://chelseablues.ru' . $href;
 	}

 	/**
 	 * {@inheritdoc}
 	 */
 	public function getLinks( $data )
 	{
 		$q = \phpQuery::newDocument( $data );
 		/**
 		 */
 		$itemContainer = $q->find( 'div#allEntries' );

 		if ( $itemContainer->count() !== 1 ) {
 			throw new LinksNotFoundException();
 		}

 		$retLinks = [];

 		/**
 		 * Ссылки на страницы с контентом содержаться в прямых потомках div узла-контейнера. Последний div должен содержать ссылки.
 		 */
 		$items = pq( $itemContainer )->children();

 		foreach ( $items as $item ) {
 			$id = pq( $item )->attr( "id" );

 			/**
 			 * Если атрибута id нет - или это блок ссылок на страницы, или беда.
 			 */
 			if ( is_null( $id ) ) {
 				break;
 			}

 			/**
 			 * У блоков, которые содержат ссылку на контент id выглядит "entryID62366". Номер уменьшается сверху вниз - можно выбрать точкой опоры.
 			 */
 			if ( preg_match( '/^entryID([0-9]+)$/', $id, $matches ) ) {
 				$contentId = $matches[ 1 ];

 				$links = pq( $item )->find( 'a' );

 				/**
 				 * Ссылок должно быть 2
 				 */
 				if ( $links->count() === 2 ) {
 					$href1 = pq( $links->get( 0 ) )->attr( 'href' );
 					$href2 = pq( $links->get( 1 ) )->attr( 'href' );

 					/**
 					 * И содержимое href у них должно быть одинаковое
 					 */

 					if ( $href1 === $href2 ) {

 						if ( $href1[ 0 ] !== '/' ) {
 							$href1 = '/' . $href1;
 						}

 						$retLinks[ $contentId ] = 'https://chelseablues.ru' . $href1;
 					}
 				}
 			}
 		}
 		
 		if ( count( $retLinks ) === 0 ) {
 			throw new LinksNotFoundException();
 		}

 		return $retLinks;
 	}
 }
