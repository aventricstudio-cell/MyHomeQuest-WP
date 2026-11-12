<?php
/** MILLDONE
 * WpResidence Virtual Tour Section
 * src: templates/submit_templates/virtual_tour.php
 * This file is part of the WpResidence theme and handles the display and submission
 * of virtual tour information for property listings.
 *
 * @package WpResidence
 * @subpackage PropertySubmission
 * @since 1.0.0
 *
 * Dependencies:
 * - WordPress core functions (get_post_meta)
 * - Global variables: $wpestate_submission_page_fields
 *
 * Usage:
 * This file should be included in the property submission or edit form within the WpResidence theme.
 * It displays a textarea for entering virtual tour embed code.
 */

// Define allowed HTML tags and attributes for iframe
$iframe = array(
    'iframe' => array(
        'src'             => array(),
        'width'           => array(),
        'height'          => array(),
        'name'            => array(),
        'frameborder'     => array(),
        'style'           => array(),
        'allowFullScreen' => array(),
    )
);

// Initialize virtual tour variable
$virtual_tour = '';

// Handle property edit scenario
if (isset($_GET['listing_edit']) && is_numeric($_GET['listing_edit'])) {
    $edit_id = intval($_GET['listing_edit']);
    $virtual_tour = get_post_meta($edit_id, 'embed_virtual_tour', true);
}

// Handle form submission
if (isset($_POST['embed_virtual_tour'])) {
    $virtual_tour = wp_kses(
        trim(str_replace("'", '"', $_POST['embed_virtual_tour'])),
        $iframe
    );
}

// Check if virtual tour field should be displayed
if (is_array($wpestate_submission_page_fields) && in_array('embed_virtual_tour', $wpestate_submission_page_fields, true)) :
?>
    <div class="profile-onprofile row">
        <div class="wpestate_dashboard_section_title"><?php esc_html_e('Virtual Tour', 'wpresidence'); ?></div>
        <div class="col-md-12">
            <label for="embed_virtual_tour"><?php esc_html_e('Virtual Tour / Meta Reels:', 'wpresidence'); ?></label>
            <textarea id="embed_virtual_tour" class="form-control" name="embed_virtual_tour"><?php echo wp_kses($virtual_tour, $iframe); ?></textarea>
        </div>
    </div>
<?php
endif;
?>