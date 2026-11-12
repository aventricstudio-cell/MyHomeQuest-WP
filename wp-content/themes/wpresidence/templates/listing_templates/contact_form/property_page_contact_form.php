<?php
/** MILLDONE
 * Contact Form Template
 * src: templates\listing_templates\contact_form\property_page_contact_form.php
 * This template handles the display of the contact form on property pages and contact pages.
 * It determines whether to use a simple contact form or a Contact Form 7 integration.
 *
 * @package WpResidence
 * @subpackage PropertyDetails
 * @since 3.0.3
 *
 * Dependencies:
 * - WordPress functions: get_post_meta(), wpresidence_get_option(), get_page_template()
 * - WpResidence theme functions: wpestate_display_contact_form_title(), wpestate_display_simple_schedule_form()
 * - WPML plugin (optional): icl_translate() function
 */


 // in case we do not have a property ID like on agent contact form 
if(!isset($propertyID)) { 
    $propertyID = '';
}

// Ensure $agentID is set
if (!isset($agentID)) {
    $agentID = intval(get_post_meta($propertyID, 'property_agent', true));
}



// ensure we have agent details
if (!isset($realtor_details)) {
    $realtor_details = wpestate_return_agent_details('', $agentID);
}   


// Retrieve Contact Form 7 shortcodes from theme options
$contact_form_7_agent = wpresidence_get_option('wp_estate_contact_form_7_agent', '');
$contact_form_7_contact = wpresidence_get_option('wp_estate_contact_form_7_contact', '');

// WPML translation support
if (function_exists('icl_translate')) {
    $contact_form_7_agent = icl_translate('wpestate', 'contact_form7_agent', $contact_form_7_agent);
    $contact_form_7_contact = icl_translate('wpestate', 'contact_form7_contact', $contact_form_7_contact);
}

$current_page_template = get_page_template();

// Display section title if not in schedule section
if ($context !== 'schedule_section') :
    $title = wpestate_display_contact_form_title($current_page_template, $contact_form_7_agent);
    echo wp_kses_post(trim($title));
endif;

// Determine which form to display
$use_simple_form = (empty($contact_form_7_agent) && $current_page_template !== 'page-templates/contact_page.php') ||
                   (empty($contact_form_7_contact) && $current_page_template === 'page-templates/contact_page.php');

if ($use_simple_form) :
    ?>
    <div class="alert-message " id="alert-agent-contact"></div>
    <?php
    if ($context !== 'schedule_section') :
    
        echo wpestate_display_simple_schedule_form();
    endif;
    include(locate_template('/templates/listing_templates/contact_form/contact_form_simple.php'));
else :
    include(locate_template('/templates/listing_templates/contact_form/contact_form_via_contact7.php'));
endif;
?>