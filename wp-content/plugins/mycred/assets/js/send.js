/**
 * myCRED Points for Link Clicks jQuery Scripts
 * @since 0.1
 * @version 1.0
 */
jQuery(function($) {
	var mycred_send = function( button, label ) {
		$.ajax({
			type : "POST",
			data : {
				action    : 'mycred-send-points',
				amount    : button.attr( 'data-amount' ),
				recipient : button.attr( 'data-to' ),
				log       : button.attr( 'data-log' ),
				reference : button.attr( 'data-ref' ),
				token     : myCREDsend.token
			},
			dataType : "JSON",
			url : myCREDsend.ajaxurl,
			// Before we start
			beforeSend : function() {
				button.attr( 'value', myCREDsend.working );
				button.attr( 'disabled', 'disabled' );
			},
			// On Successful Communication
			success    : function( data ) {
				// Transfer complete
				if ( data == 'done' ) {
					button.attr( 'value', myCREDsend.done );
					setTimeout( function(){
						button.attr( 'value', label );
						button.removeAttr( 'disabled' ); }, 2000 );
				}
				// Transfer complete but now account has reached zero
				else if ( data == 'zero' ) {
					// Loop though each button disable and hide
					$('.mycred-send-points-button').each(function(){
						$(this).attr( 'disabled', 'disabled' );
						$(this).hide();
					});
				}
				// Transfer complete but this amount can not be sent again
				else if ( data == 'minus' ) {
					// Loop though each button
					$('.mycred-send-points-button').each(function(){
						// If amount is larger or equal to this buttons amount disable and hide
						if ( $(this).attr( 'data-amount' ) && $(this).attr( 'data-amount' ) >= button.attr( 'data-amount' ) ) {
							$(this).attr( 'disabled', 'disabled' );
							$(this).hide();
						}
					});
				}
			},
			// Error (sent to console)
			error      : function( jqXHR, textStatus, errorThrown ) {
				button.attr( 'value', myCREDsend.error );
				button.removeAttr( 'disabled' );
				// Debug - uncomment to use
				//console.log( jqXHR );
			}
		});
	};
	
	$('.mycred-send-points-button').click(function(){
		mycred_send( $(this), $(this).attr( 'value' ) );
	});
});