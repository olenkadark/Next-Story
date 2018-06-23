jQuery(document).ready(function($) {
console.log('dddd');
    /***** Select2 *****/
    $( document.body ).on( "u_init_select", function() {
        $( 'select.u-init-select:not(.u-inited)' ).select2().addClass('u-inited');
    })
        .on('change', '#apply_styles', function (e) {
            if( $(this).is(':checked') ){
                $('#styles-options').show();
            }else{
                $('#styles-options').hide();
            }
        })
        .on('click', '#add_new_rule', function (e) {

        });
    if( $( 'select.u-init-select:not(.u-inited)' ).length > 0 ) {
        $( document.body ).trigger( "u_init_select");
    }

    /***** Colour picker *****/

    $('.color').wpColorPicker();


    /***** Uploading images *****/

    var file_frame;

    jQuery.fn.uploadMediaFile = function( button, preview_media ) {
        var button_id = button.attr('id');
        var field_id = button_id.replace( '_button', '' );
        var preview_id = button_id.replace( '_button', '_preview' );

        // If the media frame already exists, reopen it.
        if ( file_frame ) {
          file_frame.open();
          return;
        }

        // Create the media frame.
        file_frame = wp.media.frames.file_frame = wp.media({
          title: jQuery( this ).data( 'uploader_title' ),
          button: {
            text: jQuery( this ).data( 'uploader_button_text' ),
          },
          multiple: false
        });

        // When an image is selected, run a callback.
        file_frame.on( 'select', function() {
          attachment = file_frame.state().get('selection').first().toJSON();
          jQuery("#"+field_id).val(attachment.id);
          if( preview_media ) {
            jQuery("#"+preview_id).attr('src',attachment.sizes.thumbnail.url);
          }
          file_frame = false;
        });

        // Finally, open the modal
        file_frame.open();
    }

    jQuery('.image_upload_button').click(function() {
        jQuery.fn.uploadMediaFile( jQuery(this), true );
    });

    jQuery('.image_delete_button').click(function() {
        jQuery(this).closest('td').find( '.image_data_field' ).val( '' );
        jQuery(this).closest('td').find( '.image_preview' ).remove();
        return false;
    });

});