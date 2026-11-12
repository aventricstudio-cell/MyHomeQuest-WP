<?php
/**MILLDONE
 * Agent Unit Template for Elementor - Agent card type 2
 * src: templates\agent_card_templates\agent_unit_v2.php
 * This template is used specifically for grid Elementor widgets.
 * It renders individual agent cards in a grid layout.
 *
 * @package WpResidence
 * @subpackage Templates
 * @since WpResidence 1.0
 */

// Check if agent_id is set, otherwise use current post
if(!isset($agent_id)){    
    $item_height_style = '';
    $agent_details = wpestate_return_agent_details('', $postID);
} else {
    $agent_details = wpestate_return_agent_details('', $agent_id);
}

// Set up image attributes
$extra = array(
    'class' => 'lazyload img-responsive',    
);

// Prepare inline style for background image
$inline_style = " background-image: url(" . esc_attr($agent_details['realtor_image']) . ");";

if (empty($agent_details['realtor_image'])) {
    $inline_style = " background-color: #ddd;";
}

$term_link = $agent_details['link'];
?>

<div class="<?php echo esc_attr($agent_unit_col_class['col_class']); ?> elementor_residence_grid agent_card_2  ">
    <div class="listing_wrapper elementor_places_wrapper" <?php echo esc_attr($item_height_style); ?>>
        <div class="property_listing  places_listing" data-link="<?php echo esc_attr($term_link); ?>" style="<?php echo trim($inline_style); ?>">
            <div class="places_cover agent_grid_elementor" data-link="<?php echo esc_attr($term_link); ?>"></div>
        </div>

        <h4 class="realtor_name">
            <a href="<?php echo esc_url($term_link); ?>">
                <?php echo esc_html($agent_details['realtor_name']); ?>
            </a>
        </h4>

        <div class="property_location realtor_position">
            <?php echo esc_html($agent_details['realtor_position']); ?>
        </div>

    </div>
</div>