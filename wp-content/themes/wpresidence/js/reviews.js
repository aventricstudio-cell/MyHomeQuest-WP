/* * Reviews.js
 * This script handles the functionality for editing and submitting property reviews.
 * It uses jQuery to manage AJAX requests for updating and posting reviews.
 * It also updates the UI to reflect the status of these actions.   
 * 
 * */

jQuery(document).ready(function ($) {
    "use strict";

    wpestate_enable_star_action();   // Star rating actions (hover and click events)
    wpestate_edit_review();          // Edit property reviews
    wpestate_submit_review();        // Submit new reviews

});

/**
 * Star Rating Interactions
 *
 * Handles user interactions with star ratings, including hover effects and click events.
 */
function wpestate_enable_star_action() {
    const stars = jQuery('.empty_star');  // Select all star elements

    // Handle hover effect for selecting stars
    stars.on('mouseenter', function () {
        const index = stars.index(this);  // Get the index of the hovered star
        stars.each(function (loopIndex) { // Loop through all stars and select up to the hovered index
            if (loopIndex <= index) {
                jQuery(this).addClass('starselected');  // Highlight the star on hover
            } else {
                jQuery(this).removeClass('starselected');
            }
        });
    });

    // Clear the hover effect when leaving the rating area
    jQuery('.rating').mouseleave(function () {
        stars.removeClass('starselected');  // Remove the hover effect when mouse leaves
    });

    // Handle click event for selecting stars
    stars.on('click', function () {
        stars.removeClass('starselected_click');  // Remove previous click selections
        const index = stars.index(this);          // Get the index of the clicked star
        stars.each(function (loopIndex) {         // Loop through and mark selected stars
            if (loopIndex <= index) {
                jQuery(this).addClass('starselected_click');  // Keep the stars selected on click
            }
        });
    });
}


/**
 * Edit Review
 *
 * Handles the functionality for editing existing reviews via an AJAX request.
 */
function wpestate_edit_review() {
    jQuery('#edit_review').on('click', function () {
        const listingId = jQuery(this).attr('data-listing_id');  // Get the listing ID
        const title = jQuery(this).parent().find('#wpestate_review_title').val();  // Review title
        const content = jQuery(this).parent().find('#wpestare_review_content').val();  // Review content
        const stars = jQuery(this).parent().find('.starselected_click').length;  // Star rating count
        const commentId = jQuery(this).attr('data-coment_id');  // Get the comment ID
        const nonce = jQuery('#wpestate_review_nonce').val();  // Security nonce
        const ajaxurl = review_vars.admin_url + 'admin-ajax.php';
        var parentContainer = jQuery(this).closest('.add_review_wrapper');

        // Check if the user selected stars and entered content
        if (stars > 0 && content !== '') {
            jQuery('.rating').text(review_vars.posting);  // Update the UI to show "posting" status

            // AJAX request to update the review
            jQuery.ajax({
                type: 'POST',
                url: ajaxurl,
                data: {
                    action: 'wpestate_edit_review',
                    listing_id: listingId,
                    title: title,
                    stars: stars,
                    content: content,
                    coment: commentId,
                    security: nonce
                },
                success: function (response) {
                    if ( response.status == 'pending' ) {
                        jQuery('.add_review_wrapper').html('<h5>' + response.message + '</h5>');  // Show pending status if review is pending
                        parentContainer.find('input, textarea').val('');  // Clear input fields
                        return;
                    }
                    jQuery('.rating').text(review_vars.review_edited);  // Update the UI to show success
                },
                error: function (errorThrown) {
                    console.error('Edit review error:', errorThrown);
                }
            });
        }
    });
}

/**
 * Submit New Review
 *
 * Handles the functionality for submitting new property reviews via an AJAX request.
 */
