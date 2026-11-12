<?php
/** MILLDONE
 * WpResidence Featured Submission Section
 * src: templates/submit_templates/property_featured.php
 * This file is part of the WpResidence theme and handles the display of the featured
 * submission option in the property submission form based on user membership status.
 *
 * @package WpResidence
 * @subpackage PropertySubmission
 * @since 1.0.0
 *
 * Dependencies:
 * - WpResidence theme functions (wpresidence_get_option, wpestate_get_remain_featured_listing_user)
 * - Global variables: $userID
 *
 * Usage:
 * This file should be included in the property submission form within the WpResidence theme.
 * It displays the featured submission option if the user has the appropriate membership status.
 */

// Get the paid submission status from theme options
$paid_submission_status = esc_html(wpresidence_get_option('wp_estate_paid_submission', ''));

// Check if the user has a membership that allows featured listings
if ($paid_submission_status === 'membership' && wpestate_get_remain_featured_listing_user($userID) > 0) :
?>
    <div class="submit_container">  
        <div class="submit_container_header"><?php esc_html_e('Featured Submission', 'wpresidence'); ?></div>
        <div class="user_dashboard_box">
            <p class="meta-options full_form-nob">
                <?php esc_html_e('Make this listing featured from property list.', 'wpresidence'); ?>
            </p>
        </div>
    </div>
<?php
// If paid submissions are disabled, no action is needed
elseif ($paid_submission_status === 'no') :
     echo $prop_featured_check; 
endif;
?>