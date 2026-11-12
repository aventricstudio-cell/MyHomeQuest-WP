<?php
/** MILLDONE
 * Other Agents Display Template
 * src: templates\listing_templates\other_agents.php
 * This template displays information about secondary agents associated with a property.
 * It's used within the wpestate_property_other_agents_v2 function.
 *
 * @package WpResidence
 * @subpackage PropertyDetails
 * @since 3.0.3
 *
 * @global WP_Post $post Current post object.
 */



if (!isset($property_page_context) || $property_page_context!=='custom_page_temaplate'){
    $propertyID=$post->ID;
}

// Retrieve secondary agents associated with the property
$agents_secondary = get_post_meta($propertyID, 'property_agent_secondary', true);

// Check if there are secondary agents to display
if (is_array($agents_secondary) && !empty($agents_secondary) && $agents_secondary[0] !== '') {
    // Add to sticky menu array
    $sticky_menu_array['property_other_agents'] = esc_html__('Other Agents', 'wpresidence');
    
    // Start output buffer to capture HTML
    ob_start();
    ?>
    <div class="wpresidence_realtor_listings_wrapper row" id="property_other_agents">
        <?php if ($is_tab !== 'yes') : ?>
            <h3 class="agent_listings_title_similar">
                <?php echo esc_html($label); ?>
            </h3>
        <?php endif; ?>
        
        <?php
        // Prepare and execute query for secondary agents
        $agents_sec_list = implode(',', $agents_secondary);
        $args = array(
            'post_type'      => 'estate_agent',
            'posts_per_page' => -1,
            'post__in'       => $agents_secondary
        );
        $agent_selection = new WP_Query($args);

        // Display agent list
        wpresidence_display_agent_list_as_html($agent_selection, 'estate_agent', $wpestate_options, 'shortcode');
        

        ?>
    </div>
    <?php
    // Output the captured HTML
    echo ob_get_clean();
}
?>