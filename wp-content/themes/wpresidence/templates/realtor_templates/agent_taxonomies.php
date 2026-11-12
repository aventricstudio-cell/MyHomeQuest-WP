<?php
/** MILLDONE
 * Agent Taxonomy Display
 * src: templates\realtor_templates\agent_taxonomies.php
 * This template displays the specialties and service areas for an agent
 * by showing their associated taxonomies.
 *
 * @package WPResidence
 * @subpackage AgentProfile
 * @since 1.0.0
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

// Define the taxonomies we want to display
$agent_taxonomies = array(
    'property_county_state_agent',
    'property_city_agent',
    'property_area_agent',
    'property_category_agent',
    'property_action_category_agent'
);

// Initialize the term list
$agent_term_list = '';

// Build the term list
foreach ($agent_taxonomies as $taxonomy) {
    if (!taxonomy_exists($taxonomy)) {
        continue;
    }

    $term_list = get_the_term_list($post->ID, $taxonomy, '', '', '');

    if (!is_wp_error($term_list) && $term_list) {
        $agent_term_list .= $term_list;
    }
}

// Display the taxonomy section if terms exist
if (trim($agent_term_list) !== ''):
?>
    <div class="developer_taxonomy agent_taxonomy">
        <h4><?php esc_html_e('Specialties & Service Areas', 'wpresidence'); ?></h4>
        <?php echo wp_kses_post($agent_term_list); ?>
    </div>
<?php
endif;
?>
