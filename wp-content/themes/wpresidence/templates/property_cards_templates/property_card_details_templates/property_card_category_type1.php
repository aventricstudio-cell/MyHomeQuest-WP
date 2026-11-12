<?php
/**
 * Template for displaying property category and action on Type 1 property cards
 * src: templates\property_cards_templates\property_card_details_templates\property_card_category_type1.php
 * This template is responsible for rendering the property category and action information
 * on Type 1 property cards. It displays the property's category followed by the action category.
 * Modified to use wpestate_return_data_from_cache_if_exists for improved performance and fallback capability.
 */
?>
<div class="property_categories_type1_wrapper">
    <?php
    // Get property category using cache function with fallback
    $property_category_terms = wpestate_return_data_from_cache_if_exists($property_unit_cached_data, $postID, 'terms', 'property_category');
    $property_category = '';
    if (!empty($property_category_terms) && is_array($property_category_terms)) {
        $categories = array();
        foreach ($property_category_terms as $category) {
            $categories[] = $category['name'];
        }
        $property_category = implode(', ', $categories);
    }
   
    // Get property action using cache function with fallback
    $property_action_terms = wpestate_return_data_from_cache_if_exists($property_unit_cached_data, $postID, 'terms', 'property_action_category');
    $property_action = '';
    if (!empty($property_action_terms) && is_array($property_action_terms)) {
        $actions = array();
        foreach ($property_action_terms as $action) {
            $actions[] = $action['name'];
        }
        $property_action = implode(', ', $actions);
    }
   
    // Display property category and action if available
    if (!empty($property_category) || !empty($property_action)) {
        if (!empty($property_category)) {
            echo $property_category;
           
            if (!empty($property_action)) {
                echo ' ' . esc_html__('in', 'wpresidence') . ' ';
            }
        }
       
        if (!empty($property_action)) {
            echo $property_action;
        }
    } else {
        echo '&nbsp;'; // Output a non-breaking space if no category or action is available
    }
    ?>
</div>