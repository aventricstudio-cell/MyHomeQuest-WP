<?php
/** MILLDONE
 * Property Contact Form Template for Contact 7 plugin
 * src: templates\listing_templates\contact_form\contact_form_via_contact7.php
 *
 * @package WpResidence
 * @subpackage PropertyDetails
 * @since 3.0.3
 */
if ((get_page_template()) == 'page-templates/contact_page.php') {
    echo do_shortcode($contact_form_7_contact);
} else {
    wp_reset_query();
    echo do_shortcode($contact_form_7_agent);
}
?>