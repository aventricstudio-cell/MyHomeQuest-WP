<?php
/** MILLDONE
 * Related Listings Template
 * src: templates\listing_templates\property-page-templates\similar_listings.php
 * This template displays related property listings based on the current property's categories.
 * It queries for similar properties and displays them in a grid or list format.
 *
 * @package WpResidence
 * @subpackage PropertyDetails
 * @since 3.0.3
 */

// Fetch options and current post ID
$wpestate_options = get_query_var('wpestate_options');


// get Elementor widget settings
if( isset($property_id) ){
    $selectedPropertyID=$property_id;
    $similar_no = 3;	
    if (isset($attributes['post_number'])) {
        $similar_no = $attributes['post_number'];
    }
    if(isset($attributes['unit_type'])){
        $property_card_type = $attributes['unit_type'];
    }else{
        $property_card_type = intval(wpresidence_get_option('wp_estate_unit_card_type'));
    }


}else{
    // get general website settings
    $selectedPropertyID=$post->ID;
    $similar_no = wpresidence_get_option('wp_estate_similar_prop_no');
    $property_card_type = intval(wpresidence_get_option('wp_estate_unit_card_type'));
    $attributes = array();
}

$exclude = $selectedPropertyID;





// Initialize variables
$counter = 0;

$order = wpresidence_get_option('wp_estate_similar_listins_order');
$selected_categ = wpresidence_get_option('wp_estate_simialar_taxes');

// Fetch property taxonomies
$taxonomies = array(
    'property_category' => get_the_terms($selectedPropertyID, 'property_category'),
    'property_action_category' => get_the_terms($selectedPropertyID, 'property_action_category'),
    'property_city' => get_the_terms($selectedPropertyID, 'property_city'),
    'property_area' => get_the_terms($selectedPropertyID, 'property_area')
);

// Build 2026 params with taxonomy filters from the current property's terms.
$params = array(
    'posts_per_page' => $similar_no,
    'post__not_in'   => array( $exclude ),
    'order'          => $order,
    'no_found_rows'  => true,
);

foreach ( $taxonomies as $taxonomy => $terms ) {
    if ( $terms && ( empty( $selected_categ ) || in_array( $taxonomy, $selected_categ ) ) ) {
        $params[ $taxonomy ] = wp_list_pluck( $terms, 'term_id' );
    }
}

// Run the query via 2026 layer.
$result   = wpestate_query_2026_property( 'featured', $params );
$my_query = $result['query'];

if ($my_query->have_posts()) :
    $wpestate_prop_unit = esc_html(wpresidence_get_option('wp_estate_prop_unit', ''));
   
    $property_card_type_string = $property_card_type === 0 ? '' : '_type' . $property_card_type;

    // Prepare section label
    $default_label = esc_html__('Similar Listings', 'wpresidence');
    $label = wpestate_property_page_prepare_label('wp_estate_property_similart_listings_text', $default_label);

    if (isset($attributes['section_title']) && $attributes['section_title'] !== '') {
        $label = $attributes['section_title'];
    }


    ?>

    <div class="wpresidence_realtor_listings_wrapper row" id="property_similar_listings">
        <?php if ($is_tab !== 'yes') : ?>
            <h3 class="agent_listings_title_similar"><?php echo esc_html($label); ?></h3>
        <?php endif; ?>

        <?php
        // Set global variables for list display
        if (wpresidence_get_option('wp_estate_unit_md_similar') === "list") {
            global $is_shortcode, $row_number_col;
            $is_shortcode = 1;
            $row_number_col = 12;
        }

        // Display property listings
        wpresidence_display_property_list_as_html($my_query, $wpestate_options, 'related_listings',$attributes);
        ?>
    </div>

    <?php
    // Add to sticky menu array
    $sticky_menu_array['property_similar_listings listings'] = esc_html__('Similar Listings', 'wpresidence');
endif;


?>