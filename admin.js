jQuery( document ).ready( function ($) {
    $( '#cf-pdf-admin-form' ).on( 'submit', function(e){
        e.preventDefault();

        $spinner = $( '#cf-pdf-spinner' );
        $spinner.show().attr( 'aria-hidden', false ).css( 'visibility', 'visible' );
        $( '.cf-pdf-notice' ).hide().attr( 'aria-hidden', true ).css( 'visibility', 'none' );
       
        var data = {
            _nonce: $( '#cf-pdf-nonce' ).val(),
            action: 'cf_pdf_admin_save',
            key: $( '#cf-pdf-api-key').val()
        };

        $.post({
            url: ajaxurl,
            data:data,
            success: function (r) {
                $spinner.hide().attr( 'aria-hidden', true ).css( 'visibility', 'none' );
                $( '#cf-pdf-saved' ).show().attr( 'aria-hidden', false ).css( 'visibility', 'visible' );
            },
            error: function(){
                $( '#cf-pdf-not-saved' ).show().attr( 'aria-hidden', false ).css( 'visibility', 'visible' );
                $spinner.hide().attr( 'aria-hidden', true ).css( 'visibility', 'none' );
            }
        })
    });
});