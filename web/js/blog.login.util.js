
window.addEventListener( 'load', function()
{
	let handleEscapeCallback = function( evt )
	{
		/**
			Ловим escape
		*/

		if ( evt.which === 27 )
		{
			$( "div.wrap > nav ul li form" ).hide();
			$( "div.wrap > nav ul li i.fa.fa-sign-in-alt" ).show();
		}
	};
	let handleLoginClickCallback = function( evt )
	{
		$( "div.wrap > nav ul li i.fa.fa-sign-in-alt" ).hide();

		$( "div.wrap > nav ul li form" ).show();
		$( "div.wrap > nav ul li form input" ).each(
			function()
			{
				if( [ 'text', 'password' ].indexOf( $( this ).attr( 'type' ) ) !== -1 )
				{
					$( this ).val( '' );
				}
			}
		);

		evt.preventDefault();
		evt.stopPropagation();
	};

	$( "div.wrap > nav ul li i.fa.fa-sign-in-alt a" ).click( handleLoginClickCallback );
	$( "div.wrap > nav ul li form input" ).keydown( handleEscapeCallback );
} );