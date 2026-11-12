<?php
/** MILLDONE
 * Agent Details Initialization
 * src: templates\realtor_templates\agentdetails.php
 * This script initializes variables and includes the appropriate agent details template
 * based on the context (modal or regular page) and the type of user associated with the property.
 *
 * @package WpResidence
 * @subpackage PropertyDetails
 * @since 3.0.3
 *
 * @global WP_Post $post Current post object.
 */

// Initialize variables
$prop_id = $propertyID = $agent_id = $agentID = 0;


// Determine if we're in a modal context
if (isset($is_modal)) {

    $prop_id = $propertyID = $post_id;
    $agent_id = $agentID = $modal_agent_id;
} else {
    // Check if we're in Elementor edit mode
    $is_elementor_edit_mode = false;
    if (did_action('plugins_loaded')) {
        $is_elementor_edit_mode = class_exists('Elementor\Plugin') && \Elementor\Plugin::$instance->editor->is_edit_mode();
    }

    // Set property ID based on context
    if ($is_elementor_edit_mode || ( isset($property_page_context) && $property_page_context=='custom_page_temaplate')  ) {
        $prop_id = $propertyID = $property_id;
    } else {
        $prop_id = $propertyID = $post->ID;
    }

    // Set agent ID
    $agent_id = $agentID = intval(get_post_meta($prop_id, 'property_agent', true));
}


// Fetch realtor details and set context
$realtor_details = wpestate_return_agent_details($prop_id);

$author_email = get_the_author_meta('user_email');
$agent_user_id = get_post_meta($agent_id, 'user_agent_id', true);
$agent_context = "property_page";

// Include appropriate template based on agent ID
if ($agent_id != 0) {
    include(locate_template('templates/realtor_templates/agentdetails.php'));
} else {
    $author_id = wpsestate_get_author($prop_id);
    if (get_the_author_meta('user_level', $author_id) != 10) {
        include(locate_template('templates/realtor_templates/agentdetails.php'));
    }
}
?>