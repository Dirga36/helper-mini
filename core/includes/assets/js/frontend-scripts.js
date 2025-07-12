/*------------------------- 
Frontend related javascript
-------------------------*/

(function( $ ) {

	"use strict";

	$( document ).on( 'heartbeat-send', function( event, data ){
		// Add additional data to Heartbeat data.
		data.myplugin_customfield = 'some_data';
	});

	$( document ).on( 'heartbeat-tick', function( event, data ){
		// Check for our data, and use it.
		if( ! data.myplugin_customfield_hashed ){
			return;
		}
	
		alert( 'The hash is ' + data.myplugin_customfield_hashed );
	});

})( jQuery );
