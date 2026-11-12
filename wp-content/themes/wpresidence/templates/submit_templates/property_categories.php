<?php
/**MILLDONE
 * WpResidence Theme - Property Categories Template
 * src: templates/submit_templates/property_categories.php
 * This template handles the display of property categories in the property submission form.
 *
 * @package WpResidence
 * @subpackage PropertySubmission
 * @since 1.0.0
 */

// Initialize variables to avoid undefined variable warnings
$prop_category_selected = -1;
$prop_action_category_selected = -1;

// Check if form is submitted and set category values
if (isset($_POST['prop_category'])) {
    $prop_category_selected = intval($_POST['prop_category']);
}
if (isset($_POST['prop_action_category'])) {
    $prop_action_category_selected = intval($_POST['prop_action_category']);
}

// Check if we're editing an existing listing
if (isset($_GET['listing_edit']) && is_numeric($_GET['listing_edit'])) {
    $edit_id = intval($_GET['listing_edit']);
    
    // Get property category
    $prop_category = get_the_terms($edit_id, 'property_category');
    if (!empty($prop_category) && !is_wp_error($prop_category)) {
        $prop_category_selected = intval($prop_category[0]->term_id);
    }
    
    // Get property action category
    $prop_action_category = get_the_terms($edit_id, 'property_action_category');
    if (!empty($prop_action_category) && !is_wp_error($prop_action_category)) {
        $prop_action_category_selected = intval($prop_action_category[0]->term_id);
    }
}

// Check if categories should be displayed
$display_categories = is_array($wpestate_submission_page_fields) &&
    (in_array('prop_action_category', $wpestate_submission_page_fields) ||
     in_array('prop_category', $wpestate_submission_page_fields));

if ($display_categories): ?>
    <div class="profile-onprofile row">
        <div class="wpestate_dashboard_section_title"><?php esc_html_e('Select Categories', 'wpresidence'); ?></div>
        
        <?php if (is_array($wpestate_submission_page_fields) && in_array('prop_category', $wpestate_submission_page_fields)): ?>
            <p class="col-md-6">
                <label for="prop_category_submit"><?php esc_html_e('Category', 'wpresidence'); ?></label>
                <?php
                wp_dropdown_categories(array(
                    'class'             => 'select-submit2',
                    'hide_empty'        => false,
                    'selected'          => $prop_category_selected,
                    'name'              => 'prop_category',
                    'id'                => 'prop_category_submit',
                    'orderby'           => 'NAME',
                    'order'             => 'ASC',
                    'show_option_none'  => esc_html__('None', 'wpresidence'),
                    'taxonomy'          => 'property_category',
                    'hierarchical'      => true
                ));
                ?>
            </p>
        <?php endif; ?>
        
        <?php if (is_array($wpestate_submission_page_fields) && in_array('prop_action_category', $wpestate_submission_page_fields)): ?>
            <p class="col-md-6">
                <label for="prop_action_category_submit"><?php esc_html_e('Listed In', 'wpresidence'); ?></label>
                <?php
                wp_dropdown_categories(array(
                    'class'             => 'select-submit2',
                    'hide_empty'        => false,
                    'selected'          => $prop_action_category_selected,
                    'name'              => 'prop_action_category',
                    'id'                => 'prop_action_category_submit',
                    'orderby'           => 'NAME',
                    'order'             => 'ASC',
                    'show_option_none'  => esc_html__('None', 'wpresidence'),
                    'taxonomy'          => 'property_action_category',
                    'hierarchical'      => true
                ));
                ?>
            </p>
        <?php endif; ?>
    </div>
<?php endif; ?>