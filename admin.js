
jQuery( document ).ready( function ($) {
    $( '#cf-pdf-admin-form' ).on( 'submit', function(e){
        e.preventDefault();

        $spinner = $( '#cf-pdf-spinner' );
        $spinner.show().attr( 'aria-hidden', false ).css( 'visibility', 'visible' );
        $( '.cf-pdf-notice' ).hide().attr( 'aria-hidden', true ).css( 'visibility', 'none' );
       


        data = $(this).serialize();
console.log( data );
        $.post({
            url: ajaxurl,
            data:data,
            success: function (r) {
                if( 0 == r ){
                    fail($);
                }else{
                    $spinner.hide().attr( 'aria-hidden', true ).css( 'visibility', 'none' );
                    $( '#cf-pdf-saved' ).show().attr( 'aria-hidden', false ).css( 'visibility', 'visible' );
                }

            },
            error: function(){
                fail($);
            }
        });

        function fail($) {
            $('#cf-pdf-not-saved').show().attr('aria-hidden', false).css('visibility', 'visible');
            $spinner.hide().attr('aria-hidden', true).css('visibility', 'none');
        }
    });
});