
/*
    * Property Actions Script
    * Handles AJAX requests for property actions like duplicate, featured, etc.
    * Updates the UI based on the response from the server.
    * 
    * @package wpresidence-core
    * @since 1.0.0
*/

(function ($) {
    'use strict';

    $(document).ready(function () {
        $(document).on('click', '.wpresidence_properties_action_admin', function (e) {
            e.preventDefault();

            var $button = $(this);
            var action = $button.data('action');
            var postId = $button.data('postid');
            var currentButtons  = $button.parents('.wpestate_admin_actions_wrapper').html();

            if (action && postId) {
                $button.addClass('.wpresidence_actions_loader');
                $.ajax({
                    url: wpestate_crm_script_vars.ajaxurl,
                    type: 'POST',
                    data: {
                        action: 'wp_estate_handle_property_action',
                        action_type: action,
                        post_id: postId,
                        // _wpnonce: wpestate_ajax_object.nonce
                    },
                    success: function (response) {
                       
                        $button.removeClass('.wpresidence_actions_loader');
                        if (response.success) {
                            // Update the buttons with the new state
                            if ( action === 'duplicate' ) {
                                // Refresh the page if the action was duplicate
                                location.reload();
                            }
                            if ( action == 'featured' ) {
                                $button.parents('.type-estate_property').find('.estate_featured').text(response.data.featured_text);
                            }
                            if ( action == 'sold' ) {
                                if ( $button.parents('.type-estate_property').find('.status_label.status_label_mark_as_sold ').length > 0 ) {
                                    $button.parents('.type-estate_property').find('.status_label.status_label_mark_as_sold ').remove();
                                } else {
                                    jQuery( '<span class="status_label status_label_mark_as_sold ">' + response.data.status_text + '</span>' ).insertAfter( $button.parents('.type-estate_property').find('.status_label') );
                                }
                            } else if ( action == 'approve' || action == 'disapprove' || action == 'on-hold' ||  action == 'expire'  ) {
                                $button.parents('.type-estate_property').find('.status_label:first').text(response.data.status_text);
                                $button.parents('.type-estate_property').find('.status_label:first').attr('class', 'status_label ' + response.data.status_class);
                            }
                            $button.parents('.wpestate_admin_actions_wrapper').html(response.data.buttons);
                        } else {
                            // If the action failed, revert to the original buttons and show an error message
                            $button.parents('.wpestate_admin_actions_wrapper').html(currentButtons);
 
                            $button.parents('.wpestate_admin_actions_wrapper').append('<div class="error-message">' + response.data.message + '</div>');
                        }
                    },
                    error: function () {
                        // If the AJAX request fails, revert to the original buttons and show an error message
                        $button.parents('.wpestate_admin_actions_wrapper').html(currentButtons);
                              $button.removeClass('.wpresidence_actions_loader');
                        $button.parents('.wpestate_admin_actions_wrapper').append('<div class="error-message">An error occurred. Please try again.</div>');
                    }
                });
            }
        });
    });

})(jQuery);