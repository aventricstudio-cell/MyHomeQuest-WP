<?php
/** MILLDONE
 * src : libs\property_page_functions\property_agent_sections_functions.php
 * Property Agent Area Function
 *
 * This function generates and displays the agent area for a property listing.
 * It can be displayed either as a tab or as a standalone section.
 *
 * @package WpResidence
 * @subpackage PropertyDetails
 * @since 3.0.3
 *
 * @param int    $postID             The ID of the property post.
 * @param array  $wpestate_options   Optional. Additional options for customization.
 * @param string $is_tab             Optional. Whether to display as a tab ('yes') or not.
 * @param string $tab_active_class   Optional. The CSS class for active tabs.
 *
 * @return string|void Returns tab content if $is_tab is 'yes', otherwise echoes the content.
 */

if (!function_exists('wpestate_property_agent_area_v2')) :
    function wpestate_property_agent_area_v2($postID, $wpestate_options = '', $is_tab = '', $tab_active_class = '') {
        global $post;

        // Set context and property ID
        $context = "property_agent";
        $prop_id = $post->ID;

        // Retrieve label data for the agent area
        $data = wpestate_return_all_labels_data('agent_area');
        $label = wpestate_property_page_prepare_label($data['label_theme_option'], $data['label_default']);

        // Generate agent area content
        ob_start();
        include(locate_template('/templates/listing_templates/agent_section/agent_area.php'));
        $content = ob_get_clean();

        // Display content based on whether it's a tab or not
        if ($is_tab === 'yes') {
            return wpestate_property_page_create_tab_item($content, $label, $data['tab_id'], $tab_active_class);
        } else {
            echo (trim($content));
        }
    }
endif;



/**
 * Property Other Agents Function
 *
 * This function displays information about other agents associated with a property.
 * It can be used to create either a tab or a standalone section on a property page.
 *
 * @package WpResidence
 * @subpackage PropertyDetails
 * @since 3.0.3
 *
 * @param int    $postID             The ID of the property post.
 * @param array  $wpestate_options   Optional. Additional options for customization.
 * @param string $is_tab             Optional. Whether to display as a tab ('yes') or not.
 * @param string $tab_active_class   Optional. The CSS class for active tabs.
 *
 * @return string|void Returns tab content if $is_tab is 'yes', otherwise echoes the content.
 */

if (!function_exists('wpestate_property_other_agents_v2')):
    function wpestate_property_other_agents_v2($postID, $wpestate_options = '', $is_tab = '', $tab_active_class = '') {
        global $post;

        // Retrieve label data for other agents section
        $data = wpestate_return_all_labels_data('other_agents');
        $label = wpestate_property_page_prepare_label($data['label_theme_option'], $data['label_default']);

        // Generate content for other agents
        ob_start();
        include(locate_template('/templates/listing_templates/other_agents.php'));
        $content = ob_get_clean();

        // Display content based on whether it's a tab or not
        if ($is_tab === 'yes') {
            return wpestate_property_page_create_tab_item($content, $label, $data['tab_id'], $tab_active_class);
        } else {
            echo (trim($content));
        }
    }
endif;
