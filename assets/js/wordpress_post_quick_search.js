/*! WordPress Post Quick Search - v0.1.0
 * http://wordpress.org/plugins
 * Copyright (c) 2014; * Licensed GPLv2+ */
/**
 * WordPress Post Quick Search
 * http://wordpress.org/plugins
 *
 * Copyright (c) 2014 Adam Silverstein
 * Licensed under the GPLv2+ license.
 */

( function( $ ) {
	var postSearch = {
		init: function(){
			var searchfield = $( '#search-submit' ),
				position = { my : "left top", at: "left bottom" };

			searchfield.after( '<input type="search" style="float:right;" value="" placeholder="Quick Search" name="s" id="ajaxy-post-search-input">');

			if ( typeof isRtl !== 'undefined' && isRtl ) {
				position.my = 'right top';
				position.at = 'right bottom';
			}
			$( '#ajaxy-post-search-input' ).autocomplete( {
				delay:     500,
				minLength: 2,
				position:  position,
				source:    ajaxurl + '?action=post_search&typenow=' + typenow + '&_ajax_nonce=' + _ajax_nonce,
				select: function( event, ui ) {
					if ( 'undefined' !== typeof ui.item.value ){ /* Check URL passed */
						document.location = ui.item.value.replace( /&amp;/g, '&' );
					}
					event.preventDefault(); /* Prevent selection from inserting label */
				},
				focus: function( event, ui ){
					$( this ).attr( 'value', ui.item.label ); /* Copy label to input */
					event.preventDefault(); /* Prevent navigation from inserting value */
				}
			} );
		}
	};
	$( document ).ready( function(){ 
		postSearch.init(); 
	} );
} )( jQuery );
