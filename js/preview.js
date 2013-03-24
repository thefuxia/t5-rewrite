/**
 * Preview scripts for T5 Rewrite
 * 
 * @version 2013.03.24
 * @since   2013.03.24
 * @author  toscho
 */
jQuery( function( $ ) {
	"use strict";
	var structTable = $( 'form[action="options-permalink.php"] table:first' );
	$( structTable ).append( 
		'<tr><th>' + t5PermalinkPreview.label + '</th>'
		+ '<td id="t5preview" class="code"></td></tr>' 
	);
	var previewCell = $( '#t5preview' ),
		tagInput    = $( '#permalink_structure' ),
		$stored     = [],
		$val        = '',

		getStruct = function() {
			var val = $('input[name=selection]:checked', structTable ).val();

			if ( 'custom' == val )
				return $.trim( $( tagInput ).val() );
			
			if ( '' == val )
				return '_'; // we cannot use an empty string as key in $stored
			
			return val;
		},

		handleResult = function( response ) {
		
			$( previewCell ).html( response )
				.css( 'background', '#ff9' )
				.animate( { opacity: 0.25 }, 500, "linear", function() {
					$(this).animate( { opacity: 1 }, 500 )
						.css( 'background', 'transparent' );
				});
			$stored[ $val ] = response;
		},

		updatePreview = function() {
			
			var $val = getStruct();
	
			if ( $stored[ $val ] ) {
				$( previewCell ).html( $stored[ $val ] );
				return;
			}

			var postParams = {
				action: 'permalink_preview',
				struct: $val
			};

			$.post( ajaxurl, postParams, handleResult );
		};

	$( tagInput ).on( 'change paste keyup mouseleave', updatePreview );
	$( window ).on( 'load', updatePreview );
	$( structTable ).find( 'input' ).on( 'click', updatePreview );
});