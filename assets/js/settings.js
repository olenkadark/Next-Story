jQuery(document).ready(function($) {
    $.blockUI.defaults.overlayCSS.cursor     = 'default';
    $.blockUI.defaults.overlayCSS.background = '#fff';
    $.blockUI.defaults.overlayCSS.opacity    = 0.6;
    $.blockUI.defaults.message = null;
    var $wrapper = $('#u_next_story_settings');
    var $tb = $('#u_next_story_rules_table');

    /***** Select2 *****/
    $( document ).on( "u_init_select", function() {
        $( 'select.u-init-select:not(.u-inited)' ).select2().addClass('u-inited');
        $('#post_types').trigger('change');

    })
        .on('change', '#apply_styles', function (e) {
            if( $(this).is(':checked') ){
                $('#styles-options').show();
            }else{
                $('#styles-options').hide();
            }
        })
        .on('change', '#post_types', function (e) {
            $('.post_type_taxonomy').prop( "disabled", true );
            let post_types = $(this).val();
            if( post_types.length ){
                $.each(post_types, function (key,el){
                    $('.show_on_post_type_' + el).prop( "disabled", false );
                });
            }
            console.log(post_types);
        })
         .on('click', '#add_new_rule', function (e) {
             $wrapper.block();
             $.ajax({
                 type: 'POST',
                 url: uns_settings_params.ajax_url,
                 dataType: "html",
                 data: { action: "u_next_story_add_new_rule", security: uns_settings_params.default_nonce },
             })
              .done(function(response) {
                  $('tr', $tb).show();
                  $('.no-items', $tb).hide();

                  if( $('.edit-rule', $tb).length ){
                      $('.edit-rule', $tb).remove();
                  }

                  $('tbody', $tb).append(response);
                  $( document ).trigger( "u_init_select");
                  $('.color').wpColorPicker();
              })
              .fail(function(response) {
                  console.log("error");
              })
              .always(function(response) {
                  $wrapper.unblock();
              });
         })
         .on('click', '.u-cancel-edit', function (e) {
             var $tr = $(this).closest('tr');
             var ruleid = $tr.data('ruleid');

             var $rule_raw = $('tr.u-rule-raw[data-ruleid="' + ruleid + '"]').length ? $('tr.u-rule-raw[data-ruleid="' + ruleid + '"]') : false;

             if( $rule_raw !== false){
                 $rule_raw.show();
             }

             $('.edit-rule', $tb).remove();

         })
         .on('click', '.uns-edit-rule', function (e) {
             var $tr = $(this).closest('tr');
             var ruleid = $tr.data('ruleid');

             $wrapper.block();
             $.ajax({
                 type: 'POST',
                 url: uns_settings_params.ajax_url,
                 dataType: "html",
                 data: {
                     action: "u_next_story_edit_rule",
                     security: uns_settings_params.default_nonce,
                     rule_id: ruleid
                 },
             })
              .done(function(response) {
                  $('tbody tr', $tb).not('.no-items').show();

                  if( $('.edit-rule', $tb).length ){
                      $('.edit-rule', $tb).remove();
                  }

                  $tr.hide().after(response);

                  $( document ).trigger( "u_init_select");
                  $('.color').wpColorPicker();
              })
              .fail(function(response) {
                  console.log("error");
              })
              .always(function(response) {
                  $wrapper.unblock();
              });
         })
         .on('click', '.uns-delete-rule', function (e) {
             var $tr = $(this).closest('tr');
             var ruleid = $tr.data('ruleid');

             $wrapper.block();
             $.ajax({
                 type: 'POST',
                 url: uns_settings_params.ajax_url,
                 dataType: "json",
                 data: {
                     action: "u_next_story_delete_rule",
                     security: uns_settings_params.default_nonce,
                     rule_id: ruleid
                 },
             })
              .done(function(response) {
                  $tr.remove();

                  if( !$('tbody tr', $tb).not('.no-items').length ){
                      $('.no-items', $tb).show();
                  }
              })
              .fail(function(response) {
                  console.log("error");
              })
              .always(function(response) {
                  $wrapper.unblock();
              });
         })
        .on('submit', '#edit-rule-form', function (e) {

            var data = $( this ).serialize();
            var $tr = $(this).closest('tr');
            var ruleid = $tr.data('ruleid');

            var $rule_raw = $('tr.u-rule-raw[data-ruleid="' + ruleid + '"]').length ? $('tr.u-rule-raw[data-ruleid="' + ruleid + '"]') : $tr;

            $wrapper.block();
            $.ajax({
                type: 'POST',
                url: uns_settings_params.ajax_url,
                dataType: "html",
                data: data + '&action=u_next_story_save_rule&security='+uns_settings_params.default_nonce
            })
                .done(function(response) {
                    $rule_raw.replaceWith(response);
                    $('.edit-rule', $tb).remove();
                })
                .fail(function(response) {
                    console.log("error");
                })
                .always(function(response) {
                    $wrapper.unblock();
                });
            return false;
        });
    if( $( 'select.u-init-select:not(.u-inited)' ).length > 0 ) {
        $( document ).trigger( "u_init_select");
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