function wpestate_submit_review() {
    jQuery('#submit_review').on('click', function () {
        const listingId = jQuery(this).attr('data-listing_id');  // Get the listing ID
        const title = jQuery(this).parent().find('#wpestate_review_title').val();  // Review title
        const content = jQuery(this).parent().find('#wpestare_review_content').val();  // Review content
        const stars = jQuery(this).parent().find('.starselected_click').length;  // Star rating count
        const nonce = jQuery('#wpestate_review_nonce').val();  // Security nonce
        const ajaxurl = review_vars.admin_url + 'admin-ajax.php';
        var parentContainer = jQuery(this).closest('.add_review_wrapper');

        // Check if the user selected stars and entered content
        if (stars > 0 && content !== '') {
            jQuery('.rating').text(review_vars.posting);  // Update the UI to show "posting" status

            // AJAX request to submit the new review
            jQuery.ajax({
                type: 'POST',
                url: ajaxurl,
                data: {
                    action: 'wpestate_post_review',
                    listing_id: listingId,
                    title: title,
                    stars: stars,
                    content: content,
                    security: nonce
                },
                success: function (response) {
                    if (response.success === false) {
                        jQuery('.rating').text(response.data);  // Show error message if submission fails
                        return;
                    }
                    if ( response.status == 'pending' ) {
                        jQuery('.add_review_wrapper').html('<h5>' + response.message + '</h5>');  // Show pending status if review is pending
                        parentContainer.find('input, textarea').val('');  // Clear input fields
                        return;
                    }
                    jQuery('.rating').text(review_vars.review_posted);  // Update the UI to show success
                    jQuery('#wpestate_review_title, #wpestare_review_content').val('');  // Clear input fields
                },
                error: function (errorThrown) {
                    console.error('Submit review error:', errorThrown);
                }
            });
        }
    });
}


/**
 *  New review system with AJAX pagination and star rating functionality
 * This script handles the review submission, pagination, and star rating interactions using jQuery and AJAX
 * It includes features like hover effects for star ratings, form validation, and dynamic loading of reviews
 * Ensure jQuery is loaded before this script
 * This script assumes the existence of a global object `review_vars` with necessary data like
 * `ajax_url`, `nonce`, and localized messages for success/error handling
 * The HTML structure should include elements with IDs and classes as used in this script
 * */

jQuery(document).ready(function($) {
    
    // Handle pagination clicks
    jQuery(document).on('click', '.pagination-btn', function(e) {
        e.preventDefault();
        
        var button = jQuery(this);
        var page = button.data('page');
        var reviewsContainer = button.closest('.estate-reviews');
        var postId = reviewsContainer.data('post-id');
        var perPage = reviewsContainer.data('per-page');
        
        if (!page || !postId || !perPage) {
            return;
        }
        
        // Show loading state
        var originalText = button.text();
        button.prop('disabled', true).text('Loading...');
        
        // Add loading overlay
        reviewsContainer.css('position', 'relative');
        var loadingOverlay = jQuery('<div class="reviews-loading" style="position: absolute; top: 0; left: 0; right: 0; bottom: 0; background: rgba(255,255,255,0.8); display: flex; align-items: center; justify-content: center; z-index: 10;"><span>Loading reviews...</span></div>');
        reviewsContainer.append(loadingOverlay);
        
        jQuery.ajax({
            url: review_vars.ajax_url,
            type: 'POST',
            data: {
                action: 'load_reviews_page',
                nonce: review_vars.nonce,
                post_id: postId,
                page: page,
                per_page: perPage
            },
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    // Replace the entire reviews container
                    var newContent = $(response.data.html);
                    reviewsContainer.replaceWith(newContent);
                    
                    // Smooth scroll to reviews
                    $('html, body').animate({
                        scrollTop: newContent.offset().top - 20
                    }, 500);
                } else {
                    console.error('Pagination error:', response.data);
                    showMessage('Error loading reviews. Please try again.', 'error');
                }
            },
            error: function(xhr, status, error) {
                console.error('AJAX Error:', error);
                showMessage('Error loading reviews. Please try again.', 'error');
            },
            complete: function() {
                // Remove loading overlay and restore button
                loadingOverlay.remove();
                button.prop('disabled', false).text(originalText);
            }
        });
    });
});

function showMessage(message, type) {
    var messageDiv = $('#review-message');
    messageDiv
        .removeClass('estate-review-success estate-review-error')
        .addClass('estate-review-message estate-review-' + type)
        .html(message)
        .show();
    
    // Scroll to message
    $('html, body').animate({
        scrollTop: messageDiv.offset().top - 20
    }, 500);
    
    // Auto-hide success messages after 5 seconds
    if (type === 'success') {
        setTimeout(function() {
            messageDiv.fadeOut();
        }, 5000);
    }
}