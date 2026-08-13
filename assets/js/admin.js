( function ( $ ) {
	'use strict';

	var GATEWAY_SECTIONS = {
		twilio: '.byot-gateway-twilio',
		whatsapp: '.byot-gateway-whatsapp',
	};

	function toggleGatewaySections() {
		var selected = $( '#byot-gateway-select' ).val();

		$.each( GATEWAY_SECTIONS, function ( gateway, selector ) {
			$( selector ).toggle( gateway === selected );
		} );
	}

	$( function () {
		$( '#byot-gateway-select' ).on( 'change', toggleGatewaySections );
		toggleGatewaySections();
	} );
} )( jQuery );
