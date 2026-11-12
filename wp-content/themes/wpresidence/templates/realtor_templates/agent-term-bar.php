<?php
/** MILLDONE
 * Template part for displaying the agent term bar for section that has the listings of an agent
 * src: templates\realtor_templates\agent-term-bar.php
 * @package WPResidence
 * @subpackage AgentProfile
 * @since 1.0.0
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

$postID = $args['post_id'] ?? get_the_ID();

// Retrieve necessary data
$agent_id = get_post_meta($postID, 'user_meda_id', true) ?: '';
$prop_selection = $args['prop_selection'] ?? null;
$tab_terms = $args['tab_terms'] ?? [];

// Prepare term bar items
$term_bar_items = [];

// Add 'All' item
if ($prop_selection) {
    $term_bar_items[] = [
        'term_id' => 0,
        'term_name' => 'all',
        'label' => __('All', 'wpresidence'),
        'count' => $prop_selection->found_posts,
        'active' => true
    ];
}

// Add category items
foreach ($tab_terms as $term_id => $term_data) {
    $term_bar_items[] = [
        'term_id' => $term_id,
        'term_name' => $term_data['slug'],
        'label' => $term_data['name'],
        'count' => $term_data['count'],
        'active' => false
    ];
}

// Generate nonce 
$ajax_nonce = wp_create_nonce("wpestate_agent_listings_nonce");
?>

<input type="hidden" id="wpestate_agent_listings_nonce" value="<?php echo esc_attr($ajax_nonce); ?>" />

<div class="term_bar_wrapper agent_listings mx-0 d-flex flex-column flex-md-row" data-agent_id="<?php echo esc_attr($agent_id); ?>" data-post_id="<?php echo esc_attr($postID); ?>">
    <?php foreach ($term_bar_items as $item): ?>
        <div class="term_bar_item<?php echo $item['active'] ? ' active_term' : ''; ?>" 
             data-term_id="<?php echo esc_attr($item['term_id']); ?>" 
             data-term_name="<?php echo esc_attr($item['term_name']); ?>">
            <?php echo esc_html($item['label']); ?> (<?php echo esc_html($item['count']); ?>)
        </div>
    <?php endforeach; ?>
</div>