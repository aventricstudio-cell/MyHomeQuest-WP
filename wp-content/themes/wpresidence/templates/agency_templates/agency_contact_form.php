<?php
/** MILLDONE
 * Display agency contact information and contact form
 * src: templates\agency_templates\agency_contact_form.php
 * This template part is responsible for showing agency details and a contact form
 * on the agency page in the WPResidence theme.
 *
 * @package WPResidence
 * @subpackage Agency
 * @since 1.0.0
 */

// set the agentID for contact forms
$agentID= $post->ID;


// Fetch and sanitize agency contact details
$agency_details = array(
    'address' => get_post_meta($post->ID, 'agency_address', true),
    'skype'   => get_post_meta($post->ID, 'agency_skype', true),
    'phone'   => get_post_meta($post->ID, 'agency_phone', true),
    'mobile'  => get_post_meta($post->ID, 'agency_mobile', true),
    'email'   => get_post_meta($post->ID, 'agency_email', true)
);

// Sanitize and validate email
$agency_details['email'] = is_email($agency_details['email']) ? $agency_details['email'] : '';

// Start output buffering to capture HTML
ob_start();
?>
<div class="agency_contact_container">
    <div class="agency_contact_wrapper flex-column flex-md-row">
        <div class="col-md-8" id="agency_contact">
            <div class="agent_contanct_form wpestate_contact_form_parent">
                <?php
                $context = 'agency_page';
                include(locate_template('/templates/listing_templates/contact_form/property_page_contact_form.php'));
                ?>
            </div>
        </div>
        <div class="col-md-4 agency_contact_padding">
            <?php
            // Array of agency details to display
            $display_details = array(
                'address' => __('Address:', 'wpresidence'),
                'email'   => __('Email:', 'wpresidence'),
                'mobile'  => __('Mobile:', 'wpresidence'),
                'phone'   => __('Phone:', 'wpresidence'),
                'skype'   => __('Skype:', 'wpresidence')
            );

            foreach ($display_details as $key => $label) {
                if (!empty($agency_details[$key])) {
                    echo '<div class="agency_detail agency_' . esc_attr($key) . '">';
                    echo '<strong>' . esc_html($label) . '</strong> ';
                    
                    if ($key === 'email' || $key === 'mobile' || $key === 'phone') {
                        $href = ($key === 'email') ? 'mailto:' : 'tel:';
                        echo '<a href="' . esc_attr($href .antispambot( $agency_details[$key])) . '">';
                        echo esc_html( antispambot($agency_details[$key]) );
                        echo '</a>';
                    } else {
                        echo esc_html($agency_details[$key]);
                    }
                    
                    echo '</div>';
                }
            }
            ?>
        </div>
    </div>
</div>
<?php
// End output buffering and echo the captured HTML
echo ob_get_clean();
?>