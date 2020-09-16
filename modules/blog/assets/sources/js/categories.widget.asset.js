class CategoriesWidget
{
	constructor( container )
	{
		let self = this;

		/*
			Контейнер виджета
		*/
		self._container = container;
		/*
			Для ActiveField должен быть input(для других случаев данный JS не нужен)
		*/
		self._input = container.find( 'input.categories-widget-input' );
		/*
			Массив, чтобы хранить id выбранных категорий
		*/
		self._activeIDs = new Array();
		/*
			Имена CSS классов для default/active/error итемов категорий
		*/

		let cssClass = self._container.attr( 'data-css-default' );

		if ( cssClass === undefined ) {
			self._defaultClass = 'default';
		} else {
			self._defaultClass = cssClass;
		}

		cssClass = self._container.attr( 'data-css-active' );

		if ( cssClass === undefined ) {
			self._activeClass = 'active';
		} else {
			self._activeClass = cssClass;
		}

		cssClass = self._container.attr( 'data-css-error' );

		if ( cssClass === undefined ) {
			self._errorClass = 'error';
		} else {
			self._errorClass = cssClass;
		}

		/*
			Остальная инициализация
		*/
		self.init();
	}

	/*
		Дополнительная инициализация. Установка обработчика click, определение самого обработчика.
		Заполнение массива self._activeIDs.
	*/
	init()
	{
		let self = this;

		/*
			Обработчик клика по итему категории. Управляет логикой "вкл-выкл". Результат отображается на массиве self._activeIDs и значении input элемента.
			Игнорирует клики по error итемам.
		*/
		self._clickCallback = function()
		{
			let id = parseInt( $( this ).attr( 'data-id' ) );

			if ( id > 0 ) {
				let ind = self._activeIDs.indexOf( id );
				let err = false;

				if ( $( this ).hasClass( self._errorClass ) ) {
					err = true;
				}

				/*
					Хоть это и маловероятно. Может получиться так, что виджет подгрузит категорию с классом error(каким-то образом
					удалось привязать категорию с таким чпу, которое конфликтует с деревом sef). Тогда надо позволить отвязать категорию,
					но снова привязывать давать нельзя.
				*/
				if( ind === -1 && !err ) {
					self._activeIDs.push( id );

					$( this ).removeClass( self._defaultClass ).addClass( self._activeClass );
				} else if( ind !== -1 ) {

					self._activeIDs.splice( ind, 1 );

					$( this ).removeClass( self._activeClass ).addClass( self._defaultClass );
				}

				self._input.val( ( self._activeIDs.length > 0 ) ? self._activeIDs.join( ',' ) : '' );
			}
		};

		/*
			Проставляем обработчик кликов на все итемы текущего контейнера
		*/
		self._container.find( '.categories-widget-item' ).click( self._clickCallback );

		/*
			Значение input элемента может изначально содержать идентификаторы включённых итемов, разделённый запятыми.
		*/
		let currentIDs = self._input.val().split( ',' );

		currentIDs.forEach(
			function( id )
			{
				let ind;
				id = parseInt( id.trim() );

				if( id > 0 )
				{
					ind = self._activeIDs.indexOf( id )
					
					if ( ind === -1 ) {
						self._activeIDs.push( id );
					}
				}
			}
		);
	}

	/*
		Возвращает количество выбранных категорий
	*/
	countActive()
	{
		return this._activeIDs.length;
	}
}

window.addEventListener( 'load', function() {
	window.categoriesIDs = new Map();
	$( '.categories-widget' ).each(
		function()
		{
			let id = $( this ).find( "input:first" ).attr( "id" );

			if ( id !== undefined ) {
				window.categoriesIDs[ id ] = new CategoriesWidget( $( this ) );
			}
		}
	);
} );
