<?php
/** MILLDONE
 * WPResidence Theme - Places Listing Template
 * src: libs\places_categories_cards\places_categories_cards.php
 * This template is responsible for rendering individual place listings
 * in the WPResidence theme. It displays place information including
 * name, image, and listing count.
 *
 * @package WPResidence
 * @subpackage Templates
 * @since 1.0.0
 */

// Ensure $place_id is an integer
$place_id = intval($place_id);
$term = get_term($place_id);

// Initialize category data array
$category_data = array(
    'attach_id'          => '',
    'featured_image'     => '',
    'featured_image_url' => '',
    'tagline'            => '',
    'name'               => $term->name,
    'count'              => $term->count,
    'description'        => $term->description,
    'tax'                => '',
    
);


// Get term link
$term_link = get_term_link($term); 
$term_link = !is_wp_error($term_link) ? $term_link : '';

// Retrieve term meta data
$term_meta = get_option("taxonomy_$place_id");

// Process featured image
if (!empty($term_meta['category_attach_id'])) {
    $category_data['attach_id'] = $term_meta['category_attach_id'];
    $featured_image = wp_get_attachment_image_src($category_data['attach_id'], 'property_full');
    $category_data['featured_image_url'] = $featured_image[0] ?? '';
} elseif (!empty($term_meta['category_featured_image'])) {
    $category_data['featured_image_url'] = $term_meta['category_featured_image'];
}



// Get category tagline
$category_data['tagline'] = isset($term_meta['category_tagline']) ? wp_kses_post(stripslashes($term_meta['category_tagline'])) : '';



// Set default featured image if not available
if (empty($category_data['featured_image_url'])) {
    $category_data['featured_image_url'] = get_theme_file_uri('/img/defaults/default_property_listings.jpg');
}

// Prepare inline styles
$inline_style = sprintf('background-image: url(%s);', esc_url($category_data['featured_image_url']));
if (!empty($item_height)) {
    $item_height = max(100, intval($item_height)); // Ensure minimum height of 100px
    $inline_style .= sprintf('min-height:100px;height:%dpx;', $item_height);
}

// Prepare listing count text
$listing_count_text = sprintf(
    _n('%d listing', '%d listings', $category_data['count'], 'wpresidence'),
    $category_data['count']
);


?>
<div class="<?php echo esc_attr($places_class['col_class']); ?> listing_wrapper places_wrapper_type_1" 
     data-org="<?php echo esc_attr($places_class['col_org']); ?>"
     <?php echo !empty($item_height_style) ? esc_attr($item_height_style) : ''; ?>>
    <div class="property_listing places_listing places_background_image" 
         data-link="<?php echo esc_url($term_link); ?>"
         style="<?php echo esc_attr($inline_style); ?>">
        <div class="places_cover" data-link="<?php echo esc_url($term_link); ?>"></div>
        <h4>
            <a href="<?php echo esc_url($term_link); ?>">
                <?php echo esc_html(wp_trim_words($category_data['name'], 7, '...')); ?>
            </a>
        </h4>
        <div class="property_location">
            <?php echo esc_html($listing_count_text); ?>
        </div>
    </div>
</div>